<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Замовлення",
 *     required={"id", "user_id", "product_name", "amount", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_name", type="string", example="Laptop"),
 *     @OA\Property(property="amount", type="number", format="float", example=1500.00),
 *     @OA\Property(property="status", type="string", example="новий"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-04T09:45:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-04T09:45:00Z")
 * )
 */

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_name', 'amount', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
