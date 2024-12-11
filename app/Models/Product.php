<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_code', 'code');
    }
}
