<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Add New Doctor</h2>
        <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Name:</label>
                <input type="text" name="name" class="w-full border p-2 rounded mt-1" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Specialization:</label>
                <input type="text" name="specialization" class="w-full border p-2 rounded mt-1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Phone:</label>
                <input type="text" name="phone" class="w-full border p-2 rounded mt-1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Room No:</label>
                <input type="text" name="room_no" class="w-full border p-2 rounded mt-1">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Doctor</button>
            <a href="{{ route('doctors.index') }}" class="ml-4 text-gray-600 hover:underline">Back to List</a>
        </form>
    </div>
</body>
</html>
