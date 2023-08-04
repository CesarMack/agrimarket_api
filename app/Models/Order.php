<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Order extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_of_measurement_id',
        'total'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
