<?php

namespace App\Http\Filters\V1;

use App\Models\EhalophField;

class PlantFilter extends QueryFilter {
    protected $sortable = [
        'id',
    ];


    public function id($value) {
        return $this->builder->where('id', $value);
    }

    public function catchAll($column, $value) {
        if ($column !== 'page') {
           self::matchValue($column, $value);
        }
    }



    public function matchValue($column, $value) {

        $ehaloph_field = EhalophField::where('name', '=', $column)->first();
        $isTree = $ehaloph_field->type == 'tree_1' ? true : false;


        $value = str_replace('*', '%', $value);

        if (!$isTree) {
            return $this->builder->whereHas('apiValues', function ($query) use($column, $value) {
                $query->where('value', 'like', $value)
                    ->whereRelation('apiField', 'name', '=', $column);
            });
        } else {
            return $this->builder->whereHas('apiValues', function ($query) use($column, $value) {
                $query->whereExists(function ($subquery) use ($value) {
                    $subquery->from('ehaloph_trees')
                             ->whereRaw('LOWER(ehaloph_trees.value) = LOWER(ehaloph_values.value)')
                             ->where(function($q) use ($value) {
                                 $q->where('ehaloph_trees.value', 'like', $value)
                                   ->orWhere('ehaloph_trees.text', 'like', $value);
                             });
                })
                ->whereRelation('apiField', 'name', '=', $column);
            });
        }

    }

    public function references($value) {
        return $this->builder->whereIn('references', explode(',', $value));
    }

    public function date($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            $dates[0] .= " 00:00:00";
            $dates[1] .= " 23:59:59";      // effectively make the date range inclusive
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }




    public function updated_at($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            $dates[0] .= " 00:00:00";
            $dates[1] .= " 23:59:59";      // effectively make the date range inclusive
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }


}