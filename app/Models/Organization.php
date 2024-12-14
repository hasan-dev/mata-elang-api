<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;

class Organization extends Model implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'address',
        'province',
        'city',
        'phone_number',
        'website',
        'oinkcode',
        'parent_id',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'address',
            'province',
            'city',
            'phone_number',
            'website',
            'oinkcode',
            'parent_id',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user_role');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function database(): DatabaseConfig
    {
        // Ini contoh implementasi, mungkin perlu disesuaikan
        return new DatabaseConfig(
            $this->id,
            config('database.connections.tenant.database')
        );
    }

    public function getInternal(string $key)
    {
        return $this->getAttribute($key);
    }
    public function setInternal(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    // Implementasi method-method dari interface Tenant
    public function getTenantKeyName(): string
    {
        return 'id';
    }

    public function getTenantKey()
    {
        return $this->getAttribute($this->getTenantKeyName());
    }

    public function run(callable $callback)
    {
        // Implementasi run method
        return $this->execute($callback);
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Organization extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'name',  
//         'email',
//         'address',
//         'province',
//         'city',
//         'phone_number',
//         'website',
//         'oinkcode',
//         'website',
//         'parent_id',
//     ];

//     public function users()
//     {
//         return $this->belongsToMany(User::class, 'organization_user_role');
//     }

//     public function roles()
//     {
//         return $this->hasMany(Role::class);
//     }
// }
