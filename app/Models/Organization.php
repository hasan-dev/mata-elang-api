<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    public function role() {
        return $this->hasMany(Role::class);
    }

    public function organization_member() {
        return $this->hasMany(OrganizationMember::class);
    }
}
