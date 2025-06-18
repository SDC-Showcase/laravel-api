<?php

namespace App\Models\Api\V1;

use App\Http\Filters\V1\QueryFilter;
use App\Models\Api\V1\ApiPlant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ApiImage extends Model
{

    protected $table = 'ehaloph_images';

    public function plant(): BelongsTo
    {
        return $this->belongsTo(ApiPlant::class, 'plantID');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }


}
