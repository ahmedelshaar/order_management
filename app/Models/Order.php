<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'name',
        'age',
        'mobile_number',
        'nationality',
        'city',
        'company_name',
        'salary',
        'bank',
        'liabilities',
        'liabilities_amount',
        'installment',
        'car_brand',
        'car_name',
        'car_model',
        'notes',
        'user_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
