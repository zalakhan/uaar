<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchDegreeVerificationRequest;
use App\Models\DegreeVerification;
use Illuminate\View\View;

class DegreeVerificationController extends Controller
{
    public function index(): View
    {
        return $this->verificationView(
            results: null,
            searched: false,
            inputs: [],
        );
    }

    public function store(SearchDegreeVerificationRequest $request): View
    {
        $validated = $request->validated();
        $term = mb_strtolower($validated['term']);

        $query = DegreeVerification::query()
            ->select([
                'degree_id',
                'reg_no',
                'st_name',
                'st_fname',
                'degree_name',
                'subject',
                'year',
            ])
            ->where('year', $validated['year'])
            ->where('level', $validated['level']);

        if ($validated['criteria'] === 'degree_id') {
            $query->whereRaw('LOWER(degree_id) = ?', [$term]);
        } else {
            $query->whereRaw('LOWER(reg_no) = ?', [$term]);
        }

        return $this->verificationView(
            results: $query->limit(20)->get(),
            searched: true,
            inputs: $validated,
        );
    }

    /**
     * @param  array<string, mixed>  $inputs
     */
    private function verificationView($results, bool $searched, array $inputs): View
    {
        $maxYear = now()->year - 2;

        return view('degree-verification.index', [
            'years' => range($maxYear, 1996),
            'levels' => DegreeVerification::LEVELS,
            'results' => $results,
            'searched' => $searched,
            'inputs' => $inputs,
        ]);
    }
}
