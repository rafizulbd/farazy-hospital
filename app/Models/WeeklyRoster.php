<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyRoster extends Model { protected $guarded = []; public function user() { return $this->belongsTo(User::class); } }
