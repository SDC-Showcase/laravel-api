<?php

namespace App\Models\Api\V1;

use App\Models\ApiPlant;
use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiField extends Model
{

    protected $table = 'ehaloph_fields';
    protected $hidden = ['id', 'position', 'type'];


    public function apiValues(): HasMany
    {
        return $this->hasMany(ApiValue::class, 'ehaloph_field_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }
}
