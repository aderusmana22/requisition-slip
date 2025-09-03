<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $table = 'revisions';

    protected $fillable = [
        'revision_number',
        'revision_count',
        'revision_date',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
