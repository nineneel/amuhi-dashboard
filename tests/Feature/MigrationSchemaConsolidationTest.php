<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('keeps the consolidated schema for core tables', function () {
    expect(Schema::hasColumns('users', [
        'role',
        'two_factor_secret',
        'two_factor_enabled',
    ]))->toBeTrue();

    expect(Schema::hasColumn('events', 'image'))->toBeTrue();

    expect(Schema::hasColumns('payments', [
        'payment_method',
        'subscription_plan_id',
    ]))->toBeTrue();

    expect(Schema::hasColumns('payment_approvals', [
        'payment_id',
        'previous_approval_status',
        'new_approval_status',
        'proof_image',
        'actor_id',
        'actor_role',
        'responded_by_id',
        'responded_by_role',
        'responded_at',
        'notes',
    ]))->toBeTrue();

    expect(Schema::hasTable('payment_approval_histories'))->toBeFalse();
    expect(Schema::hasColumn('payments', 'approval_status'))->toBeFalse();
    expect(Schema::hasColumn('payments', 'approved_by'))->toBeFalse();
    expect(Schema::hasColumn('payments', 'approved_at'))->toBeFalse();
    expect(Schema::hasColumn('payments', 'admin_notes'))->toBeFalse();
});
