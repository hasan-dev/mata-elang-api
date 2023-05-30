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
        return $this->belongsToMany(User::class, 'organization_user_role');
    }

    public function organization() 
    {
        return $this->belongsTo(Organization::class);
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
