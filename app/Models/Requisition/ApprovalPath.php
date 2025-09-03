<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;

class ApprovalPath extends Model
{
    protected $table = 'approval_paths';

    protected $fillable = [
        'category',
        'sub_category',
        'sequence_approvers',
    ];


}
