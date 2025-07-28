@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-3xl shadow-2xl p-8 mb-8 overflow-hidden relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
            </div>

            <div class="relative z-10 flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-user-graduate text-white text-3xl"></i>
                    </div>
                </div>
                <div class="ml-6">
                    <h2 class="text-4xl font-bold text-white mb-2">
                        Selamat Datang, {{ $siswa->nama_lengkap }}! 👋
                    </h2>
                    <p class="text-white/90 text-lg">
                        <i class="fas fa-graduation-cap mr-2"></i>Kelas {{ $siswa->kelas->nama_kelas }}
                        <span class="mx-3">•</span>
                        <i class="fas fa-calendar mr-2"></i>{{ ucfirst($hariIni) }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card-hover bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-day text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Jadwal Hari Ini
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $jadwalHariIni->count() }}
                            </p>
                            <p class="text-xs text-gray-500">Mata Pelajaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-hover bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Materi Terbaru
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $materiTerbaru->count() }}
                            </p>
                            <p class="text-xs text-gray-500">Materi Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-hover bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Kelas
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $siswa->kelas->nama_kelas }}
                            </p>
                            <p class="text-xs text-gray-500">Kelas Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Jadwal Hari Ini -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                        Jadwal Hari Ini ({{ ucfirst($hariIni) }})
                    </h3>
                    
                    @if($jadwalHariIni->count() > 0)
                        <div class="space-y-3">
                            @foreach($jadwalHariIni as $jadwal)
                                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded-r-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $jadwal->mataPelajaran->nama_mata_pelajaran }}</h4>
                                            <p class="text-sm text-gray-600">{{ $jadwal->guru->nama_lengkap }}</p>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $jadwal->ruangan->nama_ruangan ?? 'Ruangan TBA' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('student.jadwal') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Lihat jadwal lengkap →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada jadwal pelajaran hari ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Materi Terbaru -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-book text-green-500 mr-2"></i>
                        Materi Terbaru
                    </h3>
                    
                    @if($materiTerbaru->count() > 0)
                        <div class="space-y-3">
                            @foreach($materiTerbaru as $materi)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $materi->judul }}</h4>
                                            <p class="text-sm text-gray-600">{{ $materi->mataPelajaran->nama_mata_pelajaran }}</p>
                                            <p class="text-sm text-gray-500">{{ $materi->guru->nama_lengkap }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-gray-500">
                                                {{ $materi->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($materi->deskripsi)
                                        <p class="text-sm text-gray-600 mt-2">{{ Str::limit($materi->deskripsi, 100) }}</p>
                                    @endif
                                    @if($materi->files && count($materi->files) > 0)
                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            {{ count($materi->files) }} file lampiran
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('student.materi') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Lihat semua materi →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">Belum ada materi pembelajaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
