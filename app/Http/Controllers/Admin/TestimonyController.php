<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonyStoreRequest;
use App\Http\Requests\Admin\TestimonyUpdateRequest;
use App\Models\Testimony;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestimonyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $testimonies = Testimony::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('text', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                }

                if ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('admin.content.testimonies.index', [
            'testimonies' => $testimonies,
        ]);
    }

    public function create(): View
    {
        return view('admin.content.testimonies.create');
    }

    public function store(TestimonyStoreRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        if (! array_key_exists('sort_order', $payload) || $payload['sort_order'] === null) {
            $payload['sort_order'] = (int) Testimony::query()->max('sort_order') + 1;
        }

        $payload['is_active'] = $request->boolean('is_active', true);

        $testimony = Testimony::query()->create($payload);

        return redirect()
            ->route('admin.testimonies.edit', $testimony)
            ->with('status', 'Testimony created successfully.');
    }

    public function edit(Testimony $testimony): View
    {
        return view('admin.content.testimonies.edit', [
            'testimony' => $testimony,
        ]);
    }

    public function update(TestimonyUpdateRequest $request, Testimony $testimony): RedirectResponse
    {
        $payload = $request->validated();
        $payload['is_active'] = $request->boolean('is_active', false);

        $testimony->update($payload);

        return redirect()
            ->route('admin.testimonies.edit', $testimony)
            ->with('status', 'Testimony updated successfully.');
    }

    public function destroy(Testimony $testimony): RedirectResponse
    {
        $testimony->delete();

        return redirect()
            ->route('admin.testimonies.index')
            ->with('status', 'Testimony deleted successfully.');
    }

    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:testimonies,id'],
            'orders.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['orders'] as $item) {
                Testimony::query()
                    ->whereKey($item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Testimonies reordered successfully.',
            ]);
        }

        return redirect()
            ->route('admin.testimonies.index')
            ->with('status', 'Testimonies reordered successfully.');
    }
}
