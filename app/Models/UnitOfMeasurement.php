<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class UnitOfMeasurement extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'name',
        'code'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
