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
        Schema::table('product_boosts', function (Blueprint $table) {
            // Supprimer les anciennes colonnes
            $table->dropColumn(['boost_type', 'duration_hours', 'starts_at', 'price', 'currency', 'payment_method', 'transaction_id', 'views_gained', 'clicks_gained', 'sales_generated', 'metadata', 'admin_notes']);
            
            // Ajouter les nouvelles colonnes
            $table->foreignId('boost_type_id')->after('user_id')->constrained('boost_types')->onDelete('cascade');
            $table->integer('duration')->after('boost_type_id')->comment('Durée en jours');
            $table->decimal('total_price', 15, 2)->after('duration');
            $table->timestamp('activated_at')->nullable()->after('total_price');
            $table->timestamp('cancelled_at')->nullable()->after('expires_at');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('cancelled_at');
            $table->integer('views_generated')->default(0)->after('refund_amount');
            $table->integer('clicks_generated')->default(0)->after('views_generated');
            $table->json('metadata')->nullable()->after('clicks_generated');
            
            // Modifier la colonne status pour avoir les bonnes valeurs par défaut
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_boosts', function (Blueprint $table) {
            // Remettre les anciennes colonnes
            $table->dropForeign(['boost_type_id']);
            $table->dropColumn(['boost_type_id', 'duration', 'total_price', 'activated_at', 'cancelled_at', 'refund_amount', 'views_generated', 'clicks_generated', 'metadata']);
            
            // Remettre les anciennes colonnes
            $table->string('boost_type')->after('user_id');
            $table->integer('duration_hours')->after('boost_type');
            $table->timestamp('starts_at')->after('duration_hours');
            $table->decimal('price', 10, 2)->after('expires_at');
            $table->string('currency', 3)->default('USD')->after('price');
            $table->string('payment_method')->after('currency');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->integer('views_gained')->default(0)->after('transaction_id');
            $table->integer('clicks_gained')->default(0)->after('views_gained');
            $table->integer('sales_generated')->default(0)->after('clicks_gained');
            $table->json('metadata')->nullable()->after('sales_generated');
            $table->text('admin_notes')->nullable()->after('metadata');
        });
    }
};
