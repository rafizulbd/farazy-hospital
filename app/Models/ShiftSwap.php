<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShiftSwap extends Model {
    protected $guarded = [];
    public function requester() {
        return $this->belongsTo(Doctor::class, 'requester_doctor_id');
    }
    public function target() {
        return $this->belongsTo(Doctor::class, 'target_doctor_id');
    }
}
