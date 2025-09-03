<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_master_id',
        'quantity',
        'quantity_issued',
        'batch_number',
        'reason_for_replacement',
        'objectives',
        'estimated_potential',
        'weight_selection',
        'packaging_selection',
        'sample_count',
        'purpose',
        'coa_required',
        'shipment_method',
        'source',
        'sample_notes',
        'production_date',
        'preparation_method',
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
