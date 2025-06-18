<?php

namespace App\Models;

use App\Builders\EhalophReferenceBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophReference extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'ehaloph_references';
    protected $primaryKey = 'id_pub';
    public $timestamps = false;


    public function newEloquentBuilder($query)
    {
        return new EhalophReferenceBuilder($query);
    }


    public function records()
    {
        return $this->belongsToMany(EhalophRecord::class, 'ehaloph_references_pivot', 'referenceID', 'plantID');
    }

}
