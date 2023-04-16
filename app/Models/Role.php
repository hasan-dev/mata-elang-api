<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function organization_member() {
        return $this->hasMany(OrganizationMember::class);
    }

    public function role_permission() {
        return $this->hasMany(RolePermission::class);
    }
}
