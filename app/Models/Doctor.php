<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Doctor extends Model {
    protected $fillable = ['name', 'specialization', 'phone', 'room_no', 'is_active'];
}
