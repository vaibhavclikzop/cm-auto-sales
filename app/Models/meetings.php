<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class meetings extends Model
{
    use HasFactory;
    protected $table = "meetings";
    protected $primaryKey = 'id';

    public function organizer()
    {
        return $this->belongsTo(users::class, "organizer_id");
    }

    public function customer()
    {
        return $this->belongsTo(customers::class, "customer_id");
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class, "meeting_id");
    }
}
