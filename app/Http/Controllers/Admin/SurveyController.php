<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SurveyIndex;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = SurveyIndex::orderBy('order')->get();
        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'value' => 'required|numeric',
            'label' => 'required|string|max:255',
        ]);

        SurveyIndex::create([
            'title' => $request->title,
            'icon' => $request->icon ?: 'fas fa-chart-bar',
            'value' => $request->value,
            'label' => $request->label,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.surveys.index')->with('success', 'Indeks survei berhasil ditambahkan.');
    }

    public function edit(SurveyIndex $survey)
    {
        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, SurveyIndex $survey)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'value' => 'required|numeric',
            'label' => 'required|string|max:255',
        ]);

        $survey->update([
            'title' => $request->title,
            'icon' => $request->icon ?: 'fas fa-chart-bar',
            'value' => $request->value,
            'label' => $request->label,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.surveys.index')->with('success', 'Indeks survei berhasil diperbarui.');
    }

    public function destroy(SurveyIndex $survey)
    {
        $survey->delete();
        return redirect()->route('admin.surveys.index')->with('success', 'Indeks survei berhasil dihapus.');
    }
}
