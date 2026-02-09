@extends('layouts.app')

@section('title', 'Programs')
@section('header', 'Programs')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Programs</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Programs</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    @php
        $programs = [
            ['name' => 'AMUHI Academy', 'icon' => 'feather-book-open', 'desc' => 'Training, certification, and industry readiness programs.'],
            ['name' => 'AMUHI Check', 'icon' => 'feather-check-square', 'desc' => 'Compliance checks and operational assessments.'],
            ['name' => 'AMUHI Protect', 'icon' => 'feather-shield', 'desc' => 'Risk management, insurance, and protection services.'],
            ['name' => 'AMUHI Care', 'icon' => 'feather-heart', 'desc' => 'Member care, guidance, and community support.'],
            ['name' => 'AMUHI Network', 'icon' => 'feather-users', 'desc' => 'Industry networking and partnership opportunities.'],
            ['name' => 'AMUHI Digital', 'icon' => 'feather-monitor', 'desc' => 'Digital tools, dashboards, and reports.'],
        ];
    @endphp

    <div class="row g-4">
        <div class="col-12">
            <div class="card stretch">
                <div class="card-body text-center">
                    <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                        <i class="feather-activity"></i>
                    </div>
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
                            <div class="avatar-text bg-soft-primary text-primary">
                                <i class="{{ $program['icon'] }}"></i>
                            </div>
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
