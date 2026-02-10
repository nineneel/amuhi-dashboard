@extends('layouts.app')

@section('title', 'Programs')
@section('header', 'Programs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Programs</li>
@endsection

@section('content')
    @php
        $programs = [
            ['name' => 'AMUHI Academy', 'icon' => 'programs/academy.png', 'desc' => 'Training, certification, and industry readiness programs.'],
            ['name' => 'AMUHI Check', 'icon' => 'programs/check.png', 'desc' => 'Compliance checks and operational assessments.'],
            ['name' => 'AMUHI Protect', 'icon' => 'programs/protect.png', 'desc' => 'Risk management, insurance, and protection services.'],
            ['name' => 'AMUHI Care', 'icon' => 'programs/care.png', 'desc' => 'Member care, guidance, and community support.'],
            ['name' => 'AMUHI Network', 'icon' => 'programs/network.png', 'desc' => 'Industry networking and partnership opportunities.'],
            ['name' => 'AMUHI Digital', 'icon' => 'programs/digital.png', 'desc' => 'Digital tools, dashboards, and reports.'],
        ];
    @endphp

    <div class="row g-4">
        <div class="col-12">
            <div class="card stretch">
                <div class="card-body text-center">
                    <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" width="64" height="64" class="mx-auto mb-3 d-block">
                    <h4 class="mb-2">Programs are launching soon</h4>
                    <p class="text-muted mb-0">We are preparing six flagship AMUHI programs to support members throughout the year.</p>
                </div>
            </div>
        </div>

        @foreach($programs as $program)
            <div class="col-md-6 col-xl-4">
                <div class="card stretch h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <img src="{{ asset('images/' . $program['icon']) }}" alt="{{ $program['name'] }}" width="48" height="48" class="rounded-circle">
                            <span class="badge bg-soft-warning text-warning">Coming Soon</span>
                        </div>
                        <h5 class="mb-2">{{ $program['name'] }}</h5>
                        <p class="text-muted fs-13 mb-0">{{ $program['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
