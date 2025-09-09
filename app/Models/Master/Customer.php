<?php

namespace App\Models\Master;

use App\Models\Requisition\Requisition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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
