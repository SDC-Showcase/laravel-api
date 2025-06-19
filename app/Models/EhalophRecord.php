<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Spatie\MediaLibrary\HasMedia;

use Spatie\MediaLibrary\InteractsWithMedia;

class EhalophRecord extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['id', 'ja_id', 'value', 'ehaloph_record_id', 'ehaloph_field_id', 'active'];
    protected $guarded = [];
    protected $table = 'ehaloph_records';
    protected $primaryKey = 'id';


    public function references() {
        $ret = $this->belongsToMany(EhalophReference::class, 'ehaloph_references_pivot', 'plantID', 'referenceID')
                    ->withTimestamps();
        $ret = $ret->distinct();

        return($ret);
    }


    public function ehalophValues()
    {
        return $this->hasMany(EhalophValue::class);
    }



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


    public function registerMediaCollections() :void
    {
        $this
            ->addMediaCollection('images');
            // ->addMediaCollection('pending');
            // ->singleFile();

    }

    public function registerMediaConversions($media = null) :void
    {

        $this->addMediaConversion('thumb')
            ->width(180)
            ->height(180)
            ->sharpen(10)
            ->performOnCollections('images')
            ->nonQueued();

    }


}
