<?php

namespace App\Contexts\Device\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
            'sensor_type' => $this->sensor_type ? [
                'value' => $this->sensor_type->value,
                'label' => $this->sensor_type->label(),
                'unit' => $this->sensor_type->unit(),
            ] : null,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],
            'mac_address' => $this->mac_address,
            'ip_address' => $this->ip_address,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn() => [
                'uuid' => $this->parent->uuid,
                'name' => $this->parent->name,
                'type' => $this->parent->type->label(),
            ]),
            'children_count' => $this->whenLoaded('children', fn() => $this->children->count()),
            'metadata' => $this->metadata,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'is_online' => $this->isOnline(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
