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

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user_role')
            ->withPivot('role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'organization_user_role')
            ->withPivot('user_id');
    }
}
