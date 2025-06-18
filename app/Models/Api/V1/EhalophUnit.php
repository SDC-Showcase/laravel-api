<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophUnit extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'ehaloph_units';
    protected $primaryKey = 'id';

//    public function ehalophRecords()
//    {
//        return $this->belongsTo(EhalophRecord::class);
//    }
//
//    public function ehalophFields()
//    {
//        // return $this->hasOne('\App\Models\EhalophField');
//        return $this->hasOne(EhalophField::class);
//    }

}
