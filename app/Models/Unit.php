<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'abreviacion'
    ];

    /**
     * Get the products associated with the unit.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
