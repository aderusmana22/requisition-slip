<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    protected $table = 'customers';


    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'customer_id');
    }

}
