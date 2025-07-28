<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Siswa - JadwalKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(102, 126, 234, 0.4);
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body class="min-h-screen hero-gradient">
    <!-- Navigation -->
    <nav class="glass-effect fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-3xl font-bold gradient-text">
                            <i class="fas fa-graduation-cap mr-3 text-white"></i>
                            JadwalKu
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-white/80 text-sm font-medium">Portal Siswa</span>
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden pt-20">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl floating-animation"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center min-h-screen">
                <!-- Left Content -->
                <div class="px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
                    <div class="text-center lg:text-left">
                        <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight">
                            <span class="block">Selamat Datang di</span>
                            <span class="block bg-gradient-to-r from-yellow-300 to-pink-300 bg-clip-text text-transparent">
                                Portal Siswa
                            </span>
                        </h1>
                        <p class="mt-6 text-xl lg:text-2xl text-white/90 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            Akses jadwal pelajaran dan materi pembelajaran dengan mudah.
                            <span class="font-semibold text-yellow-300">Login dengan akun siswa Anda</span>
                            untuk melihat informasi terkini.
                        </p>

                        <!-- Stats -->
                        <div class="mt-10 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-yellow-300">24/7</div>
                                <div class="text-sm text-white/70">Akses</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-pink-300">100+</div>
                                <div class="text-sm text-white/70">Materi</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-300">Real-time</div>
                                <div class="text-sm text-white/70">Update</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
                    <div class="max-w-md mx-auto">
                        <div class="glass-effect p-8 rounded-3xl shadow-2xl">
                            <div class="text-center mb-8">
                                <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-pink-400 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-2xl text-white"></i>
                                </div>
                                <h2 class="text-3xl font-bold text-gray-800">
                                    Login Siswa
                                </h2>
                                <p class="mt-2 text-gray-600">
                                    Masuk dengan akun siswa Anda
                                </p>
                            </div>

                            @if ($errors->any())
                                <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-semibold text-red-800">
                                                Terjadi kesalahan:
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form class="space-y-6" action="{{ route('student.login') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Siswa
                                        </label>
                                        <input id="email" name="email" type="email" autocomplete="email" required
                                               class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 placeholder-gray-500"
                                               placeholder="Masukkan email siswa" value="{{ old('email') }}">
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                                        </label>
                                        <input id="password" name="password" type="password" autocomplete="current-password" required
                                               class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 placeholder-gray-500"
                                               placeholder="Masukkan password">
                                    </div>
                                </div>

                                <div>
                                    <button type="submit"
                                            class="btn-gradient w-full flex justify-center items-center py-4 px-6 text-white font-semibold rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-300">
                                        <i class="fas fa-sign-in-alt mr-3"></i>
                                        Masuk ke Portal
                                    </button>
                                </div>
                            </form>

                            <!-- Demo Credentials -->
                            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
                                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Contoh Login:
                                </h4>
                                <div class="text-sm text-gray-700 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Email:</span>
                                        <span class="text-blue-600 font-mono text-xs">ahmad.2024001@siswa.sekolah.com</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Password:</span>
                                        <span class="text-purple-600 font-mono text-xs">siswa2024001</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, #667eea 2px, transparent 2px); background-size: 50px 50px;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-purple-600 tracking-wide uppercase mb-4">Fitur Portal</h2>
                <h3 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6">
                    Semua yang Anda
                    <span class="gradient-text">butuhkan</span>
                </h3>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Portal siswa menyediakan akses mudah ke jadwal dan materi pembelajaran dengan interface yang modern dan user-friendly
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-4">Jadwal Pelajaran</h4>
                    <p class="text-gray-600 leading-relaxed">
                        Lihat jadwal pelajaran harian dan mingguan sesuai kelas Anda dengan tampilan yang jelas dan mudah dipahami
                    </p>
                </div>

                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-book text-2xl text-white"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-4">Materi Pembelajaran</h4>
                    <p class="text-gray-600 leading-relaxed">
                        Akses materi pembelajaran yang dibagikan oleh guru dengan sistem download yang cepat dan aman
                    </p>
                </div>

                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-4">Real-time Update</h4>
                    <p class="text-gray-600 leading-relaxed">
                        Informasi terkini tentang jadwal dan materi pembelajaran yang selalu update secara otomatis
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: linear-gradient(45deg, #667eea 25%, transparent 25%), linear-gradient(-45deg, #667eea 25%, transparent 25%); background-size: 30px 30px;"></div>
        </div>

        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div class="mb-8">
                    <h3 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-graduation-cap mr-3 text-yellow-400"></i>
                        JadwalKu
                    </h3>
                    <p class="text-gray-400 text-lg">Portal Siswa Modern</p>
                </div>

                <div class="flex justify-center space-x-6 mb-8">
                    <div class="text-center">
                        <i class="fas fa-shield-alt text-2xl text-green-400 mb-2"></i>
                        <p class="text-sm text-gray-400">Aman</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-bolt text-2xl text-yellow-400 mb-2"></i>
                        <p class="text-sm text-gray-400">Cepat</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-mobile-alt text-2xl text-blue-400 mb-2"></i>
                        <p class="text-sm text-gray-400">Responsif</p>
                    </div>
                </div>

                <div class="border-t border-gray-700 pt-8">
                    <p class="text-gray-400 text-sm">
                        &copy; 2025 JadwalKu. Portal Siswa untuk akses jadwal dan materi pembelajaran.
                        <br>
                        <span class="text-xs text-gray-500 mt-2 block">
                            Dibuat dengan ❤️ untuk kemudahan belajar siswa
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
