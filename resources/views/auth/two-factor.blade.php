@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-900">Two-Factor Authentication</h1>
        <p class="mt-1 text-sm text-gray-600">Add an extra layer of security to your account.</p>

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! $twoFactorEnabled)
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Enable two-factor</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Scan the QR code with your authenticator app and enter the 6-digit code to confirm.
                </p>

                @if ($qrCodeUrl)
                    <div class="mt-4 flex flex-col items-start gap-4 md:flex-row md:items-center">
                        <img src="{{ $qrCodeUrl }}" alt="QR code" class="h-40 w-40 rounded-md border border-gray-200 bg-white p-2 shadow" />
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold">Secret key</p>
                            <p class="mt-1 break-all font-mono text-xs text-gray-800">{{ $secret }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700" for="code">Authentication code</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Enable
                    </button>
                </form>
            </div>
        @else
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Recovery codes</h2>
                <p class="mt-2 text-sm text-gray-600">Use a recovery code if you lose access to your authenticator app.</p>

                <div class="mt-3 grid grid-cols-2 gap-2 text-xs font-mono text-gray-800 md:grid-cols-4">
                    @forelse ($recoveryCodes as $code)
                        <div class="rounded-md bg-gray-100 px-2 py-1 text-center">{{ $code }}</div>
                    @empty
                        <div class="col-span-4 rounded-md bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
                            No recovery codes available.
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                        Disable two-factor
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
