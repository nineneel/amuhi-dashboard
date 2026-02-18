<?php

use App\Helpers\AdminMenuHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('hides temporarily disabled cms menu paths', function () {
    $admin = User::factory()->admin()->create();

    $menuPaths = collect(AdminMenuHelper::getMenuGroups($admin))
        ->pluck('items')
        ->flatten(1)
        ->pluck('path')
        ->values()
        ->all();

    expect($menuPaths)
        ->not->toContain('/admin/news')
        ->not->toContain('/admin/testimonies')
        ->not->toContain('/admin/events')
        ->not->toContain('/admin/profile');
});
