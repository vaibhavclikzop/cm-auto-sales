<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class store extends Model
{
    use HasFactory;
    protected $table = "store";
    protected $primaryKey = 'id';

    public function warehouse(){
        return $this->belongsTo(warehouse::class,"warehouse_id");
    }
}
