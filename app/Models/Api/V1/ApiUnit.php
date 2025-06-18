<?php

namespace App\Models\Api\V1;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Api\V1\ApiValue;

class ApiUnit extends Model
{

    protected $table = 'ehaloph_units';
    use HasFactory;

    public function apiValue(): HasMany
    {
        return $this->hasMany(ApiValue::class, 'field_id', 'ehaloph_field_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }
}
