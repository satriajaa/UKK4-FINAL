<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Superadmin - Ruang Baca</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-evergreen-900 min-h-screen">

    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-5">
        <div class="absolute top-20 left-20 w-96 h-96 bg-evergreen-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-evergreen-700 rounded-full blur-3xl"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="w-full max-w-md">
            
            <!-- Logo & Badge -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-evergreen-500 to-evergreen-700 rounded-2xl shadow-2xl mb-6 animate-pulse">
                    <i class="fas fa-shield-halved text-white text-3xl"></i>
                </div>
                <div class="inline-block px-4 py-2 bg-red-500/20 border border-red-500 rounded-full mb-4">
                    <span class="text-red-400 text-sm font-semibold flex items-center">
                        <i class="fas fa-lock mr-2"></i>
                        AKSES TERBATAS
                    </span>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Portal Superadmin</h1>
                <p class="text-gray-400">Akses khusus administrator sistem</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/50 p-4 rounded-lg backdrop-blur-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Login Card -->
            <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700 rounded-2xl shadow-2xl p-8">
                
                <!-- Warning Banner -->
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-6">
                    <div class="flex items-start text-yellow-400 text-sm">
                        <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                        <p>Halaman ini hanya dapat diakses oleh Superadmin. Semua aktivitas login akan direkam.</p>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('superadmin.login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user-shield mr-2"></i>Username Superadmin
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="{{ old('username') }}"
                                placeholder="Masukkan username superadmin"
                                class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-evergreen-500 focus:border-transparent transition placeholder-gray-500 @error('username') border-red-500 @enderror"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-key mr-2"></i>Kata Sandi
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••••••"
                                class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-evergreen-500 focus:border-transparent transition placeholder-gray-500 @error('password') border-red-500 @enderror"
                                required
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-300"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember"
                                class="w-4 h-4 text-evergreen-600 bg-gray-900 border-gray-600 rounded focus:ring-evergreen-500"
                            >
                            <span class="ml-2 text-sm text-gray-400">Ingat sesi ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-4 px-4 bg-gradient-to-r from-evergreen-600 to-evergreen-700 text-white rounded-lg font-bold hover:shadow-xl hover:shadow-evergreen-500/50 hover:scale-[1.02] transition duration-300 flex items-center justify-center space-x-2 uppercase tracking-wider"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Akses Superadmin</span>
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="flex items-start text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-evergreen-500 mr-2 mt-0.5"></i>
                        <p>Koneksi aman dengan enkripsi SSL. IP Anda: <span class="text-evergreen-400 font-mono">{{ request()->ip() }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm mb-2">Sistem Manajemen Perpustakaan Digital</p>
                <div class="flex items-center justify-center space-x-4 text-xs text-gray-600">
                    <span>&copy; 2024 Ruang Baca</span>
                    <span>•</span>
                    <span class="text-evergreen-400">Versi 2.4.0</span>
                    <span>•</span>
                    <span>Build 20240212</span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mt-6 text-center">
                <a href="mailto:support@ruangbaca.com" class="text-sm text-gray-500 hover:text-evergreen-400 transition">
                    <i class="fas fa-envelope mr-2"></i>
                    Lupa akses? Hubungi support@ruangbaca.com
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Security: Disable right-click and inspect
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.onkeydown = function(e) {
            if(e.keyCode == 123 || (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0))) {
                return false;
            }
        };
    </script>

</body>
</html>