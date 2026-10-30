<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShiftSwap extends Model { protected $guarded = []; public function requester() { return $this->belongsTo(User::class, 'requester_id'); } public function targetUser() { return $this->belongsTo(User::class, 'target_user_id'); } }
