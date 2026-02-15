<?php

namespace App\Models\Freeradius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nas extends Model
{
    use HasFactory;

    protected $table = 'nas';

    protected $fillable = [
        'nasname',
        'shortname',
        'type',
        'secret',
        'api_username',
        'api_password',
        'api_port',
        'community',
        'description',
        'tenant_id',
        'admin_id',
    ];

    protected static function booted()
    {
        static::creating(function ($nas) {
            $nas->tenant_id = Auth::user()->tenant_id ?? 1; // Assuming default tenant_id if not set
            $nas->admin_id = Auth::id();
        });
    }

    // Define relationships here, e.g., with IpPools, PppoeProfiles, etc.
    public function ipv4Pools()
    {
        return $this->hasMany(\App\Models\Ipv4Pool::class, 'nas_id');
    }

    public function pppoeProfiles()
    {
        return $this->hasMany(\App\Models\PppoeProfile::class, 'nas_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }
}