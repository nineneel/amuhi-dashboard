@extends('layouts.guest')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Forgot password</h1>
    <p class="mt-2 text-sm text-gray-700">Enter your email and we will send you a reset link.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Email reset link
        </button>

        <p class="text-center text-sm text-gray-600">
            <a class="text-indigo-600 hover:underline" href="{{ route('login') }}">Back to login</a>
        </p>
    </form>
@endsection
