<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'billing_invoice_items';

    protected $fillable = ['billing_invoice_id', 'description', 'quantity', 'unit_amount', 'amount'];

    protected $casts = ['quantity' => 'integer', 'unit_amount' => 'integer', 'amount' => 'integer'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'billing_invoice_id');
    }
}
