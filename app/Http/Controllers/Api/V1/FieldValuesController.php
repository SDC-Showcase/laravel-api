<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\FieldValuesResource;
use App\Models\Api\V1\ApiField;

use App\Models\EhalophField;
use App\Models\EhalophRecord;

class FieldValuesController extends ApiController
{

    public function getFieldValues($fieldName) {

        // get all the configured field names
        $fieldNames = ApiField::select('name')->get();
        // check if the given fieldname is valid
        $inCollection = $fieldNames->where('name', $fieldName);
        if (count($inCollection)) {

            $ehaloph_field = EhalophField::where('name', '=', $fieldName)->first();
            $isTree = $ehaloph_field->type == 'tree_1' ? true : false;
            if (!$isTree) {
                $fieldNames = EhalophRecord::select('ehaloph_values.value')->distinct()
                ->join('ehaloph_values', 'ehaloph_records.id', '=', 'ehaloph_values.ehaloph_record_id')
                ->join('ehaloph_fields', 'ehaloph_values.ehaloph_field_id', '=', 'ehaloph_fields.id')
                ->where('ehaloph_fields.name', '=', $fieldName)
                ->orderBy('ehaloph_values.value', 'asc')
                ->get();
            } else {
                $fieldNames = EhalophRecord::select('ehaloph_trees.text as value')->distinct()
                ->join('ehaloph_values', 'ehaloph_records.id', '=', 'ehaloph_values.ehaloph_record_id')
                ->join('ehaloph_fields', 'ehaloph_values.ehaloph_field_id', '=', 'ehaloph_fields.id')
                ->join('ehaloph_trees', 'ehaloph_values.value', '=', 'ehaloph_trees.value')
                ->where('ehaloph_fields.name', '=', $fieldName)
                ->orderBy('ehaloph_values.value', 'asc')
                ->get();
            }


        } else {
            return [
                'status' => 404,
                'message' => 'Invalid Field name'
            ];
        }


        return FieldValuesResource::collection($fieldNames);

    }

}