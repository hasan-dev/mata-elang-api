<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user_role')
            ->withPivot('organization_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user_role')
            ->withPivot('user_id');
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
