<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Halophyte extends Model
{
    protected $table = 'halophyte';
    use HasFactory;


    public function references() {
        $ret = $this->belongsToMany(EhalophReference::class, 'ehaloph_references', 'plantID', 'referenceID')
            ->withTimestamps();
        $ret = $ret->distinct();

        return($ret);
    }

    // define a scope to use with Eloquent to filter only current active records.
    public function scopeActive($query) {

        $where_clause = ' (status = 1 AND
                          ((halophyte.id IN (SELECT max(halophyte.id) FROM halophyte WHERE halophyte.status = 1 GROUP BY halophyte.ja_id)
                           AND halophyte.id NOT IN (SELECT halophyte.merged_from_ja_id FROM halophyte ))))
                        ';

        $query->whereRaw($where_clause);

        return $query;
    }


    public function getDateAttribute($value)
    {
        $ret = Carbon::parse($value)->format("M jS Y g:ia");

        return $ret;
    }


}
