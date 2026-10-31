@php
    $chartStats = $chartStats ?? ['present'=>0,'late'=>0,'absent'=>0,'off'=>0,'pending'=>0,'morning'=>0,'evening'=>0,'night'=>0,'general'=>0,'ot_hrs'=>0];
    $draftSubmittedCount = $draftSubmittedCount ?? 0;
    $activeStaffCount = $users->where('is_active', true)->count();
    $complianceRate = $activeStaffCount > 0 ? round(($draftSubmittedCount / $activeStaffCount) * 100) : 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Command Center | {{ $setting->hospital_name }}</title>
    @if(!empty($setting->favicon_url))
        <link rel="icon" href="{{ $setting->favicon_url }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">
    @include('partials.nav')

    <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">
        <!-- Toast Alerts -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-[#065f46] text-emerald-950 font-semibold rounded-2xl shadow-sm text-xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-[#065f46] text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="text-[10px] text-emerald-700 font-bold uppercase">Synced</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-50 border-l-4 border-rose-600 text-rose-950 font-semibold rounded-2xl shadow-sm text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- TOP BENTO HERO & KPI ROW -->
        <div class="bg-gradient-to-br from-[#053b2c] via-[#064e3b] to-[#022c22] text-white p-6 rounded-3xl shadow-xl border border-emerald-700/60">
            <div class="flex flex-wrap justify-between items-center gap-4 pb-5 border-b border-emerald-700/50">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full tracking-wider">
                            <i class="fa-solid fa-crown mr-1"></i> Executive ERP Command Center
                        </span>
                        <span class="bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            Cycle: {{ date('F', mktime(0,0,0,$setting->target_month,1)) }} {{ $setting->target_year }}
                        </span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-extrabold mt-2">{{ $setting->hospital_name }} &mdash; Front Desk Governance</h1>
                    <p class="text-xs text-emerald-200/90 mt-0.5">Real-time workforce analytics, central hospital branding, 2-stage swap approvals, and super-admin staff controls.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <button type="button" onclick="openNewUserModal()" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-4 py-2.5 rounded-xl shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus"></i> <span>+ Add Staff / Admin</span>
                    </button>
                    <a href="{{ route('rosters.index') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check text-emerald-300"></i> <span>Roster Matrix</span>
                    </a>
                    <a href="{{ route('report.print') }}" class="bg-amber-400 hover:bg-amber-500 text-slate-950 px-4 py-2.5 rounded-xl shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-print"></i> <span>A4 HR Print</span>
                    </a>
                </div>
            </div>

            <!-- 5 Bento KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 pt-5">
                <div class="bg-white/10 backdrop-blur border border-white/15 p-4 rounded-2xl">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-emerald-200 font-semibold">Active Workforce</span>
                        <i class="fa-solid fa-users text-emerald-300"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-white mt-1">{{ $activeStaffCount }} <small class="text-xs font-normal text-emerald-300">/ {{ $users->count() }}</small></div>
                    <div class="text-[10px] text-emerald-200/80 mt-1">Front Desk & Admin</div>
                </div>

                <div class="bg-white/10 backdrop-blur border border-white/15 p-4 rounded-2xl">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-emerald-200 font-semibold">29th Draft Compliance</span>
                        <i class="fa-solid fa-list-check text-amber-300"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-amber-300 mt-1">{{ $complianceRate }}%</div>
                    <div class="w-full bg-white/10 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-amber-400 h-full rounded-full" style="width: {{ min($complianceRate, 100) }}%"></div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur border border-white/15 p-4 rounded-2xl">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-emerald-200 font-semibold">Pending Swap Approvals</span>
                        <i class="fa-solid fa-people-arrows text-purple-300"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-white mt-1">{{ $pendingSwaps->count() }}</div>
                    <div class="text-[10px] text-purple-200 mt-1">2-Stage Mutual Queue</div>
                </div>

                <div class="bg-white/10 backdrop-blur border border-white/15 p-4 rounded-2xl">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-emerald-200 font-semibold">Time Correction Req.</span>
                        <i class="fa-solid fa-clock-rotate-left text-rose-300"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-white mt-1">{{ $pendingUpdates->count() }}</div>
                    <div class="text-[10px] text-rose-200 mt-1">In/Out Override Queue</div>
                </div>

                <div class="bg-white/10 backdrop-blur border border-white/15 p-4 rounded-2xl col-span-2 sm:col-span-1">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-emerald-200 font-semibold">Logged Overtime</span>
                        <i class="fa-solid fa-bolt text-sky-300"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-sky-300 mt-1">{{ $chartStats['ot_hrs'] }} <small class="text-xs font-normal">hrs</small></div>
                    <div class="text-[10px] text-emerald-200/80 mt-1">Current Cycle Total</div>
                </div>
            </div>
        </div>

        <!-- MODULE NAVIGATION TABS -->
        <div class="flex flex-wrap items-center justify-between gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-200 text-xs font-bold">
            <div class="flex flex-wrap gap-1.5">
                <button type="button" onclick="filterAdminTab('all')" id="tab-btn-all" class="admin-tab-btn bg-[#065f46] text-white px-4 py-2 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-grip mr-1.5"></i> All Modules
                </button>
                <button type="button" onclick="filterAdminTab('analytics')" id="tab-btn-analytics" class="admin-tab-btn hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl transition">
                    <i class="fa-solid fa-chart-pie mr-1.5 text-emerald-600"></i> Analytics & Approvals ({{ $pendingSwaps->count() + $pendingUpdates->count() }})
                </button>
                <button type="button" onclick="filterAdminTab('branding')" id="tab-btn-branding" class="admin-tab-btn hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl transition">
                    <i class="fa-regular fa-building mr-1.5 text-amber-600"></i> Central Branding, Logo & Favicon
                </button>
                <button type="button" onclick="filterAdminTab('users')" id="tab-btn-users" class="admin-tab-btn hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl transition">
                    <i class="fa-solid fa-users-gear mr-1.5 text-teal-700"></i> Staff Directory ({{ $users->count() }})
                </button>
                <button type="button" onclick="filterAdminTab('shifts')" id="tab-btn-shifts" class="admin-tab-btn hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl transition">
                    <i class="fa-solid fa-business-time mr-1.5 text-indigo-600"></i> Shift Builder & Audit Timeline
                </button>
            </div>
        </div>

        <!-- BENTO ROW 1: INTERACTIVE ANALYTICS CHARTS & LIVE APPROVAL QUEUE -->
        <div id="admin-sec-analytics" class="admin-sec grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Chart 1: Attendance Breakdown Doughnut (4 Cols) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-3">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Attendance Status Breakdown</h2>
                            <p class="text-[11px] text-slate-500">{{ date('F Y', mktime(0,0,0,$setting->target_month,1,$setting->target_year)) }} Cycle</p>
                        </div>
                        <i class="fa-solid fa-chart-pie text-[#065f46]"></i>
                    </div>
                    <div class="h-52 flex items-center justify-center">
                        <canvas id="attendanceDoughnutChart"></canvas>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-1.5 pt-3 border-t border-slate-100 text-center text-[10px] font-bold">
                    <div class="bg-emerald-50 text-emerald-800 p-1.5 rounded-lg">Present: {{ $chartStats['present'] }}</div>
                    <div class="bg-amber-50 text-amber-800 p-1.5 rounded-lg">Late: {{ $chartStats['late'] }}</div>
                    <div class="bg-rose-50 text-rose-800 p-1.5 rounded-lg">Off: {{ $chartStats['off'] }}</div>
                    <div class="bg-slate-100 text-slate-700 p-1.5 rounded-lg">Pending: {{ $chartStats['pending'] }}</div>
                </div>
            </div>

            <!-- Chart 2: Shift Load Distribution Bar Chart (4 Cols) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-3">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Shift Allocation Distribution</h2>
                            <p class="text-[11px] text-slate-500">Morning, Evening, Night & Off-Days</p>
                        </div>
                        <i class="fa-solid fa-chart-column text-indigo-600"></i>
                    </div>
                    <div class="h-52 flex items-center justify-center">
                        <canvas id="shiftBarChart"></canvas>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-500 font-semibold">
                    <span>Active Templates: <strong>{{ $shifts->count() }}</strong></span>
                    <span>Drafts Locked: <strong class="text-[#065f46]">{{ $draftSubmittedCount }} / {{ $activeStaffCount }}</strong></span>
                </div>
            </div>

            <!-- Unified Actionable Approval Center (4 Cols) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            1-Click Approval Queue
                        </h2>
                        <p class="text-[11px] text-slate-500">Swaps & Time-Clock Corrections</p>
                    </div>
                    <span class="bg-amber-100 text-amber-900 px-2.5 py-0.5 rounded-full text-xs font-extrabold">
                        {{ $pendingSwaps->count() + $pendingUpdates->count() }}
                    </span>
                </div>

                <div class="space-y-2.5 max-h-64 overflow-y-auto flex-1 pr-1">
                    @foreach($pendingUpdates as $up)
                    <div class="p-3 bg-amber-50/60 border border-amber-200 rounded-xl text-xs space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-extrabold text-slate-900">{{ $up->user->name ?? 'Staff' }}</span>
                            <span class="bg-amber-200 text-amber-950 text-[9px] font-bold px-2 py-0.5 rounded uppercase">Time Edit</span>
                        </div>
                        <div class="text-[11px] text-slate-600">
                            {{ \Carbon\Carbon::parse($up->duty_date)->format('d M') }}:
                            <strong>{{ $up->in_time ?: '--' }}-{{ $up->out_time ?: '--' }}</strong> &rarr;
                            <strong class="text-emerald-700">{{ $up->req_in_time }}-{{ $up->req_out_time }}</strong>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-[10px] text-slate-500 italic truncate max-w-[130px]">"{{ $up->req_reason }}"</span>
                            <div class="flex gap-1">
                                <form action="{{ route('attendance.admin.action', $up->id) }}" method="POST">
                                    @csrf <input type="hidden" name="action" value="approve_request">
                                    <button class="bg-[#065f46] text-white px-2.5 py-1 rounded-lg font-bold text-[10px]">Approve</button>
                                </form>
                                <form action="{{ route('attendance.admin.action', $up->id) }}" method="POST">
                                    @csrf <input type="hidden" name="action" value="reject_request">
                                    <button class="bg-rose-600 text-white px-2 py-1 rounded-lg font-bold text-[10px]">Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($pendingSwaps as $sw)
                    <div class="p-3 bg-purple-50/60 border border-purple-200 rounded-xl text-xs space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-extrabold text-slate-900">{{ \Carbon\Carbon::parse($sw->duty_date)->format('d M Y') }}</span>
                            <span class="bg-purple-200 text-purple-950 text-[9px] font-bold px-2 py-0.5 rounded uppercase">Shift Swap</span>
                        </div>
                        <div class="text-[11px] text-slate-700 font-semibold">
                            {{ $sw->requester->name ?? '' }} &harr; {{ $sw->targetUser->name ?? '' }}
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-[10px] text-purple-800 font-bold">{{ $sw->status === 'colleague_accepted' ? 'Colleague Accepted' : 'Pending Colleague' }}</span>
                            <div class="flex gap-1">
                                <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                    @csrf <input type="hidden" name="action" value="admin_approve">
                                    <button class="bg-purple-700 text-white px-2.5 py-1 rounded-lg font-bold text-[10px]">Approve</button>
                                </form>
                                <form action="{{ route('rosters.swap.respond', $sw->id) }}" method="POST">
                                    @csrf <input type="hidden" name="action" value="admin_reject">
                                    <button class="bg-rose-600 text-white px-2 py-1 rounded-lg font-bold text-[10px]">Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if($pendingUpdates->isEmpty() && $pendingSwaps->isEmpty())
                    <div class="text-xs text-slate-400 py-12 text-center">
                        <i class="fa-regular fa-circle-check text-emerald-500 text-2xl block mb-1.5"></i>
                        All swap & attendance requests are cleared!
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- BENTO ROW 2: CENTRAL HOSPITAL BRANDING, LOGO/FAVICON UPLOAD & SIGNATURES -->
        <div id="admin-sec-branding" class="admin-sec bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap justify-between items-center gap-2">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-regular fa-hospital text-[#065f46]"></i>
                        Central Hospital Identity, Direct Logo/Favicon Upload & A4 Signatures
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Upload official Logo and Favicon files directly from your device. Changes sync across Login, Navbar, Dashboards, and A4 Print Sheets.</p>
                </div>
                <span class="bg-emerald-50 border border-emerald-200 text-[#065f46] text-[11px] font-bold px-3 py-1 rounded-full">
                    <i class="fa-solid fa-bolt mr-1"></i> Global Live Sync
                </span>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf

                <!-- Live Preview Logo & Favicon Upload Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <!-- Logo Uploader -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center gap-4 shadow-sm">
                        <div class="h-20 w-20 rounded-2xl bg-slate-100 border-2 border-dashed border-emerald-400 flex items-center justify-center shrink-0 overflow-hidden p-1.5">
                            <img id="live-logo-preview" src="{{ $setting->logo_url ?: 'https://via.placeholder.com/80x80?text=Logo' }}" alt="Logo" class="h-full w-full object-contain {{ empty($setting->logo_url) ? 'hidden' : '' }}">
                            @if(empty($setting->logo_url))
                                <i id="live-logo-icon" class="fa-regular fa-hospital text-2xl text-emerald-700"></i>
                            @endif
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="block font-extrabold text-slate-900 text-xs">Hospital Official Logo (Browse File or URL)</label>
                            <input type="file" name="logo_file" accept=".png,.jpg,.jpeg,.webp,.svg" onchange="previewUploadedImage(this, 'live-logo-preview', 'live-logo-icon')" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#065f46] hover:file:bg-emerald-100 cursor-pointer">
                            <input type="text" name="logo_url" value="{{ $setting->logo_url }}" placeholder="Or paste direct Logo Image URL..." class="w-full border border-slate-300 p-1.5 rounded-lg text-[11px]">
                            @if(!empty($setting->logo_url))
                            <label class="inline-flex items-center gap-1.5 text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_logo" value="1"> <span>Remove current logo</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <!-- Favicon Uploader -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center gap-4 shadow-sm">
                        <div class="h-20 w-20 rounded-2xl bg-slate-100 border-2 border-dashed border-emerald-400 flex items-center justify-center shrink-0 overflow-hidden p-2">
                            <img id="live-fav-preview" src="{{ $setting->favicon_url ?: '' }}" alt="Favicon" class="h-10 w-10 object-contain {{ empty($setting->favicon_url) ? 'hidden' : '' }}">
                            @if(empty($setting->favicon_url))
                                <i id="live-fav-icon" class="fa-solid fa-globe text-2xl text-emerald-700"></i>
                            @endif
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="block font-extrabold text-slate-900 text-xs">Browser Tab Favicon (Browse .ico / .png)</label>
                            <input type="file" name="favicon_file" accept=".ico,.png,.jpg,.webp,.svg" onchange="previewUploadedImage(this, 'live-fav-preview', 'live-fav-icon')" class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#065f46] hover:file:bg-emerald-100 cursor-pointer">
                            <input type="text" name="favicon_url" value="{{ $setting->favicon_url }}" placeholder="Or paste direct Favicon URL..." class="w-full border border-slate-300 p-1.5 rounded-lg text-[11px]">
                            @if(!empty($setting->favicon_url))
                            <label class="inline-flex items-center gap-1.5 text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_favicon" value="1"> <span>Remove current favicon</span>
                            </label>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hospital Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hospital Name *</label>
                        <input type="text" name="hospital_name" value="{{ $setting->hospital_name }}" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-slate-50 focus:bg-white" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department Name *</label>
                        <input type="text" name="department_name" value="{{ $setting->department_name }}" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-slate-50 focus:bg-white" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hospital Tagline / Slogan</label>
                        <input type="text" name="hospital_tagline" value="{{ $setting->hospital_tagline ?? 'Committed to Excellence in Healthcare' }}" class="w-full border border-slate-300 p-2.5 rounded-xl bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hospital Full Address</label>
                        <input type="text" name="hospital_address" value="{{ $setting->hospital_address }}" class="w-full border border-slate-300 p-2.5 rounded-xl bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Front Desk Helpdesk Phone</label>
                        <input type="text" name="hospital_phone" value="{{ $setting->hospital_phone ?? '+880 1711-000000' }}" class="w-full border border-slate-300 p-2.5 rounded-xl bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Official Email Address</label>
                        <input type="text" name="hospital_email" value="{{ $setting->hospital_email ?? 'frontdesk@farazyhospital.com' }}" class="w-full border border-slate-300 p-2.5 rounded-xl bg-slate-50 focus:bg-white">
                    </div>
                </div>

                <!-- Roster Cycle & Phase -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block font-bold text-[#065f46] mb-1">Active Roster Window Phase</label>
                        <select name="roster_phase" class="w-full border border-emerald-400 p-2.5 rounded-xl font-extrabold bg-emerald-50 text-emerald-950">
                            <option value="draft_open" {{ $setting->roster_phase === 'draft_open' ? 'selected' : '' }}>29th Window: Draft Open</option>
                            <option value="merge_review" {{ $setting->roster_phase === 'merge_review' ? 'selected' : '' }}>30th Window: Merged & Swap Open</option>
                            <option value="final_verified" {{ $setting->roster_phase === 'final_verified' ? 'selected' : '' }}>31st Window: Final Verified</option>
                            <option value="closed" {{ $setting->roster_phase === 'closed' ? 'selected' : '' }}>Portal Locked / Closed</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Month</label>
                            <input type="number" min="1" max="12" name="target_month" value="{{ $setting->target_month }}" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Year</label>
                            <input type="number" name="target_year" value="{{ $setting->target_year }}" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Schedule Validity Title</label>
                        <input type="text" name="validity_text" value="{{ $setting->validity_text }}" class="w-full border border-slate-300 p-2.5 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Verified By Authority Label</label>
                        <input type="text" name="verified_by" value="{{ $setting->verified_by }}" class="w-full border border-slate-300 p-2.5 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Scrolling Notice Ticker Text</label>
                        <input type="text" name="notice_text" value="{{ $setting->notice_text }}" class="w-full border border-slate-300 p-2.5 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-rose-700 mb-1">Emergency Alert Top Banner (Optional)</label>
                        <input type="text" name="emergency_alert" value="{{ $setting->emergency_alert ?? '' }}" placeholder="Leave blank to hide emergency top bar..." class="w-full border border-rose-300 bg-rose-50/40 p-2.5 rounded-xl">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 bg-emerald-50/70 p-3.5 rounded-xl border border-emerald-200">
                    <label class="inline-flex items-center gap-2 font-bold text-emerald-950 cursor-pointer">
                        <input type="checkbox" name="is_verified" value="1" {{ $setting->is_verified ? 'checked' : '' }} class="rounded text-[#065f46] h-4 w-4">
                        <span>Display Official "Verified Schedule" Badge on All Dashboards</span>
                    </label>
                    <label class="inline-flex items-center gap-2 font-bold text-amber-900 cursor-pointer">
                        <input type="checkbox" name="unlock_past_attendance" value="1" {{ $setting->unlock_past_attendance ? 'checked' : '' }} class="rounded text-amber-600 h-4 w-4">
                        <span>Override 2nd-of-Month Midnight Lock (Allow Past Month Edits)</span>
                    </label>
                </div>

                <!-- 4 Dynamic HR Print Signatures -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <h3 class="font-extrabold text-slate-800 mb-3 uppercase text-[11px] flex items-center gap-1.5">
                        <i class="fa-solid fa-file-signature text-[#065f46]"></i>
                        A4 HR Printable Report Footer Signatures (4 Columns)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="bg-white p-2.5 rounded-xl border">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Signatory 1</span>
                            <input type="text" name="sig1_name" value="{{ $setting->sig1_name }}" class="w-full border p-1.5 rounded-lg font-bold mb-1">
                            <input type="text" name="sig1_title" value="{{ $setting->sig1_title }}" class="w-full border p-1.5 rounded-lg text-[11px]">
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Signatory 2</span>
                            <input type="text" name="sig2_name" value="{{ $setting->sig2_name }}" class="w-full border p-1.5 rounded-lg font-bold mb-1">
                            <input type="text" name="sig2_title" value="{{ $setting->sig2_title }}" class="w-full border p-1.5 rounded-lg text-[11px]">
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Signatory 3</span>
                            <input type="text" name="sig3_name" value="{{ $setting->sig3_name }}" class="w-full border p-1.5 rounded-lg font-bold mb-1">
                            <input type="text" name="sig3_title" value="{{ $setting->sig3_title }}" class="w-full border p-1.5 rounded-lg text-[11px]">
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Signatory 4</span>
                            <input type="text" name="sig4_name" value="{{ $setting->sig4_name }}" class="w-full border p-1.5 rounded-lg font-bold mb-1">
                            <input type="text" name="sig4_title" value="{{ $setting->sig4_title }}" class="w-full border p-1.5 rounded-lg text-[11px]">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-extrabold px-6 py-3 rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Central Branding, Logo/Favicon & Signatures</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- BENTO ROW 3: STAFF DIRECTORY WITH INSTANT SEARCH & SLIDE-OVER EDIT DRAWER -->
        <div id="admin-sec-users" class="admin-sec bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users-gear text-[#065f46]"></i>
                        Front Desk Team Directory & Super Access Account Manager
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Search staff, filter by role, or click "Edit Profile" to open the slide-over permission drawer.</p>
                </div>

                <!-- Instant Search & Add User Bar -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                        <input type="text" id="staff-search-input" onkeyup="filterStaffTable()" placeholder="Search name, ID, email..." class="pl-8 pr-3 py-2 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <select id="staff-role-filter" onchange="filterStaffTable()" class="border border-slate-300 rounded-xl px-3 py-2 font-bold bg-slate-50">
                        <option value="all">All Roles</option>
                        <option value="admin">Admins Only</option>
                        <option value="staff">Staff Only</option>
                    </select>
                    <button type="button" onclick="openNewUserModal()" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-bold px-4 py-2 rounded-xl shadow-sm">
                        + New Account
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-xs border border-slate-200 rounded-2xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-800 text-white uppercase text-[11px]">
                            <th class="p-3 text-left">Staff Member & ID</th>
                            <th class="p-3 text-left">Designation & Dept</th>
                            <th class="p-3 text-left">Login Email & WhatsApp</th>
                            <th class="p-3 text-center">Role & Status</th>
                            <th class="p-3 text-center">Super Admin Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staff-table-body">
                        @foreach($users as $u)
                        <tr class="staff-row border-b border-slate-200 hover:bg-slate-50/80 transition" data-role="{{ $u->role }}" data-search="{{ strtolower($u->name . ' ' . $u->employee_id . ' ' . $u->email . ' ' . $u->designation) }}">
                            <td class="p-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-9 w-9 rounded-xl bg-emerald-100 text-[#065f46] flex items-center justify-center font-extrabold text-xs shrink-0">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm">{{ $u->name }}</div>
                                        <span class="inline-block bg-emerald-50 border border-emerald-200 text-[#065f46] text-[10px] font-bold px-2 py-0.5 rounded">{{ $u->employee_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">
                                <div class="font-semibold text-slate-800">{{ $u->designation }}</div>
                                <div class="text-[11px] text-slate-500">{{ $u->department }}</div>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $u->email }}</div>
                                <div class="text-[11px] text-slate-500"><i class="fa-brands fa-whatsapp text-emerald-600 mr-1"></i>{{ $u->whatsapp_number ?: 'Not set' }}</div>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $u->role === 'admin' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                    {{ $u->role }}
                                </span>
                                @if(!$u->is_active)
                                    <span class="block text-[10px] text-rose-600 font-bold mt-1">Inactive</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex flex-wrap justify-center items-center gap-1.5">
                                    <a href="{{ route('rosters.index', ['user_id' => $u->id]) }}" class="bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 px-2.5 py-1.5 rounded-lg font-bold text-[11px]">
                                        <i class="fa-regular fa-calendar mr-1"></i>Roster
                                    </a>
                                    <a href="{{ route('report.print', ['user_id' => $u->id]) }}" class="bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 px-2.5 py-1.5 rounded-lg font-bold text-[11px]">
                                        <i class="fa-solid fa-print mr-1"></i>A4 Sheet
                                    </a>

                                    <button type="button"
                                        onclick='openEditUserDrawer(@json($u))'
                                        class="bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 px-2.5 py-1.5 rounded-lg font-bold text-[11px]">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i>Edit Profile
                                    </button>

                                    @if($u->id !== Auth::id())
                                    <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $u->name }}?');">
                                        @csrf
                                        <button class="bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 px-2.5 py-1.5 rounded-lg font-bold text-[11px]">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BENTO ROW 4: SHIFT BUILDER & VISUAL AUDIT TIMELINE -->
        <div id="admin-sec-shifts" class="admin-sec grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Custom Shift Builder -->
            <div class="lg:col-span-5 bg-white p-5 rounded-3xl shadow-sm border border-slate-200">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-business-time text-indigo-600"></i>
                    Custom Shift Builder & Grace Rules
                </h2>
                <p class="text-[11px] text-slate-500 mb-4">Define hospital shifts and grace minutes for automatic Late calculation.</p>

                <form action="{{ route('admin.shifts.store') }}" method="POST" class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 mb-4 space-y-2.5 text-xs">
                    @csrf
                    <input type="text" name="name" placeholder="Shift Title (e.g. Morning 07:00 AM - 03:00 PM)" class="w-full border border-slate-300 p-2 rounded-xl font-semibold bg-white" required>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500">Start Time</label>
                            <input type="time" name="start_time" class="w-full border p-1.5 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500">End Time</label>
                            <input type="time" name="end_time" class="w-full border p-1.5 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500">Grace (Mins)</label>
                            <input type="number" name="grace_minutes" value="5" class="w-full border p-1.5 rounded-lg bg-white font-bold">
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <label class="inline-flex items-center gap-1.5 font-bold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_off_day" value="1" class="rounded">
                            <span>Mark as Weekly Off Day</span>
                        </label>
                        <button type="submit" class="bg-slate-900 hover:bg-black text-white font-bold px-4 py-1.5 rounded-xl">
                            + Add Shift
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-800 text-white text-[11px]">
                                <th class="p-2 text-left">Shift Name</th>
                                <th class="p-2 text-center">Hours</th>
                                <th class="p-2 text-center">Grace</th>
                                <th class="p-2 text-center">Del</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $s)
                            <tr class="border-b">
                                <td class="p-2 font-bold text-slate-800">{{ $s->name }}</td>
                                <td class="p-2 text-center text-[11px]">{{ $s->start_time ? $s->start_time . '-' . $s->end_time : 'Off' }}</td>
                                <td class="p-2 text-center font-semibold">{{ $s->grace_minutes }}m</td>
                                <td class="p-2 text-center">
                                    <form action="{{ route('admin.shifts.delete', $s->id) }}" method="POST">
                                        @csrf <button class="text-rose-600 hover:text-rose-800 font-bold"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Visual Audit Log Timeline -->
            <div class="lg:col-span-7 bg-white p-5 rounded-3xl shadow-sm border border-slate-200 flex flex-col">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-[#065f46]"></i>
                            Visual Security & Activity Audit Log
                        </h2>
                        <p class="text-[11px] text-slate-500">Real-time log of punches, swaps, settings updates, and admin overrides.</p>
                    </div>
                    <input type="text" id="audit-search-input" onkeyup="filterAuditLogs()" placeholder="Filter logs..." class="border border-slate-300 rounded-xl px-3 py-1.5 text-xs bg-slate-50 outline-none">
                </div>

                <div id="audit-log-list" class="max-h-96 overflow-y-auto space-y-2 pr-1 flex-1">
                    @forelse($logs as $log)
                    <div class="audit-row p-2.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 flex items-start justify-between gap-3 text-xs" data-log="{{ strtolower($log->user_name . ' ' . $log->action_type . ' ' . $log->description) }}">
                        <div class="flex items-start gap-2.5">
                            <div class="h-7 w-7 rounded-lg bg-emerald-50 border border-emerald-200 text-[#065f46] flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-fingerprint text-[11px]"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-slate-900">{{ $log->user_name }}</span>
                                    <span class="bg-slate-100 border border-slate-300 text-slate-800 px-1.5 py-0.5 rounded font-bold text-[9px]">{{ $log->action_type }}</span>
                                </div>
                                <p class="text-slate-600 mt-0.5">{{ $log->description }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap">{{ $log->created_at->format('d M, h:i A') }}</span>
                    </div>
                    @empty
                    <div class="p-6 text-center text-slate-400 text-xs">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- SLIDE-OVER RIGHT DRAWER FOR EDITING ANY USER -->
    <div id="edit-user-backdrop" onclick="closeEditUserDrawer()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden"></div>
    <aside id="edit-user-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-5 bg-[#053b2c] text-white flex justify-between items-center">
            <div>
                <span class="text-[10px] uppercase tracking-wider text-amber-300 font-extrabold block">Super Admin Access</span>
                <h3 id="drawer-user-title" class="text-base font-extrabold">Edit Staff Profile</h3>
            </div>
            <button type="button" onclick="closeEditUserDrawer()" class="h-8 w-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="drawer-user-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" id="edit-name" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Employee ID</label>
                    <input type="text" name="employee_id" id="edit-emp-id" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">System Role</label>
                    <select name="role" id="edit-role" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold bg-slate-50">
                        <option value="staff">Front Desk Staff</option>
                        <option value="admin">Super Admin / In-Charge</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Designation</label>
                    <input type="text" name="designation" id="edit-designation" class="w-full border border-slate-300 p-2.5 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department</label>
                    <input type="text" name="department" id="edit-department" class="w-full border border-slate-300 p-2.5 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Official Login Email</label>
                <input type="email" name="email" id="edit-email" class="w-full border border-slate-300 p-2.5 rounded-xl font-bold" required>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" id="edit-whatsapp" class="w-full border border-slate-300 p-2.5 rounded-xl">
            </div>
            <div>
                <label class="block font-bold text-rose-600 mb-1">Reset Password (Optional)</label>
                <input type="text" name="new_password" placeholder="Leave blank to keep existing password..." class="w-full border border-rose-300 bg-rose-50/30 p-2.5 rounded-xl">
            </div>
            <div class="pt-2">
                <label class="inline-flex items-center gap-2 font-bold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit-active" value="1" class="rounded text-[#065f46] h-4 w-4">
                    <span>Account Active (Allow Portal Access)</span>
                </label>
            </div>
            <div class="pt-4 border-t border-slate-200 flex gap-2">
                <button type="submit" class="flex-1 bg-[#065f46] hover:bg-[#053b2c] text-white font-extrabold py-3 rounded-xl shadow">
                    Save User Changes
                </button>
                <button type="button" onclick="closeEditUserDrawer()" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl">
                    Cancel
                </button>
            </div>
        </form>
    </aside>

    <!-- NEW USER MODAL -->
    <div id="new-user-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-extrabold text-slate-900">+ Create New Staff / Admin Account</h3>
                <button type="button" onclick="document.getElementById('new-user-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="name" placeholder="Full Name *" class="border border-slate-300 p-2.5 rounded-xl font-bold" required>
                    <input type="text" name="employee_id" placeholder="Employee ID (e.g. FH-FD-005)" class="border border-slate-300 p-2.5 rounded-xl font-bold">
                    <input type="text" name="designation" value="Front Desk Executive" placeholder="Designation" class="border border-slate-300 p-2.5 rounded-xl">
                    <input type="email" name="email" placeholder="Official Email *" class="border border-slate-300 p-2.5 rounded-xl font-bold" required>
                    <input type="text" name="whatsapp_number" placeholder="WhatsApp Number" class="border border-slate-300 p-2.5 rounded-xl">
                    <input type="text" name="password" value="password123" placeholder="Initial Password" class="border border-slate-300 p-2.5 rounded-xl font-bold">
                </div>
                <div>
                    <span class="font-bold text-slate-700 block mb-1.5">Assign Allowed Shifts:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($shifts as $sh)
                            <label class="inline-flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border text-[11px] font-semibold">
                                <input type="checkbox" name="allowed_shifts[]" value="{{ $sh->name }}" checked>
                                <span>{{ $sh->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-between items-center pt-3 border-t">
                    <select name="role" class="border border-slate-300 p-2.5 rounded-xl font-bold">
                        <option value="staff">Role: Front Desk Staff</option>
                        <option value="admin">Role: Super Admin / In-Charge</option>
                    </select>
                    <button type="submit" class="bg-[#065f46] hover:bg-[#053b2c] text-white font-extrabold px-6 py-2.5 rounded-xl shadow">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Charts Initialization
        document.addEventListener('DOMContentLoaded', function() {
            var ctx1 = document.getElementById('attendanceDoughnutChart');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Late', 'Off Day', 'Pending', 'Absent'],
                        datasets: [{
                            data: [{{ $chartStats['present'] }}, {{ $chartStats['late'] }}, {{ $chartStats['off'] }}, {{ $chartStats['pending'] }}, {{ $chartStats['absent'] }}],
                            backgroundColor: ['#10b981', '#f59e0b', '#f43f5e', '#94a3b8', '#dc2626'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } } }
                });
            }

            var ctx2 = document.getElementById('shiftBarChart');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: ['Morning', 'Evening', 'Night', 'General', 'Off Days'],
                        datasets: [{
                            label: 'Assigned Days',
                            data: [{{ $chartStats['morning'] }}, {{ $chartStats['evening'] }}, {{ $chartStats['night'] }}, {{ $chartStats['general'] }}, {{ $chartStats['off'] }}],
                            backgroundColor: ['#059669', '#d97706', '#4f46e5', '#0d9488', '#e11d48'],
                            borderRadius: 6
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }
        });

        function filterAdminTab(tab) {
            var sections = ['analytics', 'branding', 'users', 'shifts'];
            sections.forEach(function(sec) {
                var el = document.getElementById('admin-sec-' + sec);
                if (!el) return;
                el.style.display = (tab === 'all' || tab === sec) ? '' : 'none';
            });
            document.querySelectorAll('.admin-tab-btn').forEach(function(btn) {
                btn.className = 'admin-tab-btn hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-xl transition';
            });
            var activeBtn = document.getElementById('tab-btn-' + tab);
            if (activeBtn) activeBtn.className = 'admin-tab-btn bg-[#065f46] text-white px-4 py-2 rounded-xl shadow-sm transition';
        }

        function openEditUserDrawer(user) {
            document.getElementById('drawer-user-title').textContent = 'Edit ' + user.name;
            document.getElementById('drawer-user-form').action = '/admin/users/' + user.id + '/update';
            document.getElementById('edit-name').value = user.name || '';
            document.getElementById('edit-emp-id').value = user.employee_id || '';
            document.getElementById('edit-role').value = user.role || 'staff';
            document.getElementById('edit-designation').value = user.designation || '';
            document.getElementById('edit-department').value = user.department || '';
            document.getElementById('edit-email').value = user.email || '';
            document.getElementById('edit-whatsapp').value = user.whatsapp_number || '';
            document.getElementById('edit-active').checked = !!user.is_active;

            document.getElementById('edit-user-backdrop').classList.remove('hidden');
            document.getElementById('edit-user-drawer').classList.remove('translate-x-full');
        }

        function closeEditUserDrawer() {
            document.getElementById('edit-user-backdrop').classList.add('hidden');
            document.getElementById('edit-user-drawer').classList.add('translate-x-full');
        }

        function openNewUserModal() {
            document.getElementById('new-user-modal').classList.remove('hidden');
        }

        function filterStaffTable() {
            var q = (document.getElementById('staff-search-input').value || '').toLowerCase();
            var role = document.getElementById('staff-role-filter').value;
            document.querySelectorAll('.staff-row').forEach(function(row) {
                var matchText = row.getAttribute('data-search').indexOf(q) !== -1;
                var matchRole = (role === 'all' || row.getAttribute('data-role') === role);
                row.style.display = (matchText && matchRole) ? '' : 'none';
            });
        }

        function filterAuditLogs() {
            var q = (document.getElementById('audit-search-input').value || '').toLowerCase();
            document.querySelectorAll('.audit-row').forEach(function(row) {
                row.style.display = row.getAttribute('data-log').indexOf(q) !== -1 ? '' : 'none';
            });
        }

        function previewUploadedImage(input, imgId, iconId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById(imgId);
                    if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
                    var ic = document.getElementById(iconId);
                    if (ic) ic.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
