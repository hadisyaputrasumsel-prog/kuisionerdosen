<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan Dosen Dr. Hadi Syaputra, S.Kom., M.Kom ada
        $drDosen = DB::table('dosens')->where('id', 33)->first();
        if (!$drDosen) {
            $drDosen = DB::table('dosens')->where('name', 'LIKE', '%Dr%Hadi Syaputra%')->first();
        }
        if ($drDosen) {
            DB::table('dosens')->where('id', $drDosen->id)->update([
                'name' => 'Dr. Hadi Syaputra, S.Kom., M.Kom'
            ]);
        } else {
            $drId = DB::table('dosens')->insertGetId([
                'name' => 'Dr. Hadi Syaputra, S.Kom., M.Kom',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $drDosen = DB::table('dosens')->where('id', $drId)->first();
        }

        // 2. Pastikan Dosen Hadi Syaputra, S.Kom., M.Kom (belum bergelar Dr) ada
        $nonDrDosen = DB::table('dosens')->where('id', 60)->first();
        if (!$nonDrDosen) {
            $nonDrDosen = DB::table('dosens')
                ->where('name', 'LIKE', '%Hadi Syaputra%')
                ->where('name', 'NOT LIKE', '%Dr%')
                ->first();
        }
        if ($nonDrDosen) {
            DB::table('dosens')->where('id', $nonDrDosen->id)->update([
                'name' => 'Hadi Syaputra, S.Kom., M.Kom'
            ]);
        } else {
            $nonDrId = DB::table('dosens')->insertGetId([
                'name' => 'Hadi Syaputra, S.Kom., M.Kom',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $nonDrDosen = DB::table('dosens')->where('id', $nonDrId)->first();
        }

        // 3. Update relasi dosen_id pada jadwals sesuai periode:
        // - Periode 2025/2026 GENAP REGULER ke atas: Dr. Hadi Syaputra, S.Kom., M.Kom
        // - Periode sebelumnya (2023 Ganjil/Genap, 2024 Ganjil/Genap, 2025 Ganjil): Hadi Syaputra, S.Kom., M.Kom
        $hadiIds = DB::table('dosens')->where('name', 'LIKE', '%Hadi Syaputra%')->pluck('id');

        $jadwals = DB::table('jadwals')->whereIn('dosen_id', $hadiIds)->get();
        foreach ($jadwals as $j) {
            $periode = $j->periode;
            if (str_contains($periode, '2025') && str_contains($periode, '2026') && stripos($periode, 'GENAP') !== false) {
                DB::table('jadwals')->where('id', $j->id)->update(['dosen_id' => $drDosen->id]);
            } else {
                DB::table('jadwals')->where('id', $j->id)->update(['dosen_id' => $nonDrDosen->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu di-rollback
    }
};
