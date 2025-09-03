<?php

namespace App\Models\Requisition;

use App\Models\Master\Requisition;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'trackings';

    protected $fillable = [
        'requisition_id',
        'current_position',
        'last_updated',
        'notes',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
