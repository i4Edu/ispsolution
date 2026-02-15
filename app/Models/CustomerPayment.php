<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $table = 'customer_payments';

    protected $fillable = [
        'amount',
        'method',
        'customer_id',
        'admin_id',
        'tenant_id',
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            $payment->tenant_id = Auth::user()->tenant_id ?? 1;
            $payment->admin_id = Auth::id();
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}