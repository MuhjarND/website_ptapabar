<?php

namespace App\Http\Controllers\Admin;

use App\CaseStatistic;
use App\CourtHearing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourtInformationController extends Controller
{
    public function index($type)
    {
        $rows = $type === 'hearings'
            ? CourtHearing::orderByDesc('hearing_date')->orderBy('hearing_time')->paginate(20)
            : CaseStatistic::orderByDesc('period')->paginate(20);
        return view('admin.court-information.index', compact('type', 'rows'));
    }

    public function form($type, $entry = null)
    {
        $record = $this->record($type, $entry);
        return view('admin.court-information.form', compact('type', 'record'));
    }

    public function save(Request $request, $type, $entry = null)
    {
        $record = $this->record($type, $entry);
        if ($type === 'hearings') {
            $data = $request->validate([
                'hearing_date' => 'required|date_format:Y-m-d',
                'hearing_time' => 'nullable|date_format:H:i',
                'title' => 'required|string|max:160',
                'location' => 'nullable|string|max:160',
            ]);
        } else {
            $data = $request->validate([
                'month' => 'required|date_format:Y-m',
                'opening' => 'required|integer|min:0|max:2147483647',
                'received' => 'required|integer|min:0|max:2147483647',
                'decided' => 'required|integer|min:0|max:2147483647',
                'closing' => 'required|integer|min:0|max:2147483647',
                'source' => 'required|string|max:160',
            ]);
            $data['period'] = $data['month'] . '-01';
            validator(['month' => $data['period']], [
                'month' => [Rule::unique('case_statistics', 'period')->ignore($record->id)],
            ])->validate();
            unset($data['month']);
        }
        $request->validate(['is_published' => 'nullable|boolean']);
        $record->fill($data + ['is_published' => $request->boolean('is_published')])->save();

        return redirect()->route('admin.court-information.index', $type)->with('success', 'Data berhasil disimpan.');
    }

    private function record($type, $entry)
    {
        $model = $type === 'hearings' ? new CourtHearing : new CaseStatistic;
        return $entry ? $model->newQuery()->findOrFail($entry) : $model;
    }
}
