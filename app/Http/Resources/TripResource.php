<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'  => $this->id,
            'date' => date('m/d/Y', strtotime($this->date)),
            'miles' => $this->miles,
            'total' => $this->total,
            'car' => new CarResource($this->car)
        ];
    }
}
