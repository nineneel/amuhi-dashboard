@extends('layouts.guest')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Two-factor authentication</h1>
    <p class="mt-2 text-sm text-gray-700">Enter the 6-digit code from your authenticator app.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Authentication code</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Verify and continue
        </button>
    </form>
@endsection
