<?php

namespace App\Http\Filters\V1;

class ReferenceFilter extends QueryFilter {
    protected $sortable = [
        'title',
        'status',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function createdAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }

    public function include($value) {
        return $this->builder->with($value);
    }

    public function status($value) {
        return $this->builder->whereIn('status', explode(',', $value));
    }

    public function references($value) {
        return $this->builder->whereIn('references', explode(',', $value));
    }



    public function title($value) {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('title', 'like', $likeStr);
    }

    public function updatedAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }


    public function catchAll($column, $value) {
        if ($column !== 'page') {
            self::matchValue($column, $value);
        }
    }

    public function matchValue($column, $value) {

        $value = str_replace('*', '%', $value);
        return $this->builder->whereHas('values', function ($query) use($column, $value) {
            $query->where('value', 'like', $value)
                  ->whereRelation('field', 'name', '=', $column);
        });

    }



}