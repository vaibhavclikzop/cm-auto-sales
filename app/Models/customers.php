<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    use HasFactory;
    protected $table = "customers";
    protected $primaryKey = 'id';


    public function customerType()
    {
        return $this->belongsTo(customer_type::class, "customer_type_id");
    }

}
