@props([
    'label' => null,
    'name',
    'id' => null,
    'value' => 1,
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'containerClass' => null,
    'labelClass' => null,
])

@php
    $id = $id ?? $name;

    $containerClass = $containerClass ?? 'flex cursor-pointer items-start gap-3 text-sm text-gray-600 dark:text-gray-400';
    $labelClass = $labelClass ?? 'font-medium text-gray-700 dark:text-gray-400';
@endphp

<div>
    <label for="{{ $id }}" class="{{ $containerClass }}">
        <span class="group relative mt-0.5 inline-flex">
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="checkbox"
                value="{{ $value }}"
                {{ $checked ? 'checked' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except(['class'])->merge(['class' => 'peer sr-only']) }}
            />

            <span
                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-[1.25px] border-gray-300 bg-transparent transition group-hover:border-brand-500 peer-checked:border-brand-500 peer-checked:bg-brand-500 dark:border-gray-700 dark:group-hover:border-brand-500 [&>svg]:opacity-0 peer-checked:[&>svg]:opacity-100"
            >
                <svg
                    class="transition"
                    width="14"
                    height="14"
                    viewBox="0 0 14 14"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                        stroke="white"
                        stroke-width="1.94437"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </span>
        </span>

        <span class="{{ $labelClass }}">
            @if (trim($slot) !== '')
                {{ $slot }}
            @else
                {{ $label }}
                @if ($required)
                    <span class="text-error-500">*</span>
                @endif
            @endif
        </span>
    </label>

    @error($name)
        <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
    @enderror
</div>
