<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
use HasFactory;

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
     * Get the roles associated with the order status.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
