<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ItemDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_detail_id',
        'quantity_required',
        'quantity_issued',
        'batch_number',
        'remarks',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function itemDetail()
    {
        return $this->belongsTo(ItemDetail::class, 'item_detail_id');
    }
}
