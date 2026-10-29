<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model {
    protected $fillable = ['name', 'email', 'specialization', 'phone', 'room_no', 'is_active'];
    public function weeklyRoster() {
        return $this->hasOne(WeeklyRoster::class);
    }
    public function user() {
        return $this->hasOne(User::class, 'doctor_id');
    }
}
