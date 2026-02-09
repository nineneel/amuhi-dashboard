@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome to {{ config('app.name') }}</h5>
                    <p class="text-muted">Features coming soon.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
