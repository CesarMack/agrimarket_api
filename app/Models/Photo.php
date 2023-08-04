<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Photo extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'product_id',
        'photo'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
