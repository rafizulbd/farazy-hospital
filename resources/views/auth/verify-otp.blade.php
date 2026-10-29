<!DOCTYPE html>
<html lang="en"><head><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96 border-t-4 border-green-500">
        <h2 class="text-2xl font-bold mb-4 text-center">Verify OTP</h2>
        @if (session('success')) <div class="mb-4 text-green-600 text-sm text-center bg-green-100 p-2 rounded">{{ session('success') }}</div> @endif
        <form action="{{ route('otp.verify.post') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Enter 6-Digit Code</label>
                <input type="text" name="otp" class="w-full text-center tracking-widest text-2xl border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" required maxlength="6">
                @error('otp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded hover:bg-green-700 transition">Verify & Proceed</button>
        </form>
    </div>
</body></html>
