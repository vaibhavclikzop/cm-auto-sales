<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class warehouse extends Model
{
    use HasFactory;
    protected $table = "warehouse";
    protected $primaryKey = 'id';

        public function company()
    {
        return $this->belongsTo(company::class, "company_id");
    }
}
