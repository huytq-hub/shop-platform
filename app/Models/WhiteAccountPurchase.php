<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhiteAccountPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_id',
        'account_type',
        'quantity',
        'total_amount',
        'delivery_payload',
        'status',
        'note',
    ];

    protected $casts = [
        'delivery_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(WhiteAccountBatch::class, 'batch_id');
    }

    public function accounts()
    {
        return $this->hasMany(WhiteAccount::class, 'purchase_id');
    }
}





