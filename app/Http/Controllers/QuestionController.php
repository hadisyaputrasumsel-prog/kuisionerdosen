<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = \App\Models\Question::orderBy('order_num')->get();
        // Group by section for the view
        $groupedQuestions = $questions->groupBy('section');
        return view('admin.questions', compact('questions', 'groupedQuestions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string',
            'question_text' => 'required|string',
            'order_num' => 'required|integer',
            'is_active' => 'nullable'
        ]);

        $validated['is_active'] = $request->has('is_active');
        \App\Models\Question::create($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $question = \App\Models\Question::findOrFail($id);
        
        $validated = $request->validate([
            'section' => 'required|string',
            'question_text' => 'required|string',
            'order_num' => 'required|integer',
            'is_active' => 'nullable'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $question = \App\Models\Question::findOrFail($id);
        $question->delete();
        
        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
