<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Jadwal;
use App\Models\Evaluation;

class FormController extends Controller
{
    public function index()
    {
        $prodis = Prodi::orderBy('name')->get();
        return view('form', compact('prodis'));
    }

    public function getJadwals($prodi_id)
    {
        $jadwals = Jadwal::with(['dosen', 'mataKuliah'])
            ->where('prodi_id', $prodi_id)
            ->get()
            ->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'dosen' => $jadwal->dosen->name,
                    'matakuliah' => $jadwal->mataKuliah->name . ' (' . $jadwal->mataKuliah->code . ')'
                ];
            });

        return response()->json($jadwals);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'q1' => 'required|integer|min:1|max:5',
            'q2' => 'required|integer|min:1|max:5',
            'q3' => 'required|integer|min:1|max:5',
            'q4' => 'required|integer|min:1|max:5',
            'saran' => 'nullable|string'
        ]);

        Evaluation::create($request->all());

        return redirect()->route('form.success')->with('success', 'Kuisioner berhasil disubmit. Terima kasih!');
    }
}
