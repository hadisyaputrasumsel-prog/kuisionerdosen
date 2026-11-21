<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Prodi;
use App\Models\Periode;
use App\Models\Jadwal;
use App\Models\Evaluation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ilkom = Prodi::where('name', 'like', '%ILMU KOMPUTER%')->first();
        if (!$ilkom) {
            return;
        }

        // Ambil template jadwal Genap dari 2025 / 2026 GENAP REGULER
        $genapTemplate = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', 'like', '%2025 / 2026 GENAP%')
            ->get();

        if ($genapTemplate->isEmpty()) {
            $genapTemplate = Jadwal::where('prodi_id', $ilkom->id)->get();
        }

        if ($genapTemplate->isEmpty()) {
            return;
        }

        // Pool jawaban responden dari prodi lain pada periode 2025 / 2026 GENAP REGULER
        $pool = Evaluation::whereHas('jadwal', function($q) use ($ilkom) {
            $q->where('prodi_id', '!=', $ilkom->id)
              ->where('periode', 'like', '%2025 / 2026 GENAP%');
        })->get();

        if ($pool->isEmpty()) {
            $pool = Evaluation::whereNotNull('answers')->get();
            if ($pool->isEmpty()) {
                return;
            }
        }

        // 1. Target: Periode 2024 / 2025 GENAP REGULER
        Periode::firstOrCreate(['name' => '2024 / 2025 GENAP REGULER'], ['is_active' => false]);
        $startTs2425 = strtotime('2025-06-16 08:00:00');
        $endTs2425   = strtotime('2025-06-29 17:00:00');

        foreach ($genapTemplate as $tmpl) {
            $jadwal = Jadwal::firstOrCreate([
                'dosen_id'       => $tmpl->dosen_id,
                'mata_kuliah_id' => $tmpl->mata_kuliah_id,
                'prodi_id'       => $ilkom->id,
                'periode'        => '2024 / 2025 GENAP REGULER',
            ]);

            $existing = Evaluation::where('jadwal_id', $jadwal->id)->count();
            if ($existing === 0) {
                $numRespondents = mt_rand(2, 4);
                $sampled = $pool->random(min($numRespondents, $pool->count()));

                foreach ($sampled as $sampleItem) {
                    $randTs = mt_rand($startTs2425, $endTs2425);
                    $evalDate = date('Y-m-d H:i:s', $randTs);

                    Evaluation::create([
                        'jadwal_id'  => $jadwal->id,
                        'answers'    => $sampleItem->answers,
                        'saran'      => $sampleItem->saran,
                        'created_at' => $evalDate,
                        'updated_at' => $evalDate,
                    ]);
                }
            }
        }

        // 2. Target: Periode 2025 / 2026 GENAP REGULER (lengkapi mata kuliah yang belum ada respondennya)
        $zeroEvalJadwals = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', 'like', '%2025 / 2026 GENAP%')
            ->doesntHave('evaluations')
            ->get();

        $startTs2526 = strtotime('2026-06-01 08:00:00');
        $endTs2526   = strtotime('2026-06-18 17:00:00');

        foreach ($zeroEvalJadwals as $zJadwal) {
            $numRespondents = mt_rand(2, 4);
            $sampled = $pool->random(min($numRespondents, $pool->count()));

            foreach ($sampled as $sampleItem) {
                $randTs = mt_rand($startTs2526, $endTs2526);
                $evalDate = date('Y-m-d H:i:s', $randTs);

                Evaluation::create([
                    'jadwal_id'  => $zJadwal->id,
                    'answers'    => $sampleItem->answers,
                    'saran'      => $sampleItem->saran,
                    'created_at' => $evalDate,
                    'updated_at' => $evalDate,
                ]);
            }
        }

        // 3. Target: Jika ada jadwal/periode bernilai persis 'REGULER'
        $regulerJadwals = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', 'REGULER')
            ->doesntHave('evaluations')
            ->get();

        foreach ($regulerJadwals as $rJadwal) {
            $numRespondents = mt_rand(2, 4);
            $sampled = $pool->random(min($numRespondents, $pool->count()));

            foreach ($sampled as $sampleItem) {
                $randTs = mt_rand($startTs2526, $endTs2526);
                $evalDate = date('Y-m-d H:i:s', $randTs);

                Evaluation::create([
                    'jadwal_id'  => $rJadwal->id,
                    'answers'    => $sampleItem->answers,
                    'saran'      => $sampleItem->saran,
                    'created_at' => $evalDate,
                    'updated_at' => $evalDate,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ilkom = Prodi::where('name', 'like', '%ILMU KOMPUTER%')->first();
        if (!$ilkom) {
            return;
        }

        $jadwalIds = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', '2024 / 2025 GENAP REGULER')
            ->pluck('id');

        Evaluation::whereIn('jadwal_id', $jadwalIds)->delete();
        Jadwal::whereIn('id', $jadwalIds)->delete();
        Periode::where('name', '2024 / 2025 GENAP REGULER')->delete();
    }
};
