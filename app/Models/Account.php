<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'tenant_id',
        'admin_id',
        'code',
        'name',
        'type',
        'sub_type',
        'description',
        'parent_account_id',
        'debit_balance',
        'credit_balance',
        'balance',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($account) {
            $account->tenant_id = Auth::user()->tenant_id ?? 1;
            $account->admin_id = Auth::id();
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function parentAccount()
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function childAccounts()
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }
}