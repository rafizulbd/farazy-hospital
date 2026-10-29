<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-lg shadow-xl w-96 border-t-4 border-blue-800">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900"><i class="fa-solid fa-hospital mr-2"></i>Farazy Hospital</h1>
            <p class="text-gray-500 mt-2 text-sm">Authorized Personnel Only</p>
        </div>
        
        @if (session('status'))
            <div class="mb-4 text-green-600 font-medium text-sm text-center bg-green-100 p-2 rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-3 top-3 text-gray-400"></i>
                    <input type="email" name="email" class="w-full pl-10 pr-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required autofocus>
                </div>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3 top-3 text-gray-400"></i>
                    <input type="password" name="password" class="w-full pl-10 pr-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox text-blue-600">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                <a href="{{ route('password.custom.request') }}" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
            </div>
            
            <button type="submit" class="w-full bg-blue-800 text-white font-bold py-2 rounded hover:bg-blue-900 transition">Secure Login</button>
        </form>
    </div>
</body>
</html>
