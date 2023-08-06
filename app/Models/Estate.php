<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Passport\HasApiTokens;

class Estate extends Model
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'street',
        'ext_num',
        'int_num',
        'suburb',
        'city',
        'state',
        'zip_code',
        'photo',
        'active'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
