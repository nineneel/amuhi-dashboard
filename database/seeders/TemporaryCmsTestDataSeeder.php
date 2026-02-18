<?php

namespace Database\Seeders;

use App\Enums\NewsStatus;
use App\Enums\Role;
use App\EventStatus;
use App\InvoiceStatus;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\News;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Testimony;
use App\Models\User;
use App\PaymentStatus;
use App\SubscriptionStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TemporaryCmsTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->cleanupPreviousTemporaryData();

        $this->seedTemporaryAdmins();
        $memberUsers = User::factory()->count(140)->create([
            'role' => Role::Member,
            'email' => fn () => 'temp-member-'.Str::lower(Str::random(12)).'@seed-temp.amuhi.test',
        ]);

        $plans = SubscriptionPlan::factory()->count(6)->create();

        $this->seedTemporaryEvents();
        $this->seedTemporaryNews();
        $this->seedTemporaryTestimonies();
        $this->seedTemporarySiteSettings();

        $this->seedTemporaryBillingData($memberUsers->all(), $plans->all());
    }

    private function cleanupPreviousTemporaryData(): void
    {
        Payment::query()
            ->where('gateway_transaction_id', 'like', 'TMP-TXN-%')
            ->delete();

        Invoice::query()
            ->where('invoice_number', 'like', 'TMP-INV-%')
            ->delete();

        Subscription::query()
            ->where('gateway_subscription_id', 'like', 'TMP-SUB-%')
            ->delete();

        News::query()
            ->where('slug', 'like', 'temp-news-%')
            ->delete();

        Event::query()
            ->where('title', 'like', '[TEMP] %')
            ->delete();

        Testimony::query()
            ->where('name', 'like', 'Temp %')
            ->delete();

        SiteSetting::query()
            ->where('key', 'like', 'temp_%')
            ->delete();

        User::query()
            ->where('email', 'like', '%@seed-temp.amuhi.test')
            ->delete();
    }

    private function seedTemporaryAdmins(): void
    {
        $adminAccounts = [
            ['name' => 'Temp Super Admin', 'email' => 'temp-super-admin@seed-temp.amuhi.test', 'role' => Role::SuperAdmin],
            ['name' => 'Temp Admin One', 'email' => 'temp-admin-1@seed-temp.amuhi.test', 'role' => Role::Admin],
            ['name' => 'Temp Admin Two', 'email' => 'temp-admin-2@seed-temp.amuhi.test', 'role' => Role::Admin],
        ];

        foreach ($adminAccounts as $account) {
            User::factory()->create([
                'name' => $account['name'],
                'email' => $account['email'],
                'role' => $account['role'],
            ]);
        }
    }

    private function seedTemporaryEvents(): void
    {
        $statuses = EventStatus::cases();

        for ($i = 1; $i <= 55; $i++) {
            $status = $statuses[$i % count($statuses)];
            $startAt = now()->subDays(90)->addDays($i * 3);

            Event::query()->create([
                'title' => sprintf('[TEMP] Event %03d', $i),
                'description' => fake()->paragraph(3),
                'location' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Online', 'Yogyakarta']),
                'image' => fake()->boolean(65) ? '/temp-images/events/event-'.$i.'.jpg' : null,
                'starts_at' => $startAt,
                'ends_at' => $startAt->copy()->addHours(fake()->numberBetween(2, 8)),
                'status' => $status,
            ]);
        }
    }

    private function seedTemporaryNews(): void
    {
        News::factory()->count(35)->published()->create([
            'slug' => fn () => 'temp-news-'.Str::lower(Str::random(10)),
        ]);

        News::factory()->count(25)->draft()->create([
            'slug' => fn () => 'temp-news-'.Str::lower(Str::random(10)),
        ]);

        News::factory()->count(15)->archived()->create([
            'slug' => fn () => 'temp-news-'.Str::lower(Str::random(10)),
        ]);
    }

    private function seedTemporaryTestimonies(): void
    {
        for ($i = 1; $i <= 40; $i++) {
            Testimony::factory()->create([
                'name' => sprintf('Temp Member %03d', $i),
                'sort_order' => $i,
                'is_active' => $i % 5 !== 0,
            ]);
        }
    }

    private function seedTemporarySiteSettings(): void
    {
        $settings = [
            'temp_general_contact_name' => ['group' => 'general', 'value' => ['text' => 'Temp Contact Team']],
            'temp_general_hotline' => ['group' => 'general', 'value' => ['text' => '+62-21-0000-9999']],
            'temp_seo_title' => ['group' => 'seo', 'value' => ['text' => 'Temp SEO Title']],
            'temp_seo_description' => ['group' => 'seo', 'value' => ['text' => 'Temp SEO Description']],
            'temp_contact_whatsapp' => ['group' => 'contact', 'value' => ['number' => '+62-812-0000-1111']],
        ];

        foreach ($settings as $key => $setting) {
            SiteSetting::query()->create([
                'key' => $key,
                'group' => $setting['group'],
                'value' => $setting['value'],
            ]);
        }
    }

    /**
     * @param  array<int, User>  $users
     * @param  array<int, SubscriptionPlan>  $plans
     */
    private function seedTemporaryBillingData(array $users, array $plans): void
    {
        $subscriptionStatuses = SubscriptionStatus::cases();
        $invoiceStatuses = InvoiceStatus::cases();
        $paymentStatuses = PaymentStatus::cases();

        $subscriptionNumber = 1;
        $invoiceNumber = 1;
        $paymentNumber = 1;

        $billingUsers = Arr::random($users, 90);

        foreach ($billingUsers as $user) {
            $subscriptionStatus = $subscriptionStatuses[$subscriptionNumber % count($subscriptionStatuses)];
            $startsAt = now()->subDays(fake()->numberBetween(180, 10));
            $endsAt = $startsAt->copy()->addDays(fake()->randomElement([30, 90, 180, 365]));

            $subscription = Subscription::query()->create([
                'user_id' => $user->id,
                'subscription_plan_id' => Arr::random($plans)->id,
                'status' => $subscriptionStatus,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'gateway_subscription_id' => sprintf('TMP-SUB-%05d', $subscriptionNumber),
            ]);

            $subscriptionNumber++;

            $invoiceCount = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $invoiceCount; $i++) {
                $invoiceStatus = $invoiceStatuses[$invoiceNumber % count($invoiceStatuses)];
                $amount = fake()->numberBetween(3500000, 25000000);

                $invoice = Invoice::query()->create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'invoice_number' => sprintf('TMP-INV-%06d', $invoiceNumber),
                    'amount' => $amount,
                    'due_date' => now()->addDays(fake()->numberBetween(1, 45))->toDateString(),
                    'status' => $invoiceStatus,
                ]);

                $invoiceNumber++;

                if ($invoiceStatus === InvoiceStatus::Pending && fake()->boolean(60)) {
                    continue;
                }

                $paymentCount = fake()->numberBetween(1, 2);

                for ($paymentIndex = 0; $paymentIndex < $paymentCount; $paymentIndex++) {
                    $paymentStatus = $paymentStatuses[$paymentNumber % count($paymentStatuses)];

                    Payment::query()->create([
                        'invoice_id' => $invoice->id,
                        'gateway_transaction_id' => sprintf('TMP-TXN-%07d', $paymentNumber),
                        'amount' => $amount,
                        'status' => $paymentStatus,
                        'paid_at' => $paymentStatus === PaymentStatus::Pending ? null : now()->subDays(fake()->numberBetween(0, 30)),
                    ]);

                    $paymentNumber++;
                }
            }
        }
    }
}
