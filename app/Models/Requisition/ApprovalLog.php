<?php

namespace App\Models\Requisition;


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
        'approved_at',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
