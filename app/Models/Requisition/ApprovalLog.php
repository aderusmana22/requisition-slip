<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_logs';

    protected $fillable = [
        'requisition_id',
        'approver_nik',
        'status',
        'level',
        'token',
        'notes',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
