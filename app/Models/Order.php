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
        'is_saudi',
        'city',
        'company_name',
        'salary',
        'bank',
        'liabilities',
        'liabilities_amount',
        'car_brand',
        'car_name',
        'car_model',
        'notes',
        'user_id',
        'traffic_violations'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
