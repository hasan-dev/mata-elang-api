<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',  
        'email',
        'address',
        'province',
        'city',
        'phone_number',
        'website',
        'oinkcode',
        'website',
        'parent_id',
    ];

    public function roles() {
        return $this->hasMany(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
