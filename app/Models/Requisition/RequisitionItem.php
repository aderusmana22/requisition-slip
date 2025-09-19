<?php

namespace App\Models\Requisition;

use App\Models\Master\ItemMaster;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_master_id',
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

    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_master_id');
    }
}
