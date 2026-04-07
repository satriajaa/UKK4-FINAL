<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - Ruang Baca</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles untuk layout fixed-scroll */
        .register-container {
            display: flex;
            min-height: 100vh;
            background: #f9fafb;
        }

        /* Side kiri - HIJAU STATIS (tidak ikut scroll) */
        .register-left {
            width: 50%;
            background: linear-gradient(135deg, #0d3d2e 0%, #166534 50%, #14532d 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            color: white;
            z-index: 10;
        }

        /* Custom scroll untuk side kiri (tetap hijau) */
        .register-left::-webkit-scrollbar {
            width: 6px;
        }

        .register-left::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .register-left::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        /* Side kanan - PUTIH BISA DI-SCROLL */
        .register-right {
            width: 50%;
            margin-left: 50%;
            background: white;
            min-height: 100vh;
            overflow-y: auto;
            position: relative;
            z-index: 5;
        }

        /* Custom scroll untuk side kanan */
        .register-right::-webkit-scrollbar {
            width: 8px;
        }

        .register-right::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .register-right::-webkit-scrollbar-thumb {
            background: #166534;
            border-radius: 10px;
        }

        .register-right::-webkit-scrollbar-thumb:hover {
            background: #0d3d2e;
        }

        /* Content wrapper */
        .register-content {
            max-width: 500px;
            margin: 0 auto;
            padding: 48px 32px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-left {
                display: none;
            }

            .register-right {
                width: 100%;
                margin-left: 0;
            }

            .register-content {
                padding: 32px 24px;
            }
        }

        /* Animasi fade in */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Styling form elements */
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #166534;
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.1);
            background: white;
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #166534 0%, #0d3d2e 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 101, 52, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Benefit list items */
        .benefit-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .benefit-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <!-- SISI KIRI - HIJAU STATIS -->
        <div class="register-left">
            <div class="register-content">
                <!-- Logo -->
                <div class="mb-12 fade-up" style="animation-delay: 0.1s">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-7 h-7 object-contain">
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span class="text-white text-xL font-bold tracking-wider">RUANG</span>
                            <span class="text-white text-xl font-bold tracking-tight">BACA</span>
                        </div>
                    </a>
                </div>

                <!-- Hero Text -->
                <div class="mb-12 fade-up" style="animation-delay: 0.2s">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Mulai Petualangan <br>
                        <span class="text-evergreen-200">Literasi</span> Anda
                    </h1>
                    <p class="text-white/70 text-lg leading-relaxed">
                        Bergabunglah dengan ribuan siswa yang sudah merasakan kemudahan mengakses koleksi buku digital
                        kapan saja, di mana saja.
                    </p>
                </div>

                <!-- Benefits -->
                <div class="space-y-3 mb-12 fade-up" style="animation-delay: 0.3s">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-graduation-cap text-white/80"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">Gratis untuk Siswa</h3>
                            <p class="text-white/50 text-sm">Akses penuh ke seluruh koleksi perpustakaan</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-book-open text-white/80"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">Pinjam Buku Online</h3>
                            <p class="text-white/50 text-sm">Sistem peminjaman digital yang mudah</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-bell text-white/80"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">Notifikasi Otomatis</h3>
                            <p class="text-white/50 text-sm">Pengingat jatuh tempo pengembalian</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                {{-- <div class="pt-8 border-t border-white/10 fade-up" style="animation-delay: 0.4s"> --}}
                <p class="text-white/40 text-sm">&copy; 2024 Ruang Baca Digital Library</p>
                {{-- <p class="text-white/30 text-xs mt-1">Versi 2.4.0</p> --}}
                {{-- </div> --}}
            </div>
        </div>

        <!-- SISI KANAN - PUTIH BISA DISCROLL -->
        <div class="register-right">
            <div class="register-content">
                <!-- Mobile Logo (hanya tampil di mobile) -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-2">
                        <div class="w-10 h-10 bg-evergreen-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-sm"></i>
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span class="text-gray-500 text-[10px] font-semibold tracking-wider">RUANG</span>
                            <span class="text-evergreen-700 text-lg font-bold">BACA</span>
                        </div>
                    </a>
                </div>

                <!-- Welcome Text -->
                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Daftar Akun Siswa</h2>
                    <p class="text-gray-500">Buat akun untuk mengakses perpustakaan digital sekolah Anda.</p>
                </div>

                <!-- Important Notice -->
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">Catatan Penting:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                <li>Akun akan diverifikasi oleh admin sekolah</li>
                                <li>Role otomatis di-set sebagai siswa</li>
                                <li>Anda akan menerima notifikasi setelah akun disetujui</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                        <div class="flex gap-3">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                            <div class="flex-1">
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                    @csrf

                    <!-- School Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-school text-gray-400 mr-1"></i>
                            Sekolah <span class="text-red-500">*</span>
                        </label>
                        <select name="school_id" id="school_id" class="form-input" required>
                            <option value="">Pilih Sekolah</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}"
                                    {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                            placeholder="Masukkan nama lengkap Anda" class="form-input" required>
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}"
                            placeholder="Pilih username unik" class="form-input" required>
                        <p class="mt-1 text-xs text-gray-400">Username harus unik dan tidak boleh sama dengan yang lain
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-gray-400">(Opsional)</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                            class="form-input">
                    </div>

                    <!-- Class Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas <span
                                class="text-red-500">*</span></label>
                        <select name="class_id" id="class_id" class="form-input" required disabled>
                            <option value="">Pilih Sekolah Terlebih Dahulu</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                                class="form-input pr-12" required>
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">
                            <p>Kata sandi minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka</p>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Ulangi kata sandi" class="form-input pr-12" required>
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="terms" name="terms"
                            class="w-4 h-4 mt-0.5 text-evergreen-600 rounded" required>
                        <label for="terms" class="text-sm text-gray-600">
                            Saya setuju dengan <a href="#"
                                class="text-evergreen-600 hover:text-evergreen-700 font-medium">Syarat & Ketentuan</a>
                            serta <a href="#"
                                class="text-evergreen-600 hover:text-evergreen-700 font-medium">Kebijakan Privasi</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus mr-2"></i>
                        DAFTAR SEKARANG
                    </button>
                </form>

                <!-- Login Link -->
                <p class="mt-8 text-center text-gray-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-evergreen-600 hover:text-evergreen-700 font-medium">
                        Masuk di Sini
                    </a>
                </p>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="{{ route('landing') }}"
                        class="text-sm text-gray-400 hover:text-evergreen-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = fieldId === 'password' ? document.getElementById('toggleIcon1') : document.getElementById(
                'toggleIcon2');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Script untuk Dependent Dropdown Kelas
        document.getElementById('school_id').addEventListener('change', function() {
            const schoolId = this.value;
            const classSelect = document.getElementById('class_id');

            // Reset dropdown kelas saat sekolah diganti
            classSelect.innerHTML = '<option value="">Memuat kelas...</option>';
            classSelect.disabled = true;

            // Kalau user milih "Pilih Sekolah" (kosong)
            if (!schoolId) {
                classSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
                return;
            }

            // Request AJAX pakai Fetch API
            fetch('{{ route("classes.by.school") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ school_id: schoolId })
            })
            .then(response => response.json())
            .then(data => {
                // Kosongin lagi, masukin opsi default
                classSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                // Looping data kelas dari database
                data.forEach(cls => {
                    // Filter biar template bawaan admin nggak ikut muncul
                    if(cls.name !== '__level_template__' && cls.name !== '__major_template__') {
                        classSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                    }
                });

                // Buka kuncian dropdown
                classSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                classSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
            });
        });
    </script>

</body>

</html>
