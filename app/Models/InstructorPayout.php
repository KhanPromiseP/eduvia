<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id', 'payout_method', 'account_name', 'account_number',
        'operator', 'currency', 'payout_threshold', 'auto_payout',
        'verification_data', 'is_verified'
    ];

    protected $casts = [
        'verification_data' => 'array',
        'auto_payout' => 'boolean',
        'is_verified' => 'boolean',
        'payout_threshold' => 'decimal:2'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Payout method constants
    const METHOD_MOBILE_MONEY = 'mobile_money';
    const METHOD_BANK_ACCOUNT = 'bank_account';
    const METHOD_WALLET = 'tranzak_wallet';

    public function getPayoutMethodDisplayAttribute()
    {
        return match($this->payout_method) {
            self::METHOD_MOBILE_MONEY => 'Mobile Money (' . $this->operator . ')',
            self::METHOD_BANK_ACCOUNT => 'Bank Transfer (' . $this->operator . ')',
            self::METHOD_WALLET => 'Tranzak Wallet',
            default => ucfirst($this->payout_method)
        };
    }

    public function getMaskedAccountNumberAttribute()
    {
        if ($this->payout_method === self::METHOD_MOBILE_MONEY) {
            return substr($this->account_number, 0, 4) . '****' . substr($this->account_number, -3);
        }
        
        if ($this->payout_method === self::METHOD_BANK_ACCOUNT) {
            return '****' . substr($this->account_number, -4);
        }
        
        return $this->account_number;
    }
}