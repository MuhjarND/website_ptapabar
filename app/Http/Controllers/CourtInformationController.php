<?php

namespace App\Http\Controllers;

use App\CaseStatistic;
use App\CourtHearing;
use App\Support\PublicNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CourtInformationController extends Controller
{
    public function hearings(Request $request)
    {
        $date = $request->query('tanggal');
        if (Validator::make(['tanggal' => $date], ['tanggal' => 'required|date_format:Y-m-d'])->fails()) {
            $date = now('Asia/Jayapura')->toDateString();
        }
        $rows = Schema::hasTable('court_hearings') ? CourtHearing::where('is_published', true)
            ->where('hearing_date', $date)->orderBy('hearing_time')->orderBy('id')->get() : collect();

        return view('public.court-information', ['type' => 'hearings', 'date' => $date, 'rows' => $rows]);
    }

    public function statistics(Request $request)
    {
        $defaultYear = now('Asia/Jayapura')->year;
        $year = filter_var($request->query('tahun'), FILTER_VALIDATE_INT, ['options' => [
            'min_range' => 1900, 'max_range' => 2100,
        ]]) ?: $defaultYear;
        $rows = Schema::hasTable('case_statistics') ? CaseStatistic::where('is_published', true)
            ->whereBetween('period', [$year . '-01-01', $year . '-12-31'])->orderByDesc('period')->get() : collect();

        return view('public.court-information', ['type' => 'statistics', 'year' => $year, 'rows' => $rows]);
    }

    public function verdicts()
    {
        return view('public.court-information', ['type' => 'verdicts', 'verdictsUrl' => PublicNavigation::verdictsUrl()]);
    }
}
