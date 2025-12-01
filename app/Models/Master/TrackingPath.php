<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class TrackingPath extends Model
{
    protected $table = 'tracking_paths';

    protected $fillable = [
        'category',
        'sub_category',
        'sequence_approvers',
        'print_batch',
    ];

    protected $casts = [
        'sequence_approvers' => 'array',
    ];
}
