<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class special_offer extends Model
{
    use HasFactory;
    protected $table = "special_offer";
    protected $primaryKey = 'id';

    public function product(){
        return $this->belongsTo(products::class,"product_id");
    }
}
