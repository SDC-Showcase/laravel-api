<?php
namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\Api\V1\ApiValue;


class ApiTree extends Model
{
    protected $table = 'ehaloph_trees';

    // public function apiValues()
    // {
    //     return $this->hasMany(ApiTree::class)
    //         ->whereRaw('LOWER(ehaloph_trees.value) = ?', [strtolower($this->value)]);
    // }

    public function apiValues()
    {
        return $this->hasMany(ApiValue::class);
    }
}
