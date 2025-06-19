<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\PlantFilter;
use App\Models\Api\V1\ApiPlant;
use App\Http\Resources\V1\PlantResource;

class PlantController extends ApiController
{

    /**
     * Get all plants
     *
     * @group Managing Plants
     */
    public function index(PlantFilter $filters)
    {
        $plants = ApiPlant::with([
            'apiValues.apiField',  // Eager load values and their fields
            'approvedImages'       // Eager load approved images
        ])->filter($filters)->paginate(15);  // Adjust pagination as needed

        // If using a resource collection
        return PlantResource::collection($plants);

    }


    /**
     * Show a specific plant
     *
     * @group Managing plants
     *
     */
    public function show($plantId)
    {

        $plant = ApiPlant::find($plantId);

        if (!$plant) {
            return $this->error("Record Not Found", 400);
        } else {

            $ret = new PlantResource(
                ApiPlant::with([
                    'apiValues.apiField',
                    'approvedImages'
                ])->findOrFail($plantId)
            );

            return $ret;

        }
    }

}
