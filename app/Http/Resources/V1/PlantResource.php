<?php

namespace App\Http\Resources\V1;

use App\Http\Controllers\EhalophReferenceController;
use App\Models\Api\V1\ApiField;

use App\Models\Api\V1\ApiReference;
use App\Models\Api\V1\ApiReferencePivot;
use App\Models\Api\V1\ApiTree;
use App\Models\Api\V1\ApiUnit;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Schema;


class PlantResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // First check if this is part of a collection (index method)
        // or a single resource (show method)
        $isIndexView = $request->route()->getName() === 'api.plants.index' ||
                      ($this->resource instanceof LengthAwarePaginator ||
                       $this->resource instanceof Collection);

        // Use the date in ISO 8601 format with microseconds
        // This is Laravel's default format for date serialization
        $formattedDate = $this->date ? $this->date->toJSON() : null;
        $formattedUpdatedAt = $this->updated_at ? $this->updated_at->toJSON() : null;

        // get full text for reference labels
        $referenceLabels = EhalophReferenceController::get_ref_labels();

        $units = ApiUnit::all()->pluck('unit_text')->toArray();

        // For index view, return minimal data
        if ($isIndexView) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'date' => $formattedDate,
                'updated_at' => $formattedUpdatedAt,
                'image_count' => $this->whenLoaded('approvedImages', function() {
                    return $this->approvedImages->count();
                }, 0)
            ];
        }

        // For single plant view (full details)
        // Get fields with values for this plant
        $fieldsWithValues = $this->apiValues->pluck('ehaloph_field_id')->unique();

        // Get all reference pivots for this plant
        $referencePivots = ApiReferencePivot::where('plantID', $this->id)
            ->with(['reference', 'field'])
            ->get();

        // Find fields that have references but no values
        $fieldsWithReferencesOnly = $referencePivots
            ->pluck('field_associated')
            ->unique()
            ->diff($fieldsWithValues);

        // Combine both sets of field IDs
        $allFieldIds = $fieldsWithValues->merge($fieldsWithReferencesOnly)->unique();

        // Fetch all these fields
        $fields = ApiField::whereIn('id', $allFieldIds->toArray())
            ->orderBy('position')
            ->get();

        // Group reference pivots by field_associated and field_associatedID
        $groupedReferences = [];
        // Get the hidden columns from the ApiReference model
        $hiddenColumns = (new ApiReference())->getHidden();
        foreach ($referencePivots as $pivot) {
            $key = $pivot->field_associated . '_' . $pivot->field_associatedID;
            if (!isset($groupedReferences[$key])) {
                $groupedReferences[$key] = [];
            }

            $reference = $pivot->reference;
            $refData = ['id' => $reference->id_pub];

            // Get all columns from the reference table
            $columns = Schema::getColumnListing('ehaloph_references');
            $columns = array_diff($columns, ['id_pub']);
            $columns = array_diff($columns, $hiddenColumns);

            // Add all reference fields with full labels instead of abbreviations
            foreach ($columns as $column) {
                // If there's a label for this column, use it as the key, otherwise use the original column name
                $labelKey = isset($referenceLabels[$column]) ? $referenceLabels[$column] : $column;
                if ($reference->$column !== "") {
                   $refData[$labelKey] = $reference->$column;
                }
            }

            $groupedReferences[$key][] = $refData;
        }

        // Create a lookup of values by field ID
        $valuesByField = [];
        foreach ($this->apiValues as $value) {
            $fieldId = $value->ehaloph_field_id;
            if (!isset($valuesByField[$fieldId])) {
                $valuesByField[$fieldId] = [];
            }
            $valuesByField[$fieldId][] = $value;
        }

        // Pre-fetch all tree data needed to avoid lazy loading
        $treeValues = $this->apiValues
            ->filter(function($value) {
                return $value->apiField->type === 'tree_1';
            })
            ->pluck('value')
            ->unique();


        if ($treeValues instanceof \Illuminate\Support\Collection) {
            $treeValues = $treeValues->toArray();
        }

        $lowercaseTreeValues = array_map('strtolower', $treeValues);

        $trees = ApiTree::whereIn(DB::raw('LOWER(value)'), $lowercaseTreeValues)
            ->get()
            ->keyBy(function($item) {
                return strtolower($item->value);
            });


        // Process all fields
        $formattedFields = [];
        foreach ($fields as $field) {
            $fieldId = $field->id;
            $values = $valuesByField[$fieldId] ?? [];

            // Handle fields with no values but with references
            if (empty($values) && $fieldsWithReferencesOnly->contains($fieldId)) {
                $fieldData = [
                    'name' => $field->name,
                    'question' => $field->question,
                    'value' => null,
                ];

                // Find all references for this field
                foreach ($referencePivots as $pivot) {
                    if ($pivot->field_associated == $fieldId) {
                        $reference = $pivot->reference;
                        $refData = ['id' => $reference->id_pub];

                        foreach ($columns as $column) {
                            $labelKey = isset($referenceLabels[$column]) ? $referenceLabels[$column] : $column;
                            if ($reference->$column !== "") {
                               $refData[$labelKey] = $reference->$column;
                            }
                        }

                        $fieldData['references'][] = $refData;
                    }
                }

                $formattedFields[] = $fieldData;
                continue;
            }

            // Process fields with values
            foreach ($values as $index => $value) {
                $position = $index + 1;
                $refKey = $fieldId . '_' . $position;

                $fieldValueData = [
                    'name' => $field->name,
                    'question' => $field->question,
                ];

                // Safely handle tree values without lazy loading
                if ($field->type === 'tree_1' && isset($trees[strtolower($value->value)])) {
                    $fieldValueData['value'] = $trees[strtolower($value->value)]->text;
                } else {
                    $fieldValueData['value'] = $value->value;
                }

                if ($field->type === 'multi_pair_dup_types') {
                    $fieldValueData['unit'] = $units[$value->unit_id -1] ?? '';
                }

                if ($field->type === 'pair_text') {
                    $fieldValueData['unit'] = $units[$value->unit_id -1] ?? '';
                }


                if (isset($groupedReferences[$refKey])) {
                   $fieldValueData['references'] = $groupedReferences[$refKey] ?? [];
                }

                $formattedFields[] = $fieldValueData;
            }
        }

        return [
            'id' => $this->id,
            'date' => $formattedDate,
            'updated_at' => $formattedUpdatedAt,
            'fields' => $formattedFields,
            'images' => $this->whenLoaded('approvedImages', function() {
                return $this->approvedImages->map(function($image) {
                    return [
                        'id' => $image->id,
                        'fileName' => $image->fileName
                    ];
                });
            })
        ];
    }

}
