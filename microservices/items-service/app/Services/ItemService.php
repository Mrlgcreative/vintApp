<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

/**
 * Mutations du catalogue.
 *
 * Chaque mutation écrit son événement dans la même transaction que la
 * modification de la ligne : c'est ce qui garantit qu'un `item.updated`
 * existe si et seulement si le prix a réellement changé en base.
 */
class ItemService
{
    public function __construct(private readonly OutboxWriter $outbox) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes, int $sellerId): Item
    {
        return DB::transaction(function () use ($attributes, $sellerId) {
            $item = Item::create([
                'currency' => config('items.currency.default', 'USD'),
                'condition' => 'good',
                'status' => 'active',
                'quantity' => 1,
                'views' => 0,
                ...$attributes,
                // L'identité fait foi : un user_id fourni par le client est écrasé.
                'user_id' => $sellerId,
            ]);

            $this->outbox->write(OutboxWriter::ITEM_CREATED, $this->payload($item));

            return $item;
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Item $item, array $attributes): Item
    {
        return DB::transaction(function () use ($item, $attributes) {
            $item->fill($attributes);

            $changed = array_keys($item->getDirty());

            if ($changed === []) {
                return $item;
            }

            $item->save();

            $this->outbox->write(OutboxWriter::ITEM_UPDATED, [
                ...$this->payload($item),
                'changed' => array_values($changed),
            ]);

            return $item;
        });
    }

    public function delete(Item $item): void
    {
        DB::transaction(function () use ($item) {
            $payload = $this->payload($item);

            $item->delete();

            $this->outbox->write(OutboxWriter::ITEM_DELETED, $payload);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Item $item): array
    {
        return [
            'item_id' => $item->public_id,
            'seller_id' => (int) $item->user_id,
            'category_id' => $item->category_id,
            'brand_id' => $item->brand_id,
            'name' => $item->name,
            'price' => (float) $item->price,
            'currency' => $item->currency,
            'quantity' => (int) $item->quantity,
            'condition' => $item->condition,
            'status' => $item->status,
        ];
    }
}
