<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserData extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'phone',
        'street',
        'ext_num',
        'int_num',
        'suburb',
        'city',
        'state',
        'zip_code',
        'photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
