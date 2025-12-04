<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_master_id',
        'item_detail_id',
        'material_type',
        'quantity_required',
        'quantity_issued',
        'batch_number',
        'remarks',
    ];

    // Remove date cast for batch_number since it's now a string
    protected $casts = [
        // 'batch_number' => 'date', // Commented out - batch_number is now string
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function itemDetail()
    {
        return $this->belongsTo(ItemDetail::class, 'item_detail_id');
    }

    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_master_id');
    }
}
