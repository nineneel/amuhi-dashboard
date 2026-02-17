<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
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

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(AdminStoreRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()
            ->route('admin.admins.index')
            ->with('status', 'Admin account created successfully.');
    }

    public function edit(User $admin): View
    {
        $this->ensureManageableAdmin($admin);

        return view('admin.admins.edit', [
            'admin' => $admin,
        ]);
    }

    public function update(AdminUpdateRequest $request, User $admin): RedirectResponse
    {
        $this->ensureManageableAdmin($admin);

        $admin->update([
            'role' => $request->validated('role'),
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('status', 'Admin role updated successfully.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        $this->ensureManageableAdmin($admin);

        if (auth()->id() === $admin->id) {
            return back()->with('warning', 'You cannot remove your own admin access.');
        }

        $admin->update([
            'role' => Role::Member,
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('status', 'Admin access removed successfully.');
    }

    private function ensureManageableAdmin(User $admin): void
    {
        $roleValue = $admin->role instanceof Role
            ? $admin->role->value
            : (string) $admin->role;

        if (! in_array($roleValue, [Role::Admin->value, Role::SuperAdmin->value], true)) {
            abort(404);
        }
    }
}
