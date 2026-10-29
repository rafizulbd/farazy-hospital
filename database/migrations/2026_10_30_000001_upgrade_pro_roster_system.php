<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('doctor');
            }
            if (!Schema::hasColumn('users', 'doctor_id')) {
                $table->unsignedBigInteger('doctor_id')->nullable();
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'email')) {
                $table->string('email')->nullable();
            }
        });

        Schema::dropIfExists('shift_swaps');
        Schema::dropIfExists('weekly_rosters');
        Schema::dropIfExists('roster_settings');

        Schema::create('roster_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Master Duty Roster Cycle');
            $table->string('validity_label')->default('Valid for 3 Months (Recurring 7-Day Shift Pattern)');
            $table->text('notice_text')->nullable();
            $table->string('current_phase')->default('draft_open'); // draft_open, swap_open, locked_final
            $table->string('phase1_date')->default('29th of Month');
            $table->string('phase2_date')->default('30th of Month');
            $table->string('phase3_date')->default('31st of Month');
            $table->string('approved_by')->nullable();
            $table->integer('active_month')->default(date('n'));
            $table->integer('active_year')->default(date('Y'));
            $table->timestamps();
        });

        Schema::create('weekly_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('sun_shift')->default('Morning (8AM-4PM)');
            $table->string('mon_shift')->default('Morning (8AM-4PM)');
            $table->string('tue_shift')->default('Evening (4PM-12AM)');
            $table->string('wed_shift')->default('Evening (4PM-12AM)');
            $table->string('thu_shift')->default('Night (12AM-8AM)');
            $table->string('fri_shift')->default('Night (12AM-8AM)');
            $table->string('sat_shift')->default('Off Day');
            $table->boolean('is_submitted')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shift_swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('target_doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('day_key'); // sun, mon, tue, wed, thu, fri, sat
            $table->string('requester_shift');
            $table->string('target_shift');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
        });

        // Admin role set kora ebong default Roster Notice toiri kora
        DB::table('users')->where('email', 'admin@farazy.com')->update(['role' => 'admin']);
        DB::table('roster_settings')->insert([
            'title' => 'Farazy Hospital Official Duty Roster',
            'validity_label' => 'Validity: 3 Months (8-Hour Weekly Repeating Schedule)',
            'notice_text' => '📌 জরুরি রোস্টার নোটিশঃ ২৯ তারিখ সকল ডাক্তার নিজ পছন্দের ৭ দিনের ড্রাফট রোস্টার সাবমিট করবেন। ৩০ তারিখ ড্রাফট মার্জ হবে এবং মিউচুয়াল রিকুইজিশনের মাধ্যমে কনফ্লিক্ট পরিবর্তন করা যাবে। ৩১ তারিখ ঊর্ধ্বতন কর্মকর্তার অনুমোদনের পর চূড়ান্ত রোস্টার লক করা হবে।',
            'current_phase' => 'draft_open',
            'phase1_date' => '29 Tarikh (Draft Entry)',
            'phase2_date' => '30 Tarikh (Merge & Swap)',
            'phase3_date' => '31 Tarikh (Final Approval)',
            'approved_by' => 'Pending Higher Authority Approval',
            'active_month' => date('n'),
            'active_year' => date('Y'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('shift_swaps');
        Schema::dropIfExists('weekly_rosters');
        Schema::dropIfExists('roster_settings');
    }
};
