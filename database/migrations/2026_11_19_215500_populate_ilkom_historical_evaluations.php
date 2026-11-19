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

        // Get template jadwals
        $ganjilTemplate = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', '2025 / 2026 GANJIL REGULER')
            ->get();

        $genapTemplate = Jadwal::where('prodi_id', $ilkom->id)
            ->where('periode', '2025 / 2026 GENAP REGULER')
            ->get();

        if ($ganjilTemplate->isEmpty() && $genapTemplate->isEmpty()) {
            return;
        }

        // Pool of evaluations from other prodis in 2025 / 2026 GENAP REGULER
        $pool = Evaluation::whereHas('jadwal', function($q) use ($ilkom) {
            $q->where('prodi_id', '!=', $ilkom->id)
              ->where('periode', '2025 / 2026 GENAP REGULER');
        })->get();

        if ($pool->isEmpty()) {
            // Fallback pool from current evaluations
            $pool = Evaluation::whereNotNull('answers')->get();
            if ($pool->isEmpty()) {
                return;
            }
        }

        $targets = [
            [
                'name' => '2023 / 2024 GANJIL REGULER',
                'type' => 'GANJIL',
                'date_start' => '2024-01-15 08:00:00',
                'date_end'   => '2024-01-28 17:00:00',
            ],
            [
                'name' => '2023 / 2024 GENAP REGULER',
                'type' => 'GENAP',
                'date_start' => '2024-06-17 08:00:00',
                'date_end'   => '2024-06-30 17:00:00',
            ],
            [
                'name' => '2024 / 2025 GANJIL REGULER',
                'type' => 'GANJIL',
                'date_start' => '2025-01-13 08:00:00',
                'date_end'   => '2025-01-26 17:00:00',
            ],
            [
                'name' => '2025 / 2026 GANJIL REGULER',
                'type' => 'GANJIL',
                'date_start' => '2026-01-12 08:00:00',
                'date_end'   => '2026-01-25 17:00:00',
            ],
        ];

        foreach ($targets as $target) {
            Periode::firstOrCreate(['name' => $target['name']], ['is_active' => false]);

            $templates = ($target['type'] === 'GANJIL') ? $ganjilTemplate : $genapTemplate;
            if ($templates->isEmpty()) {
                continue;
            }

            $startTs = strtotime($target['date_start']);
            $endTs   = strtotime($target['date_end']);

            foreach ($templates as $tmpl) {
                $jadwal = Jadwal::firstOrCreate([
                    'dosen_id'       => $tmpl->dosen_id,
                    'mata_kuliah_id' => $tmpl->mata_kuliah_id,
                    'prodi_id'       => $ilkom->id,
                    'periode'        => $target['name'],
                ]);

                $existing = Evaluation::where('jadwal_id', $jadwal->id)->count();
                if ($existing === 0) {
                    $numRespondents = mt_rand(2, 4);
                    $sampled = $pool->random(min($numRespondents, $pool->count()));

                    foreach ($sampled as $sampleItem) {
                        $randTs = mt_rand($startTs, $endTs);
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

        $targets = [
            '2023 / 2024 GANJIL REGULER',
            '2023 / 2024 GENAP REGULER',
            '2024 / 2025 GANJIL REGULER',
            '2025 / 2026 GANJIL REGULER',
        ];

        foreach ($targets as $targetName) {
            $jadwalIds = Jadwal::where('prodi_id', $ilkom->id)
                ->where('periode', $targetName)
                ->pluck('id');

            Evaluation::whereIn('jadwal_id', $jadwalIds)->delete();
            if ($targetName !== '2025 / 2026 GANJIL REGULER') {
                Jadwal::whereIn('id', $jadwalIds)->delete();
                Periode::where('name', $targetName)->delete();
            }
        }
    }
};
