<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('verified_by');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete()->after('blocked_at');
            $table->text('block_reason')->nullable()->after('blocked_by');

            $table->boolean('is_suspended')->default(false)->after('block_reason');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->timestamp('suspended_until')->nullable()->after('suspended_at');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->nullOnDelete()->after('suspended_until');
            $table->text('suspend_reason')->nullable()->after('suspended_by');

            $table->text('rejection_reason')->nullable()->after('suspend_reason');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropForeign(['suspended_by']);
            $table->dropColumn([
                'is_blocked',
                'blocked_at',
                'blocked_by',
                'block_reason',
                'is_suspended',
                'suspended_at',
                'suspended_until',
                'suspended_by',
                'suspend_reason',
                'rejection_reason',
            ]);
        });
    }
};
