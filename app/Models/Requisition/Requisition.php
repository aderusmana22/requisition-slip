<?php

namespace App\Models\Requisition;

use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\Revision;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'requisitions';

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
}
