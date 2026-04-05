<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Ruang Baca</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-evergreen-50 via-white to-evergreen-50 min-h-screen">

    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-evergreen-50 to-evergreen-100 p-12 items-center justify-center relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-64 h-64 bg-evergreen-400 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-evergreen-600 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 max-w-md">
                <!-- Logo -->
                       <a href="{{ route('landing') }}" class="inline-flex items-center space-x-3 mb-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-evergreen-500 to-evergreen-700 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Ruang Baca" class="w-10 h-10 object-contain">
                    </div>

                    <div class="flex flex-col leading-none font-bold text-2xl tracking-tight">
                        <span class="text-gray-900 mb-0.5">RUANG</span>
                        <span class="text-evergreen-600">BACA</span>
                    </div>
                </a>

                <!-- Hero Text -->
                <h1 class="text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Buka Potensi Anda <br>
                    Melalui <br>
                    <span class="text-evergreen-600">Membaca</span>
                </h1>

                <p class="text-gray-600 text-lg mb-8">
                    Sistem perpustakaan digital terpadu untuk sekolah modern. Memberdayakan siswa dan pendidik dengan akses mudah ke ribuan koleksi buku.
                </p>

                <!-- Image Section -->
                <div class="relative">
                    <div class="relative bg-gradient-to-br from-amber-200 to-amber-400 rounded-3xl p-6 shadow-2xl transform hover:scale-105 transition duration-300">
                        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=400"
                             alt="Tumpukan Buku"
                             class="w-full h-64 object-contain drop-shadow-2xl">
                    </div>

                    <!-- Active Members Badge -->
                    <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-xl p-3 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-evergreen-400 to-evergreen-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase">Anggota Aktif</div>
                            <div class="text-lg font-bold text-gray-900">12.5k+</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-12 text-sm text-gray-500">
                    <p>&copy; 2024 Sistem Ruang Baca</p>
                    <p class="text-evergreen-600">Versi 2.4.0</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ route('landing') }}" class="inline-flex items-center space-x-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-evergreen-500 to-evergreen-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold">
                            <span class="text-gray-900">RUANG</span>
                            <span class="text-evergreen-600">BACA</span>
                        </span>
                    </a>
                </div>

                <!-- Welcome Text -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang Kembali!</h2>
                    <p class="text-gray-600">Silakan masukkan detail Anda untuk mengakses dashboard.</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div class="flex-1">
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-evergreen-50 border-l-4 border-evergreen-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-evergreen-500 mt-0.5 mr-3"></i>
                            <p class="text-sm text-evergreen-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Email or Username -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                            Email atau Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                value="{{ old('login') }}"
                                placeholder="Masukkan email atau username Anda"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-evergreen-500 focus:border-transparent transition @error('login') border-red-500 @enderror"
                                required
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Kata Sandi
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm text-evergreen-600 hover:text-evergreen-700 font-medium">
                                Lupa Kata Sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                class="w-full pl-12 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-evergreen-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 text-evergreen-600 border-gray-300 rounded focus:ring-evergreen-500"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-700">
                            Ingat saya tetap masuk
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-3 px-4 bg-gradient-to-r from-evergreen-500 to-evergreen-600 text-white rounded-lg font-medium hover:shadow-lg hover:scale-[1.02] transition duration-300 flex items-center justify-center space-x-2"
                    >
                        <span>MASUK</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <!-- Divider -->
                    {{-- <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">ATAU LANJUTKAN DENGAN</span>
                        </div>
                    </div> --}}

                    <!-- Social Login -->
                    {{-- <div class="grid grid-cols-2 gap-4">
                        <button
                            type="button"
                            class="py-3 px-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition flex items-center justify-center space-x-2"
                        >
                            <i class="fab fa-google text-red-500"></i>
                            <span class="text-gray-700 font-medium">Google</span>
                        </button>
                        <button
                            type="button"
                            class="py-3 px-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition flex items-center justify-center space-x-2"
                        >
                            <i class="fab fa-apple text-gray-900"></i>
                            <span class="text-gray-700 font-medium">Apple</span>
                        </button>
                    </div> --}}
                </form>

                <!-- Register Link -->
                <p class="mt-8 text-center text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-evergreen-600 hover:text-evergreen-700 font-medium">
                        Daftar Sekarang
                    </a>
                </p>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="{{ route('landing') }}" class="text-sm text-gray-500 hover:text-evergreen-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
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
    </script>

</body>
</html>
