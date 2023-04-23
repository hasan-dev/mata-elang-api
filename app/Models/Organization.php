<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $table = 'organizations';

    public function roles() {
        return $this->hasMany(Role::class);
    }

    public function organization_members() {
        return $this->hasMany(OrganizationMember::class);
    }
}
