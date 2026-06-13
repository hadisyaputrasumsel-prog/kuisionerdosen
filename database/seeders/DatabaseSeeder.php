<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $prodi1 = \App\Models\Prodi::create(['name' => 'Ilmu Komunikasi']);
        $prodi2 = \App\Models\Prodi::create(['name' => 'Manajemen']);
        $prodi3 = \App\Models\Prodi::create(['name' => 'Ilmu Komputer']);

        $dosen1 = \App\Models\Dosen::create(['name' => 'Dr. Hadi Syaputra, S.Kom., M.Kom.']);
        $dosen2 = \App\Models\Dosen::create(['name' => 'Intan Putri, S.I.Kom,. M.I.Kom']);
        $dosen3 = \App\Models\Dosen::create(['name' => 'Rikkie Dekas, S.E., M.M.']);

        $mk1 = \App\Models\MataKuliah::create(['name' => 'PENGOLAHAN CITRA', 'code' => 'IKM244321']);
        $mk2 = \App\Models\MataKuliah::create(['name' => 'KOMUNIKASI MASSA', 'code' => 'KOM101']);
        $mk3 = \App\Models\MataKuliah::create(['name' => 'PENGANGGARAN PERUSAHAAN', 'code' => 'MAN201']);

        \App\Models\Jadwal::create(['prodi_id' => $prodi3->id, 'dosen_id' => $dosen1->id, 'mata_kuliah_id' => $mk1->id]);
        \App\Models\Jadwal::create(['prodi_id' => $prodi1->id, 'dosen_id' => $dosen2->id, 'mata_kuliah_id' => $mk2->id]);
        \App\Models\Jadwal::create(['prodi_id' => $prodi2->id, 'dosen_id' => $dosen3->id, 'mata_kuliah_id' => $mk3->id]);
    }
}
