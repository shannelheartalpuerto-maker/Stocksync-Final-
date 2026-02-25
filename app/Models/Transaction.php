<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionItem;
use App\Models\User;

class Transaction extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id',
        'transaction_number',
        'total_amount',
        'payment_received',
        'change_returned',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
