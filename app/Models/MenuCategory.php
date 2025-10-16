<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function availableItems()
    {
        return $this->hasMany(MenuItem::class)->where('is_available', true)->orderBy('sort_order');
    }
}
