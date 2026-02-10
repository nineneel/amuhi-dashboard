@extends('layouts.app')

@section('title', 'Settings')
@section('header', 'Settings')
@section('content-class', 'p-0 d-flex align-items-stretch')
@section('footer-class', '')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Settings</li>
@endsection

@section('content')
    <div class="d-flex flex-grow-1">
        {{-- Settings Sidebar --}}
        <div class="content-sidebar content-sidebar-md h-100 min-vh-100">
            <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
                <h4 class="fw-bolder mb-0">Settings</h4>
                <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex d-lg-none">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="content-sidebar-body">
                <ul class="nav flex-column nxl-content-sidebar-item">
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'account' ? 'active' : '' }}" href="{{ route('settings.index', ['section' => 'account']) }}">
                            <i class="feather-user"></i>
                            <span>Account</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'notifications' ? 'active' : '' }}" href="{{ route('settings.index', ['section' => 'notifications']) }}">
                            <i class="feather-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'privacy' ? 'active' : '' }}" href="{{ route('settings.index', ['section' => 'privacy']) }}">
                            <i class="feather-shield"></i>
                            <span>Privacy</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'appearance' ? 'active' : '' }}" href="{{ route('settings.index', ['section' => 'appearance']) }}">
                            <i class="feather-sun"></i>
                            <span>Appearance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'security' ? 'active' : '' }}" href="{{ route('settings.index', ['section' => 'security']) }}">
                            <i class="feather-lock"></i>
                            <span>Security</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $section === 'danger' ? 'active' : '' }} text-danger" href="{{ route('settings.index', ['section' => 'danger']) }}">
                            <i class="feather-alert-triangle"></i>
                            <span>Danger Zone</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Settings Content --}}
        <div class="content-area grow h-100 min-vh-100">
            <div class="content-area-header bg-white sticky-top d-lg-none">
                <div class="page-header-left">
                    <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2">
                        <i class="feather-menu fs-24"></i>
                    </a>
                    <span class="fw-bold">{{ ucfirst($section) }}</span>
                </div>
            </div>
            <div class="content-area-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @include("settings.sections.{$section}")
            </div>
        </div>
    </div>

    <div class="content-sidebar-backdrop d-lg-none"></div>
@endsection
