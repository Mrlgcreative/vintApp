<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Configuration de l'application Laravel pour accéder à la base de données
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Vérification de la structure de la table messages:\n\n";

try {
    // Vérifier si la table messages existe
    if (Schema::hasTable('messages')) {
        echo "✅ Table 'messages' existe\n\n";
        
        // Récupérer la structure de la table
        $columns = DB::getSchemaBuilder()->getColumnListing('messages');
        echo "Colonnes de la table messages:\n";
        foreach ($columns as $column) {
            echo "- $column\n";
        }
        
        // Compter les messages dans la table
        $messageCount = DB::table('messages')->count();
        echo "\nNombre total de messages: $messageCount\n";
        
        // Compter les messages avec subject
        $subjectCount = DB::table('messages')->whereNotNull('subject')->count();
        echo "Messages avec subject: $subjectCount\n";
        
        // Compter les messages avec item_id
        $itemCount = DB::table('messages')->whereNotNull('item_id')->count();
        echo "Messages avec item_id: $itemCount\n";
        
        // Compter les messages avec subject ET item_id
        $bothCount = DB::table('messages')
                       ->whereNotNull('subject')
                       ->whereNotNull('item_id')
                       ->count();
        echo "Messages avec subject ET item_id: $bothCount\n";
        
        if ($messageCount > 0) {
            echo "\nExemples de messages:\n";
            $samples = DB::table('messages')
                        ->select('id', 'sender_id', 'receiver_id', 'subject', 'item_id', 'content', 'created_at')
                        ->limit(3)
                        ->get();
            
            foreach ($samples as $message) {
                echo "ID: {$message->id}, Sender: {$message->sender_id}, Receiver: {$message->receiver_id}\n";
                echo "Subject: " . ($message->subject ?? 'null') . "\n";
                echo "Item ID: " . ($message->item_id ?? 'null') . "\n";
                echo "Content: " . substr($message->content ?? '', 0, 50) . "...\n";
                echo "Date: {$message->created_at}\n";
                echo "---\n";
            }
        }
        
    } else {
        echo "❌ Table 'messages' n'existe pas\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}