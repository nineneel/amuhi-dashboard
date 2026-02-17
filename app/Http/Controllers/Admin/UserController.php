<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): View
    {
        $data = $request->validated();
        $search = trim((string) ($data['search'] ?? ''));
        $role = (string) ($data['role'] ?? '');
        $status = (string) ($data['status'] ?? '');
        $perPage = (int) ($data['per_page'] ?? 10);
        $sort = (string) ($data['sort'] ?? 'created_at');
        $direction = (string) ($data['direction'] ?? 'desc');

        $users = User::query()
            ->withCount(['subscriptions', 'invoices'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== '', function ($query) use ($role): void {
                $query->where('role', $role);
            })
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                }

                if ($status === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'sortField' => $sort,
            'sortDirection' => $direction,
        ]);
    }

    public function show(User $user): View
    {
        $user->load([
            'profile',
            'subscriptions.subscriptionPlan',
            'activityLogs' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('warning', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }
}
