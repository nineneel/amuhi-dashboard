<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonyStoreRequest;
use App\Http\Requests\Admin\TestimonyUpdateRequest;
use App\Models\Testimony;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonyController extends Controller
{
    public function index(): View
    {
        $testimonies = Testimony::query()
            ->ordered()
            ->paginate(15);

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
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = Testimony::query()->max('sort_order') + 1;

        Testimony::query()->create($data);

        return redirect()
            ->route('admin.testimonies.index')
            ->with('success', 'Testimony created successfully.');
    }

    public function edit(Testimony $testimony): View
    {
        return view('admin.content.testimonies.edit', [
            'testimony' => $testimony,
        ]);
    }

    public function update(TestimonyUpdateRequest $request, Testimony $testimony): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $testimony->update($data);

        return redirect()
            ->route('admin.testimonies.index')
            ->with('success', 'Testimony updated successfully.');
    }

    public function destroy(Testimony $testimony): RedirectResponse
    {
        $testimony->delete();

        return redirect()
            ->route('admin.testimonies.index')
            ->with('success', 'Testimony deleted successfully.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->input('order') as $sortOrder => $id) {
            Testimony::query()->where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        return response()->json(['success' => true]);
    }
}
