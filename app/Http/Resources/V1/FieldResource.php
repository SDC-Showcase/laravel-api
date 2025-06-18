<?php

namespace App\Http\Resources\V1;


use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class FieldResource extends JsonResource
{

    use ApiResponses;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $ret = [
            'type' => 'field',
            'id' => $this->id,
            'position' => $this->position,
            'name' => $this->name,
            'question' => $this->question
        ];

        return $ret;

    }
}
