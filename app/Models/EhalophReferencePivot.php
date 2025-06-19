<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophReferencePivot extends Model
{
    // public $timestamps = false;
    protected $guarded = [];
    use HasFactory;
    protected $table = 'ehaloph_references_pivot';

}
