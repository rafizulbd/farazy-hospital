<!DOCTYPE html>
<html lang="en"><head><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96 border-t-4 border-blue-500">
        <h2 class="text-2xl font-bold mb-4 text-center">Set New Password</h2>
        <form action="{{ route('password.custom.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                <input type="password" name="password" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none" required minlength="8">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none" required minlength="8">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Update Password</button>
        </form>
    </div>
</body></html>
