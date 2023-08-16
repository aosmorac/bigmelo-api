<?php

namespace App\Http\Resources\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'dealership' => ['id' => $this->dealership->id, 'name' => $this->dealership->name],
            'address'    => $this->address,
            'city'       => $this->city,
            'state'      => $this->state,
            'zip'        => $this->zip
        ];
    }
}
