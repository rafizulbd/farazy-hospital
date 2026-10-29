<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Directory - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-lg"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
                <h2 class="text-2xl font-extrabold text-blue-900"><i class="fa-solid fa-user-doctor mr-2"></i>Doctor Directory</h2>
            </div>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('doctors.create') }}" class="bg-emerald-600 text-white font-bold px-5 py-2.5 rounded-lg hover:bg-emerald-700 shadow">+ Add Doctor</a>
            @endif
        </div>
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-100 text-emerald-800 rounded-lg font-semibold">{{ session('success') }}</div>
        @endif
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-800 text-white text-sm uppercase">
                    <th class="p-3 text-left">Doctor Name</th>
                    <th class="p-3 text-left">Specialization</th>
                    <th class="p-3 text-center">Room No</th>
                    <th class="p-3 text-center">WhatsApp / Phone</th>
                    <th class="p-3 text-center">Portal Login Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                <tr class="border-b hover:bg-slate-50">
                    <td class="p-3 font-bold text-slate-800">{{ $doctor->name }}</td>
                    <td class="p-3 text-slate-600">{{ $doctor->specialization ?: '-' }}</td>
                    <td class="p-3 text-center font-semibold text-blue-700">{{ $doctor->room_no ?: '-' }}</td>
                    <td class="p-3 text-center">{{ $doctor->phone ?: '-' }}</td>
                    <td class="p-3 text-center">
                        @if($doctor->email)
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">{{ $doctor->email }}</span>
                        @else
                            <span class="text-xs text-slate-400">No Login ID</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No doctors added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
