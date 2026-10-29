<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - Farazy Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg border-t-4 border-blue-700">
        <h2 class="text-2xl font-extrabold mb-2 text-slate-800"><i class="fa-solid fa-user-doctor text-blue-700 mr-2"></i>Add New Doctor & Login ID</h2>
        <p class="text-sm text-slate-500 mb-6">Providing an email will automatically create a login account for the doctor to submit their roster.</p>
        <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">Doctor Name *</label>
                    <input type="text" name="name" placeholder="Dr. Rahim Uddin" class="w-full border p-2.5 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">Specialization</label>
                    <input type="text" name="specialization" placeholder="Cardiology / Medicine" class="w-full border p-2.5 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">WhatsApp / Phone Number</label>
                    <input type="text" name="phone" placeholder="017XXXXXXXX" class="w-full border p-2.5 rounded-lg">
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">Chamber / Room No</label>
                    <input type="text" name="room_no" placeholder="Room 302" class="w-full border p-2.5 rounded-lg">
                </div>
            </div>
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 mb-6">
                <h4 class="font-bold text-blue-900 text-sm mb-3"><i class="fa-solid fa-key mr-1"></i> Doctor Portal Login Credentials</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-semibold text-xs mb-1">Login Email Address</label>
                        <input type="email" name="email" placeholder="doctor@farazy.com" class="w-full border p-2 rounded-lg bg-white">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-semibold text-xs mb-1">Initial Password (Default: password123)</label>
                        <input type="text" name="login_password" value="password123" class="w-full border p-2 rounded-lg bg-white">
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <a href="{{ route('doctors.index') }}" class="text-slate-600 font-semibold hover:underline">← Back to Directory</a>
                <button type="submit" class="bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg hover:bg-blue-800 shadow">Save Doctor & Create ID</button>
            </div>
        </form>
    </div>
</body>
</html>
