<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan dan gabungkan duplikat PRODI
        $allProdis = DB::table('prodis')->get();
        $prodiGroups = [];
        foreach ($allProdis as $p) {
            $key = strtoupper(trim($p->name));
            $prodiGroups[$key][] = $p;
        }

        foreach ($prodiGroups as $normalizedName => $items) {
            // Tentukan kanonikal: yang ID terkecil, utamakan yang sudah uppercase jika ada
            usort($items, function ($a, $b) {
                $aUpper = ($a->name === strtoupper(trim($a->name))) ? 0 : 1;
                $bUpper = ($b->name === strtoupper(trim($b->name))) ? 0 : 1;
                if ($aUpper !== $bUpper) {
                    return $aUpper - $bUpper;
                }
                return $a->id - $b->id;
            });

            $canonical = $items[0];

            // Pastikan nama kanonikal seragam UPPERCASE
            DB::table('prodis')->where('id', $canonical->id)->update([
                'name' => $normalizedName
            ]);

            // Untuk duplikat lainnya, alihkan relasi jadwal lalu hapus duplikat
            for ($i = 1; $i < count($items); $i++) {
                $dup = $items[$i];
                DB::table('jadwals')->where('prodi_id', $dup->id)->update([
                    'prodi_id' => $canonical->id
                ]);
                DB::table('prodis')->where('id', $dup->id)->delete();
            }
        }

        // 2. Bersihkan dan gabungkan duplikat DOSEN jika ada
        $allDosens = DB::table('dosens')->get();
        $dosenGroups = [];
        foreach ($allDosens as $d) {
            $key = trim(preg_replace('/\s+/', ' ', strtolower($d->name)));
            $dosenGroups[$key][] = $d;
        }

        foreach ($dosenGroups as $key => $items) {
            if (count($items) > 1) {
                usort($items, function ($a, $b) {
                    return $a->id - $b->id;
                });
                $canonical = $items[0];

                for ($i = 1; $i < count($items); $i++) {
                    $dup = $items[$i];
                    DB::table('jadwals')->where('dosen_id', $dup->id)->update([
                        'dosen_id' => $canonical->id
                    ]);
                    DB::table('dosens')->where('id', $dup->id)->delete();
                }
            }
        }

        // 3. Bersihkan dan gabungkan duplikat JADWAL yang identik (prodi, dosen, matakuliah, periode sama)
        $allJadwals = DB::table('jadwals')->get();
        $jadwalGroups = [];
        foreach ($allJadwals as $j) {
            $jKey = "{$j->prodi_id}_{$j->dosen_id}_{$j->mata_kuliah_id}_{$j->periode}";
            $jadwalGroups[$jKey][] = $j;
        }

        foreach ($jadwalGroups as $jKey => $items) {
            if (count($items) > 1) {
                usort($items, function ($a, $b) {
                    $cntA = DB::table('evaluations')->where('jadwal_id', $a->id)->count();
                    $cntB = DB::table('evaluations')->where('jadwal_id', $b->id)->count();
                    if ($cntA !== $cntB) {
                        return $cntB - $cntA; // jadwal dengan evaluasi lebih banyak di urutan pertama
                    }
                    return $a->id - $b->id;
                });

                $canonicalJadwal = $items[0];

                for ($i = 1; $i < count($items); $i++) {
                    $dupJadwal = $items[$i];
                    // Pindahkan evaluasi ke jadwal kanonikal
                    DB::table('evaluations')->where('jadwal_id', $dupJadwal->id)->update([
                        'jadwal_id' => $canonicalJadwal->id
                    ]);
                    DB::table('jadwals')->where('id', $dupJadwal->id)->delete();
                }
            }
        }

        // 4. Tambahkan index UNIQUE pada prodis.name jika didukung
        try {
            Schema::table('prodis', function (Blueprint $table) {
                $table->unique('name');
            });
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau collation tidak mengizinkan
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('prodis', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });
        } catch (\Throwable $e) {
        }
    }
};
