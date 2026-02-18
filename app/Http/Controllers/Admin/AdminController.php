<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $roleFilter = (string) $request->query('role', '');
        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 15);

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
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.admins.index', [
            'admins' => $admins,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function create(): View
    {
        return view('admin.admins.create', [
            'roles' => [Role::Admin, Role::SuperAdmin],
        ]);
    }

    public function store(AdminStoreRequest $request): RedirectResponse
    {
        User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin account created successfully.');
    }

    public function show(User $admin): RedirectResponse
    {
        return redirect()->route('admin.admins.edit', $admin);
    }

    public function edit(User $admin): View
    {
        return view('admin.admins.edit', [
            'admin' => $admin,
            'roles' => [Role::Admin, Role::SuperAdmin],
        ]);
    }

    public function update(AdminUpdateRequest $request, User $admin): RedirectResponse
    {
        $admin->update([
            'role' => $request->input('role'),
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin role updated successfully.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        if ($admin->is(auth()->user())) {
            return redirect()
                ->route('admin.admins.index')
                ->with('error', 'You cannot remove your own admin access.');
        }

        $admin->update(['role' => Role::Member]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin access removed successfully.');
    }
}
