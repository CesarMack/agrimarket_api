<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'user_id',
        'product_type_id',
        'cutoff_date',
        'description',
        'price_per_measure',
        'stock',
        'minimum_sale',
        'unit_of_measurement_id',
        'active'
    ];

    public function unit_of_measurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
