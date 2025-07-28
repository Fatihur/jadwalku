@extends('student.layouts.app')

@section('title', 'Materi Pembelajaran')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-book text-green-500 mr-2"></i>
                        Materi Pembelajaran
                    </h2>
                    <p class="text-gray-600 mt-1">Kelas {{ $siswa->kelas->nama_kelas }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total materi</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $materiList->total() }} Materi</p>
                </div>
            </div>
        </div>

        <!-- Materials List -->
        @if($materiList->count() > 0)
            <div class="space-y-6">
                @foreach($materiList as $materi)
                    <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition duration-150">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-3">
                                            {{ $materi->mataPelajaran->nama_mata_pelajaran }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $materi->created_at->locale('id')->isoFormat('D MMMM Y') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                        {{ $materi->judul }}
                                    </h3>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-3">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        <span>{{ $materi->guru->nama_lengkap }}</span>
                                    </div>
                                    
                                    @if($materi->deskripsi)
                                        <p class="text-gray-700 mb-4">{{ $materi->deskripsi }}</p>
                                    @endif
                                </div>
                                
                                <div class="ml-6 flex-shrink-0">
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">
                                            {{ $materi->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- File Attachments -->
                            @if($materi->files && count($materi->files) > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">
                                        <i class="fas fa-paperclip mr-1"></i>
                                        File Lampiran ({{ count($materi->files) }} file):
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($materi->files as $index => $filePath)
                                            @php
                                                $fileName = basename($filePath);
                                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                $iconClass = match($extension) {
                                                    'pdf' => 'fas fa-file-pdf text-red-500',
                                                    'doc', 'docx' => 'fas fa-file-word text-blue-500',
                                                    'ppt', 'pptx' => 'fas fa-file-powerpoint text-orange-500',
                                                    'xls', 'xlsx' => 'fas fa-file-excel text-green-500',
                                                    'mp4', 'avi', 'mov' => 'fas fa-file-video text-purple-500',
                                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-pink-500',
                                                    'zip', 'rar' => 'fas fa-file-archive text-yellow-500',
                                                    default => 'fas fa-file text-gray-500'
                                                };

                                                // Get file size if exists
                                                $fullPath = storage_path('app/public/' . $filePath);
                                                $fileSize = file_exists($fullPath) ? formatBytes(filesize($fullPath)) : 'Unknown';
                                            @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border hover:bg-gray-100 transition duration-150">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <i class="{{ $iconClass }} text-xl"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $fileName }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ strtoupper($extension) }} • {{ $fileSize }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('student.download.materi', ['materi' => $materi->id, 'fileIndex' => $index]) }}"
                                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                                        <i class="fas fa-download mr-1"></i>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-eye mr-1"></i>
                                            Dilihat
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $materi->created_at->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        @if($materi->file_path)
                                            <button class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                                <i class="fas fa-share mr-1"></i>
                                                Bagikan
                                            </button>
                                        @endif
                                        <button class="text-gray-600 hover:text-gray-500 text-sm font-medium">
                                            <i class="fas fa-bookmark mr-1"></i>
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $materiList->links() }}
            </div>
        @else
            <div class="bg-white shadow rounded-lg">
                <div class="text-center py-12">
                    <i class="fas fa-book-open text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Materi</h3>
                    <p class="text-gray-500 mb-6">Materi pembelajaran untuk kelas {{ $siswa->kelas->nama_kelas }} belum tersedia.</p>
                    
                    <div class="flex justify-center">
                        <a href="{{ route('student.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @endif

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

                <a href="{{ route('student.jadwal') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Lihat Jadwal</p>
                        <p class="text-sm text-gray-500">Cek jadwal pelajaran</p>
                    </div>
                </a>

                <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search text-gray-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Cari Materi</p>
                        <p class="text-sm text-gray-500">Segera tersedia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
