@extends('layouts.guest')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Create account</h1>
    <p class="mt-1 text-sm text-gray-600">Join Amuhi to access programs and events.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="member_type" class="block text-sm font-medium text-gray-700">Member Type</label>
            <select id="member_type" name="member_type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @foreach (\App\MemberType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('member_type') === $type->value)>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name (optional)</label>
            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="terms" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('terms') ? 'checked' : '' }}>
            I agree to the terms and conditions
        </label>

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Create account
        </button>

        <p class="text-center text-sm text-gray-600">
            Already have an account?
            <a class="text-indigo-600 hover:underline" href="{{ route('login') }}">Log in</a>
        </p>
    </form>
@endsection
