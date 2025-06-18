<?php

namespace App\Models\Api\V1;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Api\V1\ApiValue;
use App\Models\Api\V1\ApiReference;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiPlant extends Model
{

    protected $table = 'ehaloph_records';
    // use HasFactory;


    protected $casts = [
        'date' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', '=', 1);
        });
    }


    public function apiValues(): HasMany
    {
        return $this->hasMany(ApiValue::class, 'ehaloph_record_id');
    }

    // Convenience relationship to get fields through values
    public function apiFields()
    {
        return $this->hasManyThrough(
            ApiField::class,
            ApiValue::class,
            'ehaloph_record_id',  // Foreign key on api_value table
            'id',                 // Foreign key on api_field table
            'id',                 // Local key on api_plant table
            'ehaloph_field_id'    // Local key on api_value table
        );
    }


    public function references()
    {
        return $this->belongsToMany(
            ApiReference::class,
            'ehaloph_references_pivot',
            'plantID',
            'referenceID',
            'id',
            'id_pub'
        )->withPivot('field_associated', 'field_associatedID');
    }


    public function images()
    {
        return $this->hasMany(ApiImage::class, 'plantID');
    }

    public function approvedImages()
    {
        return $this->hasMany(ApiImage::class, 'plantID')
                    ->where('approved', 1)
                    ->where('is_pending', 0);
    }




    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {

        // see if the sort option is referenceing a field name. If so then order on that field name.
        $sort = request()->sort;
        if ($sort) {
            if (substr($sort,0, 1) == '-') {
                $dir = "desc";
                $sort = substr($sort, 1);
            } else {
                $dir = 'asc';
            }

            $fieldNames = ApiField::select('name')->get();
            $inCollection = $fieldNames->where('name', $sort);
            if (count($inCollection)) {
                $builder->join('ehaloph_values', 'ehaloph_records.id', '=', 'ehaloph_values.ehaloph_record_id')
                ->join('ehaloph_fields', 'ehaloph_values.ehaloph_field_id', '=', 'ehaloph_fields.id')
                ->where('ehaloph_fields.name', '=', $sort)
                ->orderBy('ehaloph_values.value', $dir)
                ->select('ehaloph_records.id');
            }
        }

        return $filters->apply($builder);

    }
}
