<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecotype extends Model
{
    use HasFactory;

    protected $table = 'halophyte_ecotype';
    protected $primaryKey = 'id';
}
