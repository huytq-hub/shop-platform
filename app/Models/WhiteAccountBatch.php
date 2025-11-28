<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhiteAccountBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'account_type',
        'default_price',
        'login_method',
        'stock_warning_threshold',
        'is_active',
        'note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accounts()
    {
        return $this->hasMany(WhiteAccount::class, 'batch_id');
    }

    public function purchases()
    {
        return $this->hasMany(WhiteAccountPurchase::class, 'batch_id');
    }
}





