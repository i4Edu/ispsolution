<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'username',
        'password',
        'service_type',
        'package_id',
        'user_id', // This is likely the admin/operator who owns the customer
        'status',
        'mobile',
        'billing_profile_id',
        'package_expired_at',
        'tenant_id',
    ];

    protected $casts = [
        'package_expired_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($customer) {
            $customer->tenant_id = Auth::user()->tenant_id ?? 1; // Assuming default tenant_id if not set
            // The 'user_id' for customer ownership might be set explicitly or inferred from Auth::id()
            // Depending on the logic, 'user_id' could be the admin_id from Auth::id() or another user ID.
            // For now, leaving it to be set explicitly or through a relationship.
        });
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // User who owns this customer
    }

    public function billingProfile()
    {
        return $this->belongsTo(BillingProfile::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Assuming Customer has many payments and bills
    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function bills()
    {
        return $this->hasMany(CustomerBill::class);
    }
}