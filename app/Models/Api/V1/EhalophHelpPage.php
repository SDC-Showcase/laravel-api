<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EhalophHelpPage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];
    protected $table = 'ehaloph_help_pages';
    protected $primaryKey = 'id';

}
