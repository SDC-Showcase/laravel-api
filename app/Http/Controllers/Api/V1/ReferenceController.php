<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ReferenceFilter;
use App\Http\Resources\V1\ReferenceResource;
use App\Models\Api\V1\ApiReference;

class ReferenceController extends ApiController
{
    /**
     * Get all references
     */
    public function index(ReferenceFilter $filters)
    {
        return ReferenceResource::collection(ApiReference::filter($filters)->paginate());
    }


    /**
     * Show a specific reference.
     */
    public function show(ApiReference $reference)
    {
        // return new ReferenceResource($reference);
        return new ReferenceResource($reference);
    }

}
