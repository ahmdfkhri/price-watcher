<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    //
    protected $fillable = 
    [
        'name',
        'address',
        'latitude',
        'longitude'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Transaction::class,
            'store_id',        // Foreign key on transactions table
            'code',            // Foreign key on products table
            'id',              // Local key on stores table
            'product_code'     // Local key on transactions table
        );
    }
}
