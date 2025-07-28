<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Siswa') - JadwalKu</title>
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
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="glass-effect fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-3xl font-bold gradient-text">
                            <i class="fas fa-graduation-cap mr-3 text-purple-600"></i>
                            JadwalKu
                        </h1>
                    </div>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-8">
                            <a href="{{ route('student.dashboard') }}" 
                               class="nav-link {{ request()->routeIs('student.dashboard') ? 'active text-purple-600 font-semibold' : 'text-gray-600 hover:text-purple-600' }} px-3 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                            <a href="{{ route('student.jadwal') }}" 
                               class="nav-link {{ request()->routeIs('student.jadwal') ? 'active text-purple-600 font-semibold' : 'text-gray-600 hover:text-purple-600' }} px-3 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-calendar-alt mr-2"></i> Jadwal
                            </a>
                            <a href="{{ route('student.materi') }}" 
                               class="nav-link {{ request()->routeIs('student.materi') ? 'active text-purple-600 font-semibold' : 'text-gray-600 hover:text-purple-600' }} px-3 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-book mr-2"></i> Materi
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-800">{{ $siswa->nama_lengkap }}</div>
                        <div class="text-gray-500 text-xs">{{ $siswa->kelas->nama_kelas }}</div>
                    </div>
                    <form action="{{ route('student.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
        <div class="grid grid-cols-3 py-2">
            <a href="{{ route('student.dashboard') }}" 
               class="flex flex-col items-center py-2 {{ request()->routeIs('student.dashboard') ? 'text-purple-600' : 'text-gray-600' }}">
                <i class="fas fa-home text-lg mb-1"></i>
                <span class="text-xs">Dashboard</span>
            </a>
            <a href="{{ route('student.jadwal') }}" 
               class="flex flex-col items-center py-2 {{ request()->routeIs('student.jadwal') ? 'text-purple-600' : 'text-gray-600' }}">
                <i class="fas fa-calendar-alt text-lg mb-1"></i>
                <span class="text-xs">Jadwal</span>
            </a>
            <a href="{{ route('student.materi') }}" 
               class="flex flex-col items-center py-2 {{ request()->routeIs('student.materi') ? 'text-purple-600' : 'text-gray-600' }}">
                <i class="fas fa-book text-lg mb-1"></i>
                <span class="text-xs">Materi</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="pt-20 pb-20 md:pb-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
