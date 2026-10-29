<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Roster extends Model {
    protected $fillable = ['doctor_id', 'duty_date', 'shift', 'status', 'notes'];
    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }
}
