<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Get the products associated with the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Alias for products() relationship (Spanish).
     */
    public function productos()
    {
        return $this->products();
    }
}
