<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Directory - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Doctor Directory</h2>
            <a href="{{ route('doctors.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Doctor</a>
        </div>
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Specialization</th>
                    <th class="border p-2">Room No</th>
                    <th class="border p-2">Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doctor)
                <tr class="text-center">
                    <td class="border p-2">{{ $doctor->name }}</td>
                    <td class="border p-2">{{ $doctor->specialization }}</td>
                    <td class="border p-2">{{ $doctor->room_no }}</td>
                    <td class="border p-2">{{ $doctor->phone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
