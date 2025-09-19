<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'foods';
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'is_active',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
