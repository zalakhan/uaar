@extends('layouts.public')

@section('title', 'Degree Verification')

@section('content')
    <div class="public-card">
        <aside class="public-card__aside" aria-label="Verification steps">
            <h2 class="public-card__aside-title">Verify your degree</h2>
            <p class="public-card__aside-text">
                Search official graduation records by year, degree level, and registration or degree number.
            </p>
            <ul class="public-card__aside-steps">
                <li><span class="public-card__step-dot is-active" aria-hidden="true"></span> Select year &amp; level</li>
                <li><span class="public-card__step-dot" aria-hidden="true"></span> Choose search criteria</li>
                <li><span class="public-card__step-dot" aria-hidden="true"></span> View verification result</li>
            </ul>
        </aside>

        <div class="public-card__body">
            <h2 class="public-card__heading">Degree Verification</h2>

            <form method="POST" action="{{ route('degree-verification.store') }}" novalidate>
                @csrf
                <div class="public-form-grid public-form-grid--two">
                    <x-public.select
                        name="year"
                        label="Year"
                        placeholder="Select Year"
                        :options="collect($years)->mapWithKeys(fn ($y) => [$y => $y])->all()"
                        :value="$inputs['year'] ?? null"
                    />

                    <x-public.select
                        name="level"
                        label="Degree Level"
                        placeholder="Select Degree Level"
                        :options="$levels"
                        :value="$inputs['level'] ?? null"
                    />
                </div>

                <div class="public-form-row public-form-row--criteria" style="margin-top: 1rem;">
                    <x-public.select
                        name="criteria"
                        label="Search Criteria"
                        placeholder="Select Criteria"
                        :options="[
                            'reg_no' => 'Registration No.',
                            'degree_id' => 'Degree Number',
                        ]"
                        :value="$inputs['criteria'] ?? null"
                    />

                    <x-public.input
                        name="term"
                        label="Search Term"
                        maxlength="50"
                        :value="$inputs['term'] ?? null"
                        placeholder="Enter registration or degree number"
                    />
                </div>

                <div class="public-form-actions">
                    <x-public.button type="submit">Verify Degree</x-public.button>
                </div>
            </form>

            @if ($searched)
                <div class="public-results">
                    @if ($results->isNotEmpty())
                        <x-public.alert type="success">Degree verified</x-public.alert>

                        <x-public.table-wrapper style="margin-top: 1rem;">
                            <table class="public-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Degree No.</th>
                                        <th scope="col">Registration No.</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Father Name</th>
                                        <th scope="col">Degree Name</th>
                                        <th scope="col">Subject</th>
                                        <th scope="col">Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $row)
                                        <tr>
                                            <td>{{ $row->degree_id }}</td>
                                            <td>{{ $row->reg_no }}</td>
                                            <td>{{ $row->st_name }}</td>
                                            <td>{{ $row->st_fname }}</td>
                                            <td>{{ $row->degree_name }}</td>
                                            <td>{{ $row->subject }}</td>
                                            <td>{{ $row->year }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </x-public.table-wrapper>
                    @else
                        <x-public.alert type="danger">
                            No record found. The degree could not be verified.
                        </x-public.alert>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
