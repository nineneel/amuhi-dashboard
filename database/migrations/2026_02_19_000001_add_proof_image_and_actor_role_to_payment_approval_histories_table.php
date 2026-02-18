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
        Schema::table('payment_approval_histories', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->after('new_approval_status');
            $table->string('actor_role')->nullable()->after('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_approval_histories', function (Blueprint $table) {
            $table->dropColumn([
                'proof_image',
                'actor_role',
            ]);
        });
    }
};
