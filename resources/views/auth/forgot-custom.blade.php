<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96 border-t-4 border-yellow-500">
        <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Reset Password</h2>
        <p class="text-gray-500 text-sm mb-6 text-center">Enter your registered Email OR WhatsApp Number to receive a 6-digit OTP.</p>
        
        <form action="{{ route('password.custom.email') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Email or WhatsApp No.</label>
                <input type="text" name="identifier" placeholder="e.g. admin@farazy.com or 01700000000" class="w-full border p-2 rounded focus:ring-2 focus:ring-yellow-500 outline-none" required>
                @error('identifier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-white font-bold px-4 py-2 rounded hover:bg-yellow-600 transition">Send OTP Code</button>
        </form>
        <div class="text-center mt-4"><a href="{{ route('login') }}" class="text-sm text-gray-600 hover:underline">Back to Login</a></div>
    </div>
</body>
</html>
