<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex overflow-hidden font-sans">
    
    <!-- Sidebar -->
    <div class="w-64 bg-blue-900 text-white flex flex-col shadow-lg z-10">
        <div class="p-6 text-xl font-extrabold border-b border-blue-800 text-center flex items-center justify-center gap-2">
            <i class="fa-solid fa-hospital"></i> Farazy Hospital
        </div>
        <nav class="flex-1 p-4 space-y-3 mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 bg-blue-800 rounded-lg shadow transition">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('doctors.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-user-doctor"></i> Doctor Directory
            </a>
            <a href="{{ route('rosters.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-calendar-days"></i> Duty Roster
            </a>
        </nav>
        <div class="p-4 border-t border-blue-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-red-500 p-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-2 font-semibold shadow">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col relative">
        <header class="bg-white shadow-sm p-5 flex justify-between items-center z-0">
            <h2 class="text-2xl font-bold text-gray-800">Overview Dashboard</h2>
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 text-blue-800 w-10 h-10 flex items-center justify-center rounded-full font-bold text-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
            </div>
        </header>
        
        <main class="p-8 flex-1 overflow-y-auto bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Stat Card 1 -->
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500 flex justify-between items-center hover:shadow-lg transition">
                    <div>
                        <h3 class="text-gray-500 font-bold uppercase tracking-wider mb-1">Total Doctors</h3>
                        <p class="text-4xl font-extrabold text-gray-800">{{ \App\Models\Doctor::count() }}</p>
                    </div>
                    <div class="text-blue-500 text-5xl opacity-50">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500 flex justify-between items-center hover:shadow-lg transition">
                    <div>
                        <h3 class="text-gray-500 font-bold uppercase tracking-wider mb-1">Active Rosters</h3>
                        <p class="text-4xl font-extrabold text-gray-800">{{ \App\Models\Roster::count() }}</p>
                    </div>
                    <div class="text-green-500 text-5xl opacity-50">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 bg-white p-8 rounded-xl shadow-md">
                <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Quick Administrative Actions</h3>
                <div class="flex gap-4">
                    <a href="{{ route('doctors.create') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Add New Doctor
                    </a>
                    <a href="{{ route('rosters.create') }}" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus"></i> Create Duty Roster
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
