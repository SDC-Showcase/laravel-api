<?php

namespace App\Http\Resources\V1;


use App\Models\Api\V1\ApiPlant;
use App\Models\Api\V1\ApiReference;

use App\Traits\ApiResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ReferenceResource extends JsonResource
{

    use ApiResponses;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $ret = [];
        $attributes = $this->getAttributes();
        foreach($attributes as $key => $value) {
            $ret[$key] = $value;
        }


        return $ret;

    }
}
