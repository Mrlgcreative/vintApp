<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Liste de tous les index à créer
        $indexes = [
            // Table items
            "ALTER TABLE items ADD INDEX idx_items_status_created (status, created_at)",
            "ALTER TABLE items ADD INDEX idx_items_category_status (category_id, status)",
            "ALTER TABLE items ADD INDEX idx_items_brand_status (brand_id, status)",
            "ALTER TABLE items ADD INDEX idx_items_user_status (user_id, status)",
            "ALTER TABLE items ADD INDEX idx_items_price_currency (price, currency)",
            "ALTER TABLE items ADD INDEX idx_items_condition (`condition`)",
            
            // Table orders
            "ALTER TABLE orders ADD INDEX idx_orders_buyer_status (buyer_id, status)",
            "ALTER TABLE orders ADD INDEX idx_orders_seller_status (seller_id, status)",
            "ALTER TABLE orders ADD INDEX idx_orders_status_created (status, created_at)",
            "ALTER TABLE orders ADD INDEX idx_orders_scan_token (scan_token)",
            
            // Table users
            "ALTER TABLE users ADD INDEX idx_users_google_id (google_id)",
            "ALTER TABLE users ADD INDEX idx_users_apple_id (apple_id)",
            "ALTER TABLE users ADD INDEX idx_users_firebase_uid (firebase_uid)",
            "ALTER TABLE users ADD INDEX idx_users_email_verified (email_verified_at)",
            
            // Table messages
            "ALTER TABLE messages ADD INDEX idx_messages_conversation_created (conversation_id, created_at)",
            "ALTER TABLE messages ADD INDEX idx_messages_sender_created (sender_id, created_at)",
            "ALTER TABLE messages ADD INDEX idx_messages_receiver_read (receiver_id, is_read)",
            
            // Table notifications
            "ALTER TABLE notifications ADD INDEX idx_notifications_user_read (user_id, read_at)",
            "ALTER TABLE notifications ADD INDEX idx_notifications_user_created (user_id, created_at)",
            
            // Table wallet_transactions
            "ALTER TABLE wallet_transactions ADD INDEX idx_wallet_trans_wallet_created (wallet_id, created_at)",
            "ALTER TABLE wallet_transactions ADD INDEX idx_wallet_trans_type_status (type, status)",
            "ALTER TABLE wallet_transactions ADD INDEX idx_wallet_trans_status (status)",
            
            // Table product_boosts
            "ALTER TABLE product_boosts ADD INDEX idx_boosts_item_status_expires (item_id, status, expires_at)",
            "ALTER TABLE product_boosts ADD INDEX idx_boosts_active_period (status, activated_at, expires_at)",
            
            // Table reviews
            "ALTER TABLE reviews ADD INDEX idx_reviews_item_status (item_id, status)",
            "ALTER TABLE reviews ADD INDEX idx_reviews_reviewer_status (reviewer_id, status)",
            "ALTER TABLE reviews ADD INDEX idx_reviews_seller_status (seller_id, status)",
            
            // Table referrals
            "ALTER TABLE referrals ADD INDEX idx_referrals_referrer_status (referrer_id, status)",
            "ALTER TABLE referrals ADD INDEX idx_referrals_referred_status (referred_id, status)",
            
            // Table point_transactions
            "ALTER TABLE point_transactions ADD INDEX idx_point_trans_user_created (user_id, created_at)",
            "ALTER TABLE point_transactions ADD INDEX idx_point_trans_type (type)",
        ];

        // Exécuter chaque requête SQL
        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Ignorer les erreurs de doublons ou d'index existants
                if (strpos($e->getMessage(), 'Duplicate key') === false && 
                    strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }

        // Créer l'index fulltext pour la recherche
        try {
            DB::statement("ALTER TABLE items ADD FULLTEXT INDEX idx_items_fulltext (name, description)");
        } catch (\Exception $e) {
            // Ignorer si l'index existe déjà
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            // Table items
            "ALTER TABLE items DROP INDEX idx_items_status_created",
            "ALTER TABLE items DROP INDEX idx_items_category_status",
            "ALTER TABLE items DROP INDEX idx_items_brand_status",
            "ALTER TABLE items DROP INDEX idx_items_user_status",
            "ALTER TABLE items DROP INDEX idx_items_price_currency",
            "ALTER TABLE items DROP INDEX idx_items_condition",
            "ALTER TABLE items DROP INDEX idx_items_fulltext",
            
            // Table orders
            "ALTER TABLE orders DROP INDEX idx_orders_buyer_status",
            "ALTER TABLE orders DROP INDEX idx_orders_seller_status",
            "ALTER TABLE orders DROP INDEX idx_orders_status_created",
            "ALTER TABLE orders DROP INDEX idx_orders_scan_token",
            
            // Table users
            "ALTER TABLE users DROP INDEX idx_users_google_id",
            "ALTER TABLE users DROP INDEX idx_users_apple_id",
            "ALTER TABLE users DROP INDEX idx_users_firebase_uid",
            "ALTER TABLE users DROP INDEX idx_users_email_verified",
            
            // Table messages
            "ALTER TABLE messages DROP INDEX idx_messages_conversation_created",
            "ALTER TABLE messages DROP INDEX idx_messages_sender_created",
            "ALTER TABLE messages DROP INDEX idx_messages_receiver_read",
            
            // Table notifications
            "ALTER TABLE notifications DROP INDEX idx_notifications_user_read",
            "ALTER TABLE notifications DROP INDEX idx_notifications_user_created",
            
            // Table wallet_transactions
            "ALTER TABLE wallet_transactions DROP INDEX idx_wallet_trans_wallet_created",
            "ALTER TABLE wallet_transactions DROP INDEX idx_wallet_trans_type_status",
            "ALTER TABLE wallet_transactions DROP INDEX idx_wallet_trans_status",
            
            // Table product_boosts
            "ALTER TABLE product_boosts DROP INDEX idx_boosts_item_status_expires",
            "ALTER TABLE product_boosts DROP INDEX idx_boosts_active_period",
            
            // Table reviews
            "ALTER TABLE reviews DROP INDEX idx_reviews_item_status",
            "ALTER TABLE reviews DROP INDEX idx_reviews_reviewer_status",
            "ALTER TABLE reviews DROP INDEX idx_reviews_seller_status",
            
            // Table referrals
            "ALTER TABLE referrals DROP INDEX idx_referrals_referrer_status",
            "ALTER TABLE referrals DROP INDEX idx_referrals_referred_status",
            
            // Table point_transactions
            "ALTER TABLE point_transactions DROP INDEX idx_point_trans_user_created",
            "ALTER TABLE point_transactions DROP INDEX idx_point_trans_type",
        ];

        foreach ($indexes as $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Ignorer les erreurs si l'index n'existe pas
            }
        }
    }
};
