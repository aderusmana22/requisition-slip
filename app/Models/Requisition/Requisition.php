<?php

namespace App\Models\Requisition;

use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\Revision;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'requisitions';
    protected $guarded = ['id'];

    protected $fillable = [
        'requester_nik',
        'customer_id',
        'no_srs',
        'account',
        'cost_center',
        'request_date',
        'end_date',
        'revision_id',
        'category',
        'sub_category',
        'route_to',
        'status',
        'objectives',
        'estimated_potential',
        'reason_for_replacement',
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke Revision
    public function revision()
    {
        return $this->belongsTo(Revision::class, 'revision_id');
    }

    // Relasi ke RequisitionItem
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id');
    }


    // Relasi ke User (requester)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_nik', 'nik');
    }
}
