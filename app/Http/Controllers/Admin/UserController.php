<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
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
        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 15);

        $users = User::query()
            ->where('role', Role::Member)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['subscriptions.subscriptionPlan', 'invoices']);

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
            ->route('admin.users.index')
            ->with('success', __('ui.admin.user_updated_successfully'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('ui.admin.user_deleted_successfully'));
    }
}
