<?php

namespace App\Models\Master;

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
        return $this->belongsTo(\App\Models\Master\Customer::class, 'customer_id');
    }

    // Relasi ke Revision
    public function revision()
    {
        return $this->belongsTo(\App\Models\Master\Revision::class, 'revision_id');
    }
}
