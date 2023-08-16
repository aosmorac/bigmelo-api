<?php

namespace App\Http\Resources\Subscriber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'user'  => ['id' => $this->user->id, 'name' => $this->user->name],
        ];
    }
}