<?php

namespace App\Models\Api\V1;

use App\Models\Api\V1\ApiField;
use App\Models\Api\V1\ApiPlant;
use App\Models\Api\V1\ApiReference;
use Illuminate\Database\Eloquent\Model;


class ApiReferencePivot extends Model
{
    protected $table = 'ehaloph_references_pivot';

    public function plant()
    {
        return $this->belongsTo(ApiPlant::class, 'plantID');
    }

    public function reference()
    {
        return $this->belongsTo(ApiReference::class, 'referenceID', 'id_pub');
    }

    public function field()
    {
        return $this->belongsTo(ApiField::class, 'field_associated');
    }
}
