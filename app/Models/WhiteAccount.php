<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhiteAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'account_name',
        'password',
        'login_method',
        'unit_price',
        'status',
        'buyer_id',
        'purchase_id',
        'sold_at',
        'note',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(WhiteAccountBatch::class, 'batch_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function purchase()
    {
        return $this->belongsTo(WhiteAccountPurchase::class, 'purchase_id');
    }
}





