<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRefund extends Model
{
    use HasFactory;

    protected $fillable = ['payment_id', 'amount', 'currency', 'provider_refund_id', 'status', 'reason'];

    protected $casts = ['amount' => 'integer'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
