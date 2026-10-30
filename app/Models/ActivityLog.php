<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class ActivityLog extends Model {
    protected $guarded = [];
    public static function record($type, $desc) {
        $u = Auth::user();
        self::create([
            'user_id' => $u ? $u->id : null,
            'user_name' => $u ? $u->name . ' (' . ($u->employee_id ?? 'Staff') . ')' : 'System',
            'action_type' => $type,
            'description' => $desc,
            'ip_address' => request()->ip(),
        ]);
    }
}
