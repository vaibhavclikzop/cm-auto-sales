<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    use HasFactory;
    protected $table = "meeting_participants";
    protected $primaryKey = 'id';

    public function user(){
        return $this->belongsTo(users::class,"user_id");
    }
        public function customer(){
        return $this->belongsTo(customers::class,"customer_id");
    }
}
