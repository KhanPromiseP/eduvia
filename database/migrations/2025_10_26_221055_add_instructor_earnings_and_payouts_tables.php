<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Only create instructor_earnings if it doesn't exist
        if (!Schema::hasTable('instructor_earnings')) {
            Schema::create('instructor_earnings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
                $table->foreignId('payment_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2); // Instructor's share (70%)
                $table->decimal('platform_fee', 10, 2); // Platform's share (30%)
                $table->decimal('total_amount', 10, 2); // Original payment amount
                $table->string('currency')->default('USD');
                $table->string('status')->default('pending'); // pending, processed, paid_out
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('paid_out_at')->nullable();
                $table->timestamps();
                
                $table->index('instructor_id');
                $table->index('payment_id');
                $table->index('status');
                $table->index(['status', 'processed_at']);
            });
        }

        // Only create instructor_payouts if it doesn't exist
        if (!Schema::hasTable('instructor_payouts')) {
            Schema::create('instructor_payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
                $table->string('payout_method'); // mobile_money, bank_account, tranzak_wallet
                $table->string('account_name');
                $table->string('account_number'); // phone number for mobile money, account number for bank
                $table->string('operator')->nullable(); // MTN, Orange, Bank name
                $table->string('currency')->default('XAF');
                $table->decimal('payout_threshold', 10, 2)->default(0);
                $table->boolean('auto_payout')->default(true);
                $table->json('verification_data')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
                
                $table->index('instructor_id');
                $table->index('payout_method');
                $table->index('is_verified');
            });
        }

        // Add payout_setup_completed to instructor_applications table if it exists
        if (Schema::hasTable('instructor_applications') && !Schema::hasColumn('instructor_applications', 'payout_setup_completed')) {
            Schema::table('instructor_applications', function (Blueprint $table) {
                $table->boolean('payout_setup_completed')->default(false)->after('status');
            });
        }
    }

    public function down()
    {
        // Only drop tables if they exist
        if (Schema::hasTable('instructor_earnings')) {
            Schema::dropIfExists('instructor_earnings');
        }
        
        if (Schema::hasTable('instructor_payouts')) {
            Schema::dropIfExists('instructor_payouts');
        }
        
        // Remove column if it exists
        if (Schema::hasTable('instructor_applications') && Schema::hasColumn('instructor_applications', 'payout_setup_completed')) {
            Schema::table('instructor_applications', function (Blueprint $table) {
                $table->dropColumn('payout_setup_completed');
            });
        }
    }
};