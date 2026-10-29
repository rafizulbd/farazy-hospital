<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duty Roster - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-10 h-screen overflow-hidden flex flex-col">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-md w-full flex-1 flex flex-col">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600 text-xl"><i class="fa-solid fa-house"></i></a>
                <h2 class="text-3xl font-bold text-gray-800"><i class="fa-solid fa-calendar-days text-blue-600 mr-2"></i>Duty Roster</h2>
            </div>
            <a href="{{ route('rosters.create') }}" class="bg-blue-600 text-white font-semibold px-5 py-2 rounded hover:bg-blue-700 transition shadow flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Roster
            </a>
        </div>
        
        <div class="flex-1 overflow-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-200 sticky top-0">
                    <tr>
                        <th class="p-3 text-left font-bold text-gray-700 border-b">Date</th>
                        <th class="p-3 text-left font-bold text-gray-700 border-b">Doctor Name</th>
                        <th class="p-3 text-left font-bold text-gray-700 border-b">Shift</th>
                        <th class="p-3 text-left font-bold text-gray-700 border-b">Status</th>
                        <th class="p-3 text-left font-bold text-gray-700 border-b">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rosters as $roster)
                    <tr class="hover:bg-gray-50 border-b transition">
                        <td class="p-3 text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($roster->duty_date)->format('d M, Y') }}</td>
                        <td class="p-3 text-gray-800">{{ $roster->doctor->name ?? 'N/A' }}</td>
                        <td class="p-3 text-blue-600 font-semibold">{{ $roster->shift }}</td>
                        <td class="p-3">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full uppercase">{{ $roster->status }}</span>
                        </td>
                        <td class="p-3 text-gray-600 text-sm">{{ $roster->notes ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500 font-semibold italic">No rosters have been created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
