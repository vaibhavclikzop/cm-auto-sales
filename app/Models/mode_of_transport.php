<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mode_of_transport extends Model
{
    use HasFactory;
        protected $table = "mode_of_transport";
    protected $primaryKey = 'id';
}
