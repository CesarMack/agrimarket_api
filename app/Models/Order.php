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
        'client_id',
        'farmer_id',
        'product_id',
        'quantity',
        'unit_of_measurement_id',
        'total',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit_of_measurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if ($model->cantidad <= 0) {
                $model->active = false;
            }
        });
    }*/
}
