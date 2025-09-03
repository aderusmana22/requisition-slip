<?php

namespace App\Models\Requisition;

use App\Models\Master\Requisition;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'requisition_id',
        'payment_date',
        'document_url',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
