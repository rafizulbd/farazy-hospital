<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Update - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96 border-t-4 border-red-600">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Update Password</h2>
        <p class="text-gray-600 text-sm mb-6 text-center">For security reasons, you must change the default password provided by the Admin.</p>
        <form action="{{ route('force.password.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-500" required minlength="8">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-red-500" required minlength="8">
            </div>
            <button type="submit" class="w-full bg-red-600 text-white font-bold px-4 py-2 rounded hover:bg-red-700 transition">Update & Continue</button>
        </form>
    </div>
</body>
</html>
