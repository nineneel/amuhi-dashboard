<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('payment_approval_histories')) {
            Schema::rename('payment_approval_histories', 'payment_approvals');
        }

        Schema::table('payment_approvals', function (Blueprint $table) {
            $table->foreignId('responded_by_id')->nullable()->after('actor_role')->constrained('users')->nullOnDelete();
            $table->string('responded_by_role')->nullable()->after('responded_by_id');
            $table->timestamp('responded_at')->nullable()->after('responded_by_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_approvals', function (Blueprint $table) {
            $table->dropForeign(['responded_by_id']);
            $table->dropColumn([
                'responded_by_id',
                'responded_by_role',
                'responded_at',
            ]);
        });

        if (Schema::hasTable('payment_approvals')) {
            Schema::rename('payment_approvals', 'payment_approval_histories');
        }
    }
};
