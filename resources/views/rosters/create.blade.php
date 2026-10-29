<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Duty Roster - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-blue-600">
        <h2 class="text-2xl font-bold mb-6 text-gray-800"><i class="fa-solid fa-calendar-plus text-blue-600 mr-2"></i>Create Duty Roster</h2>
        <form action="{{ route('rosters.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Select Doctor:</label>
                <select name="doctor_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" required>
                    <option value="">-- Choose a Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $doctor->specialization }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Duty Date:</label>
                    <input type="date" name="duty_date" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Shift:</label>
                    <select name="shift" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" required>
                        <option value="Morning (8 AM - 2 PM)">Morning (8 AM - 2 PM)</option>
                        <option value="Evening (2 PM - 8 PM)">Evening (2 PM - 8 PM)</option>
                        <option value="Night (8 PM - 8 AM)">Night (8 PM - 8 AM)</option>
                    </select>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Notes (Optional):</label>
                <textarea name="notes" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" rows="3"></textarea>
            </div>
            <div class="flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard</a>
                <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded hover:bg-blue-700 transition shadow">Save Roster</button>
            </div>
        </form>
    </div>
</body>
</html>
