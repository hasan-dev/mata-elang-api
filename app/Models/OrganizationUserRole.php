<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationUserRole extends Model
{
    use HasFactory;

    protected $table = 'organization_user_role';

    protected $fillable = [
        'organization_id',
        'user_id',
        'role_id',
    ];
}
