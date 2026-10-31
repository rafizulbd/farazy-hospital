<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade users table for Front Desk Staff
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id')) $table->string('employee_id')->nullable();
            if (!Schema::hasColumn('users', 'designation')) $table->string('designation')->default('Front Desk Executive');
            if (!Schema::hasColumn('users', 'department')) $table->string('department')->default('Front Desk Department');
            if (!Schema::hasColumn('users', 'allowed_shifts')) $table->text('allowed_shifts')->nullable();
            if (!Schema::hasColumn('users', 'is_active')) $table->boolean('is_active')->default(true);
            if (!Schema::hasColumn('users', 'avatar_url')) $table->string('avatar_url')->nullable();
        });

        // 2. Create or Upgrade system_settings table
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('hospital_name')->default('Farazy Hospital Ltd.');
                $table->string('hospital_tagline')->default('Committed to Excellence in Healthcare');
                $table->string('hospital_address')->default('House # 15-19, Block-E, Main Road, Banasree, Rampura, Dhaka-1219');
                $table->string('hospital_phone')->default('+880 1711-000000, 106xx');
                $table->string('hospital_email')->default('info@farazyhospital.com');
                $table->string('department_name')->default('Front Desk & Patient Care Services');
                $table->string('logo_url')->nullable();
                $table->string('favicon_url')->nullable();
                $table->text('notice_text')->nullable();
                $table->string('emergency_alert')->nullable();
                $table->string('roster_phase')->default('draft_open');
                $table->integer('target_month')->default(11);
                $table->integer('target_year')->default(2026);
                $table->string('validity_text')->default('November 2026 Official Duty Roster');
                $table->boolean('is_verified')->default(false);
                $table->string('verified_by')->nullable();
                $table->boolean('unlock_past_attendance')->default(false);
                $table->string('sig1_name')->default('Md. Rafizul Islam');
                $table->string('sig1_title')->default('In-Charge, Front Desk');
                $table->string('sig2_name')->default('HR Officer');
                $table->string('sig2_title')->default('Human Resources Dept.');
                $table->string('sig3_name')->default('Operations Manager');
                $table->string('sig3_title')->default('Hospital Operations');
                $table->string('sig4_name')->default('Hospital Director');
                $table->string('sig4_title')->default('Final Approving Authority');
                $table->timestamps();
            });
        } else {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'hospital_tagline')) $table->string('hospital_tagline')->nullable();
                if (!Schema::hasColumn('system_settings', 'hospital_phone')) $table->string('hospital_phone')->nullable();
                if (!Schema::hasColumn('system_settings', 'hospital_email')) $table->string('hospital_email')->nullable();
                if (!Schema::hasColumn('system_settings', 'favicon_url')) $table->string('favicon_url')->nullable();
                if (!Schema::hasColumn('system_settings', 'emergency_alert')) $table->string('emergency_alert')->nullable();
            });
        }

        // 3. Ensure shift_templates, weekly_rosters, daily_rosters, shift_swaps, activity_logs & handover_notes exist
        if (!Schema::hasTable('shift_templates')) {
            Schema::create('shift_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_code')->default('M');
                $table->string('start_time')->nullable();
                $table->string('end_time')->nullable();
                $table->integer('grace_minutes')->default(5);
                $table->boolean('is_off_day')->default(false);
                $table->string('color_theme')->default('emerald');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('weekly_rosters')) {
            Schema::create('weekly_rosters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('month');
                $table->integer('year');
                $table->string('sun_shift');
                $table->string('mon_shift');
                $table->string('tue_shift');
                $table->string('wed_shift');
                $table->string('thu_shift');
                $table->string('fri_shift');
                $table->string('sat_shift');
                $table->boolean('is_submitted')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('daily_rosters')) {
            Schema::create('daily_rosters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('duty_date');
                $table->string('day_name');
                $table->string('shift_name');
                $table->string('shift_start')->nullable();
                $table->string('shift_end')->nullable();
                $table->boolean('is_off_day')->default(false);
                $table->boolean('is_swapped')->default(false);
                $table->string('swap_info')->nullable();
                $table->string('in_time')->nullable();
                $table->boolean('in_locked')->default(false);
                $table->string('out_time')->nullable();
                $table->boolean('out_locked')->default(false);
                $table->string('attendance_status')->default('Pending');
                $table->integer('late_minutes')->default(0);
                $table->boolean('has_overtime')->default(false);
                $table->string('overtime_shift')->nullable();
                $table->decimal('overtime_hours', 5, 2)->default(0);
                $table->string('req_in_time')->nullable();
                $table->string('req_out_time')->nullable();
                $table->string('req_overtime_shift')->nullable();
                $table->decimal('req_overtime_hours', 5, 2)->nullable();
                $table->string('req_reason')->nullable();
                $table->string('update_status')->default('none');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'duty_date']);
            });
        }

        if (!Schema::hasTable('shift_swaps')) {
            Schema::create('shift_swaps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('requester_id');
                $table->unsignedBigInteger('target_user_id');
                $table->date('duty_date');
                $table->string('requester_shift');
                $table->string('target_shift');
                $table->string('reason')->nullable();
                $table->string('status')->default('pending_colleague');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->default('System');
                $table->string('action_type');
                $table->text('description');
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('handover_notes')) {
            Schema::create('handover_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('shift_name')->nullable();
                $table->string('priority')->default('normal');
                $table->text('message');
                $table->boolean('is_resolved')->default(false);
                $table->timestamps();
            });
        }

        // Seed default settings if empty
        if (DB::table('system_settings')->count() === 0) {
            DB::table('system_settings')->insert([
                'hospital_name' => 'Farazy Hospital Ltd.',
                'hospital_tagline' => 'Committed to Excellence in Healthcare',
                'hospital_address' => 'House # 15-19, Block-E, Main Road, Banasree, Rampura, Dhaka-1219',
                'hospital_phone' => '+880 1711-000000',
                'hospital_email' => 'frontdesk@farazyhospital.com',
                'department_name' => 'Front Desk & Patient Care Services',
                'notice_text' => 'OFFICIAL NOTICE: Submit 7-Day Draft Roster (6 Days Duty + 1 Day Off) by 29th. On 30th Drafts will be merged & open for mutual swap requests. On 31st Final Roster will be verified.',
                'roster_phase' => 'draft_open',
                'target_month' => 11,
                'target_year' => 2026,
                'validity_text' => 'November 2026 Master Roster Cycle',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed default shifts if empty
        if (DB::table('shift_templates')->count() === 0) {
            DB::table('shift_templates')->insert([
                ['name' => 'Morning (07:00 AM - 03:00 PM)', 'short_code' => 'M-7', 'start_time' => '07:00', 'end_time' => '15:00', 'grace_minutes' => 5, 'is_off_day' => false, 'color_theme' => 'emerald', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Evening (03:00 PM - 11:00 PM)', 'short_code' => 'E-3', 'start_time' => '15:00', 'end_time' => '23:00', 'grace_minutes' => 5, 'is_off_day' => false, 'color_theme' => 'amber', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Night (11:00 PM - 07:00 AM)', 'short_code' => 'N-11', 'start_time' => '23:00', 'end_time' => '07:00', 'grace_minutes' => 5, 'is_off_day' => false, 'color_theme' => 'indigo', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'General (09:00 AM - 05:00 PM)', 'short_code' => 'G-9', 'start_time' => '09:00', 'end_time' => '17:00', 'grace_minutes' => 5, 'is_off_day' => false, 'color_theme' => 'teal', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Weekly Off Day', 'short_code' => 'OFF', 'start_time' => null, 'end_time' => null, 'grace_minutes' => 0, 'is_off_day' => true, 'color_theme' => 'rose', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void {}
};
