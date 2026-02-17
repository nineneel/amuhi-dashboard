<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $sortField = (string) $request->query('sort', 'created_at');
        $sortDirection = strtolower((string) $request->query('direction', 'desc'));

        if (! in_array($sortField, ['name', 'email', 'role', 'created_at'], true)) {
            $sortField = 'created_at';
        }

        if (! in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $roleFilter = (string) $request->query('role');
        $search = trim((string) $request->query('search'));

        $admins = User::query()
            ->whereIn('role', [Role::Admin->value, Role::SuperAdmin->value])
            ->when($roleFilter !== '', function ($query) use ($roleFilter): void {
                if (in_array($roleFilter, [Role::Admin->value, Role::SuperAdmin->value], true)) {
                    $query->where('role', $roleFilter);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('admin.admins.index', [
            'admins' => $admins,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin creation UI will be available in the next step.');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin creation UI will be available in the next step.');
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin detail UI will be available in the next step.');
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin edit UI will be available in the next step.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin edit UI will be available in the next step.');
    }

    public function destroy(string $id): RedirectResponse
    {
        return redirect()
            ->route('admin.admins.index')
            ->with('warning', 'Admin removal UI will be available in the next step.');
    }
}
