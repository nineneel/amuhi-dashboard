<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load(['profile', 'settings', 'subscriptions.subscriptionPlan']);

        return view('profile.index', compact('user'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('profile');

        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $originalName = $user->name;

        DB::transaction(function () use ($data, $user, $request, $originalName) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            $profileData = [
                'member_type' => $data['member_type'],
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'bio' => $data['bio'] ?? null,
            ];

            if ($request->hasFile('photo')) {
                if ($user->profile?->photo) {
                    Storage::disk('public')->delete($user->profile->photo);
                }
                $profileData['photo'] = $request->file('photo')->store('profiles', 'public');
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            $description = $originalName !== $data['name']
                ? 'Updated profile name from '.$originalName.' to '.$data['name'].'.'
                : 'Updated profile details.';

            ActivityLog::query()->create([
                'user_id' => $user->id,
                'action' => 'Profile updated',
                'description' => $description,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }
}
