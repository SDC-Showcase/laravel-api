<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophTree extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'ehaloph_trees';
    protected $primaryKey = 'id';
    public $timestamps = false;


}
