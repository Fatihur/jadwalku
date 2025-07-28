@extends('student.layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
                        Jadwal Pelajaran
                    </h2>
                    <p class="text-gray-600 mt-1">Kelas {{ $siswa->kelas->nama_kelas }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Minggu ini</p>
                    <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Schedule Grid -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @php
                $hariMapping = [
                    'senin' => 'Senin',
                    'selasa' => 'Selasa', 
                    'rabu' => 'Rabu',
                    'kamis' => 'Kamis',
                    'jumat' => 'Jumat',
                    'sabtu' => 'Sabtu',
                    'minggu' => 'Minggu'
                ];
                
                $hariOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            @endphp

            @if($jadwalMingguIni->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($hariOrder as $hari)
                        @if(isset($jadwalMingguIni[$hari]) && $jadwalMingguIni[$hari]->count() > 0)
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <div class="w-3 h-3 bg-indigo-500 rounded-full mr-3"></div>
                                    {{ $hariMapping[$hari] }}
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $jadwalMingguIni[$hari]->count() }} pelajaran)</span>
                                </h3>
                                
                                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($jadwalMingguIni[$hari] as $jadwal)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-150">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 text-sm">
                                                        {{ $jadwal->mataPelajaran->nama_mata_pelajaran }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600">{{ $jadwal->guru->nama_lengkap }}</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $jadwal->jam_mulai }}
                                                </span>
                                            </div>
                                            
                                            <div class="space-y-1 text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <i class="fas fa-clock w-4 text-center mr-2"></i>
                                                    <span>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt w-4 text-center mr-2"></i>
                                                    <span>{{ $jadwal->ruangan->nama_ruangan ?? 'Ruangan TBA' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Jadwal</h3>
                    <p class="text-gray-500">Jadwal pelajaran untuk kelas {{ $siswa->kelas->nama_kelas }} belum tersedia.</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('student.dashboard') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Kembali ke Dashboard</p>
                        <p class="text-sm text-gray-500">Lihat ringkasan hari ini</p>
                    </div>
                </a>

                <a href="{{ route('student.materi') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Lihat Materi</p>
                        <p class="text-sm text-gray-500">Akses materi pembelajaran</p>
                    </div>
                </a>

                <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-print text-gray-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Cetak Jadwal</p>
                        <p class="text-sm text-gray-500">Segera tersedia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Toggle (if needed) -->
@endsection
