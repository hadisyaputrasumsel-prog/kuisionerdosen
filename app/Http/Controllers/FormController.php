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
        $activePeriodes = \App\Models\Periode::where('is_active', true)->pluck('name')->toArray();
        $periodeLabel = count($activePeriodes) > 0 ? implode(', ', $activePeriodes) : 'Belum Ditentukan';
        $periodeLabel = trim(str_ireplace('REGULER', '', $periodeLabel));

        $prodis = Prodi::orderBy('name')->get();
        $questions = \App\Models\Question::where('is_active', true)->orderBy('order_num')->get();
        $groupedQuestions = $questions->groupBy('section');
        return view('form', compact('prodis', 'groupedQuestions', 'periodeLabel'));
    }

    public function getJadwals($prodi_id)
    {
        $activePeriodes = \App\Models\Periode::where('is_active', true)->pluck('name')->toArray();

        $jadwals = Jadwal::with(['dosen', 'mataKuliah'])
            ->where('prodi_id', $prodi_id)
            ->when(count($activePeriodes) > 0, function($q) use ($activePeriodes) {
                return $q->whereIn('periode', $activePeriodes);
            })
            ->get()
            ->sortBy(function($jadwal) {
                return $jadwal->dosen->name ?? '';
            })
            ->values()
            ->map(function ($jadwal) {
                $mkName = $jadwal->mataKuliah->name;
                if (!empty($jadwal->mataKuliah->code) && $jadwal->mataKuliah->code !== 'N/A') {
                    $mkName .= ' (' . $jadwal->mataKuliah->code . ')';
                }
                
                return [
                    'id' => $jadwal->id,
                    'dosen' => $jadwal->dosen->name,
                    'matakuliah' => $mkName
                ];
            });

        return response()->json($jadwals);
    }

    public function submit(Request $request)
    {
        $activeQuestions = \App\Models\Question::where('is_active', true)->get();
        $rules = [
            'jadwal_id' => 'required|exists:jadwals,id',
            'saran' => 'nullable|string'
        ];
        
        foreach ($activeQuestions as $q) {
            $rules['q_' . $q->id] = 'required|integer|min:1|max:5';
        }

        $validated = $request->validate($rules);

        $answers = [];
        foreach ($activeQuestions as $q) {
            $answers['q_' . $q->id] = $validated['q_' . $q->id];
        }

        Evaluation::create([
            'jadwal_id' => $validated['jadwal_id'],
            'answers' => $answers,
            'saran' => $validated['saran'] ?? null
        ]);

        return redirect()->route('form.success')->with('success', 'Kuisioner berhasil disubmit. Terima kasih!');
    }
}
