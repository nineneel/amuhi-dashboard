@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6" x-data="testimonyReorder()">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Testimonies</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage testimonials and display order.</p>
            </div>
            <a href="{{ route('admin.testimonies.create') }}"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                Add Testimony
            </a>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.testimonies.index'),
                'searchPlaceholder' => 'Search by name, role, or quote',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'is_active',
                        'label' => 'Status',
                        'all_label' => 'All',
                        'options' => [
                            '1' => 'Active',
                            '0' => 'Inactive',
                        ],
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'Rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => '10 rows',
                            '15' => '15 rows',
                            '25' => '25 rows',
                            '50' => '50 rows',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-5',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Person'],
                ['label' => 'Quote'],
                ['label' => 'Status'],
                ['label' => 'Order'],
                ['label' => 'Actions'],
            ],
            'paginator' => $testimonies,
        ])
            @forelse ($testimonies as $testimony)
                <tr class="js-sortable-testimony border-b border-gray-100 dark:border-gray-800" draggable="true"
                    data-id="{{ $testimony->id }}">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $testimony->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $testimony->role }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="max-w-lg truncate text-sm text-gray-500 dark:text-gray-400">{{ $testimony->text }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', [
                            'type' => $testimony->is_active ? 'success' : 'neutral',
                            'text' => $testimony->is_active ? 'Active' : 'Inactive',
                        ])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <span class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 5H12.5M7.5 10H12.5M7.5 15H12.5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            {{ $testimony->sort_order }}
                        </span>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'editUrl' => route('admin.testimonies.edit', $testimony),
                            'deleteUrl' => route('admin.testimonies.destroy', $testimony),
                            'deleteConfirm' => 'Delete this testimony?',
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No testimonies found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection

@push('scripts')
    <script>
        function testimonyReorder() {
            return {
                init() {
                    const rows = Array.from(document.querySelectorAll('.js-sortable-testimony'));
                    const tbody = rows[0]?.parentElement;

                    if (! tbody || rows.length === 0) {
                        return;
                    }

                    let draggedRow = null;

                    rows.forEach((row) => {
                        row.addEventListener('dragstart', (event) => {
                            draggedRow = row;
                            event.dataTransfer.effectAllowed = 'move';
                        });

                        row.addEventListener('dragover', (event) => {
                            event.preventDefault();
                            const target = row;

                            if (! draggedRow || draggedRow === target) {
                                return;
                            }

                            const rect = target.getBoundingClientRect();
                            const isAfter = event.clientY > rect.top + rect.height / 2;
                            tbody.insertBefore(draggedRow, isAfter ? target.nextSibling : target);
                        });

                        row.addEventListener('drop', async (event) => {
                            event.preventDefault();

                            const order = Array.from(tbody.querySelectorAll('tr[data-id]')).map((item) => Number(item.dataset.id));

                            await fetch('{{ route('admin.testimonies.reorder') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ order }),
                            });
                        });
                    });
                },
            };
        }
    </script>
@endpush
