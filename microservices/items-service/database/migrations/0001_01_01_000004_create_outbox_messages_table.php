<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Outbox transactionnelle.
 *
 * Un `item.created` doit être publié alors que Redis peut être indisponible.
 * L'événement est donc d'abord écrit ici, dans la même transaction que la
 * mutation de l'article : soit les deux passent, soit aucun. La publication
 * Redis est ensuite rejouable depuis cette table.
 *
 * Ce n'est pas un pattern « publish after commit » : si le worker tombait
 * entre le commit SQL et la publication, l'événement serait perdu. Ici la
 * ligne EST la preuve de l'événement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('type', 64);
            $table->string('stream', 64);
            $table->json('payload');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['published_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_messages');
    }
};
