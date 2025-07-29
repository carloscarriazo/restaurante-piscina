<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Get the products associated with the product type.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
