<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationMember extends Model
{
    use HasFactory;

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
