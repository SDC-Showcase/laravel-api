<?php

namespace App\Models\Api\V1;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Api\V1\ApiValue;
use App\Models\Api\V1\ApiPlant;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiReference extends Model
{

    protected $table = 'ehaloph_references';
    protected $primaryKey = 'id_pub';
    protected $hidden = ['searchterms'];
    use HasFactory;


    public function values(): HasMany
    {
        return $this->hasMany(ApiValue::class, 'ehaloph_record_id');
    }


    public function plants()
    {
        return $this->belongsToMany(
            ApiPlant::class,
            'ehaloph_references_pivot',
            'referenceID',
            'plantID',
            'id_pub',
            'id'
        )->withPivot('field_associated', 'field_associatedID');
    }



    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }
}
