<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EhalophImage extends Model
{
    use HasFactory;

    protected $table = 'ehaloph_images';

    protected $fillable = [
        'plantID',
        'fileName',
        'watermarktext',
        'is_pending',
        'approved'
    ];
}

