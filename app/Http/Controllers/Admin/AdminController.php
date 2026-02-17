<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.admins.index');
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
