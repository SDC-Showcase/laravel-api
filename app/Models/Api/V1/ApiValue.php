<?php

namespace App\Models\Api\V1;

use App\Models\Api\V1\ApiReferencePivot;
use App\Models\Api\V1\ApiPlant;
use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ApiValue extends Model
{

    protected $table = 'ehaloph_values';
    protected $hidden = ['ehaloph_record_id', 'ehaloph_field_id'];
    use HasFactory;

    public function apiPlant()
    {
        return $this->belongsTo(ApiPlant::class, 'ehaloph_record_id');
    }


    public function apiField(): BelongsTo
    {
        return $this->belongsTo(ApiField::class, 'ehaloph_field_id');
    }

    public function apiTree()
    {
        return $this->belongsTo(ApiTree::class)
            ->whereRaw('LOWER(ehaloph_trees.value) = ?', [strtolower($this->value)]);


    }

    public function apiUnit()
    {
        return $this->belongsTo(ApiUnit::class, 'field_id', 'unit_id');
    }

    public function references()
    {
        return $this->hasManyThrough(
            ApiReference::class,
            ApiReferencePivot::class,
            function ($join) {
                $join->on('api_references_pivot.field_associated', '=', 'api_value.ehaloph_field_id')
                     ->where('api_references_pivot.plantID', '=', 'api_value.ehaloph_record_id');
            },
            'id_pub',
            'id',
            'referenceID'
        )->where('api_references_pivot.field_associatedID', function ($query) {
            // This is a subquery to determine the position/index of this value
            // among all values of this field for this plant
            $query->select(DB::raw('COUNT(*) + 1'))
                  ->from('api_value as v')
                  ->where('v.ehaloph_field_id', '=', DB::raw('api_value.ehaloph_field_id'))
                  ->where('v.ehaloph_record_id', '=', DB::raw('api_value.ehaloph_record_id'))
                  ->where('v.id', '<', DB::raw('api_value.id'));
        });
    }



    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }
}
