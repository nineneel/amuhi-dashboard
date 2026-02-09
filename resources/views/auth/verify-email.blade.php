@extends('layouts.guest')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Verify your email</h1>
    <p class="mt-2 text-sm text-gray-700">
        Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}" class="mt-6 space-y-3">
        @csrf
        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Resend verification email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Logout
        </button>
    </form>
@endsection
