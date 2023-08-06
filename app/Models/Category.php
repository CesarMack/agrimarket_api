<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Category extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'name',
        'active'
    ];

    public function product_types()
    {
        return $this->hasMany(ProductType::class);
    }
}
