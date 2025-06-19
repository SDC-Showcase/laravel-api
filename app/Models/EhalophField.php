<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophField extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];
    protected $table = 'ehaloph_fields';
    protected $primaryKey = 'id';

    public function ehalophValues()
    {
        return $this->hasMany(EhalophValue::class);
    }


}
