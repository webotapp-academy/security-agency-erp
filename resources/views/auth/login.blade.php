<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - WeBotApp Security Agency Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 text-slate-800 antialiased text-[13px]">
    <div class="max-w-md w-full space-y-4">
        <!-- Logo and Title Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-slate-900 text-amber-400 font-bold text-xl shadow-sm mb-2 border border-slate-800">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">{{ \App\Models\Setting::get('software_logo_name', 'WeBotApp') }}</h1>
            <p class="text-xs text-slate-500 font-medium">Agency Operations & Guard Management Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
            @if (session('success'))
                <div class="mb-4 p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email', 'admin@webotapp.com') }}"
                           class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-medium text-slate-700">Password</label>
                    </div>
                    <input id="password" name="password" type="password" required autocomplete="current-password" value="password"
                           class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 focus:ring-0 text-xs">
                        <span class="ml-1.5 text-slate-600">Remember on this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-900 transition shadow-sm cursor-pointer">
                    Sign In to Dashboard
                </button>
            </form>

            <!-- Quick Demo Accounts -->
            <div class="mt-5 pt-4 border-t border-slate-200">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2 text-center">Quick Demo Login (Password: <span class="font-mono text-slate-700">password</span>)</p>
                <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                    <button type="button" onclick="fillCreds('admin@webotapp.com')" class="text-left px-2 py-1.5 rounded border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition">
                        <span class="font-semibold text-slate-800 block">Administrator</span>
                        <span class="text-slate-500 truncate block text-[10px]">admin@webotapp.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('hr@webotapp.com')" class="text-left px-2 py-1.5 rounded border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition">
                        <span class="font-semibold text-slate-800 block">HR Manager</span>
                        <span class="text-slate-500 truncate block text-[10px]">hr@webotapp.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('supervisor@webotapp.com')" class="text-left px-2 py-1.5 rounded border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition">
                        <span class="font-semibold text-slate-800 block">Site Supervisor</span>
                        <span class="text-slate-500 truncate block text-[10px]">supervisor@webotapp.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('accounts@webotapp.com')" class="text-left px-2 py-1.5 rounded border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition">
                        <span class="font-semibold text-slate-800 block">Accountant</span>
                        <span class="text-slate-500 truncate block text-[10px]">accounts@webotapp.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('operations@webotapp.com')" class="col-span-2 text-left px-2 py-1.5 rounded border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition">
                        <span class="font-semibold text-slate-800 block">Operations Manager</span>
                        <span class="text-slate-500 truncate block text-[10px]">operations@webotapp.com</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500">
            WeBotApp Security Agency Manager &bull; v1.0.0
        </p>
    </div>

    <script>
        function fillCreds(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
