@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Edit Event</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $event->title }}</p>
        </div>

        @if (session('status'))
            @include('admin.components.alert', ['type' => 'success', 'title' => 'Success', 'message' => session('status')])
        @endif

        <form action="{{ route('admin.events.update', $event) }}" method="POST"
            class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input name="title" value="{{ old('title', $event->title) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                @error('title')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <input name="location" value="{{ old('location', $event->location) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Image URL</label>
                    <input name="image" value="{{ old('image', $event->image) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Starts At</label>
                    <input type="datetime-local" name="starts_at"
                        value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\\TH:i')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ends At</label>
                    <input type="datetime-local" name="ends_at"
                        value="{{ old('ends_at', optional($event->ends_at)->format('Y-m-d\\TH:i')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $event->status->value ?? $event->status) === $status->value)>
                                {{ ucfirst($status->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap justify-between gap-2">
                <a href="{{ route('admin.events.show', $event) }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">View</a>

                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save</button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
            onsubmit="return confirm('Delete this event?');"
            class="rounded-2xl border border-error-200 bg-error-50 p-6 dark:border-error-700 dark:bg-error-900/10">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-between gap-3">
                <p class="text-theme-sm text-error-700 dark:text-error-400">This action cannot be undone.</p>
                <button type="submit" class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">
                    Delete Event
                </button>
            </div>
        </form>
    </div>
@endsection
