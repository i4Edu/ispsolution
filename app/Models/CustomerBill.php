<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerBill extends Model
{
    use HasFactory;

    protected $table = 'customer_bills';

    protected $fillable = [
        'amount',
        'bill_date',
        'due_date',
        'status',
        'customer_id',
        'admin_id',
        'tenant_id',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($bill) {
            $bill->tenant_id = Auth::user()->tenant_id ?? 1;
            $bill->admin_id = Auth::id();
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
