<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'description',
    ];

    /**
     * Get the roles associated with the payment method.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
