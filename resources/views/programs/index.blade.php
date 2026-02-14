@extends('layouts.app')

@section('content')
    @php
        $programs = [
            [
                'id' => 1,
                'title' => 'AMUHI Academy',
                'icon' => 'images/programs/academy-nobg.png',
            ],
            [
                'id' => 2,
                'title' => 'AMUHI Check',
                'icon' => 'images/programs/check-nobg.png',
            ],
            [
                'id' => 3,
                'title' => 'AMUHI Protect',
                'icon' => 'images/programs/protect-nobg.png',
            ],
            [
                'id' => 4,
                'title' => 'AMUHI Care',
                'icon' => 'images/programs/care-nobg.png',
            ],
            [
                'id' => 5,
                'title' => 'AMUHI Network',
                'icon' => 'images/programs/network-nobg.png',
            ],
            [
                'id' => 6,
                'title' => 'AMUHI Digital',
                'icon' => 'images/programs/digital-nobg.png',
            ],
        ];
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.programs.title')" />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($programs as $program)
            <x-program.program-card
                :title="$program['title']"
                :description="__('ui.programs.module_under_development')"
                :icon="$program['icon']"
            />
        @endforeach
    </div>
@endsection
