<?php

use App\Enums\Role;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\News;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\Testimony;
use App\Models\User;
use Database\Seeders\TemporaryCmsTestDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds temporary cms data for pagination and filter testing', function () {
    $this->seed(TemporaryCmsTestDataSeeder::class);

    expect(User::query()->where('email', 'like', '%@seed-temp.amuhi.test')->count())->toBeGreaterThanOrEqual(143)
        ->and(User::query()->where('role', Role::Member->value)->where('email', 'like', '%@seed-temp.amuhi.test')->count())->toBeGreaterThanOrEqual(140)
        ->and(Event::query()->where('title', 'like', '[TEMP] %')->count())->toBe(55)
        ->and(News::query()->where('slug', 'like', 'temp-news-%')->count())->toBe(75)
        ->and(Testimony::query()->where('name', 'like', 'Temp %')->count())->toBe(40)
        ->and(Subscription::query()->where('gateway_subscription_id', 'like', 'TMP-SUB-%')->count())->toBe(90)
        ->and(Invoice::query()->where('invoice_number', 'like', 'TMP-INV-%')->count())->toBeGreaterThanOrEqual(90)
        ->and(Payment::query()->where('gateway_transaction_id', 'like', 'TMP-TXN-%')->count())->toBeGreaterThanOrEqual(40)
        ->and(SiteSetting::query()->where('key', 'like', 'temp_%')->count())->toBe(5);
});
