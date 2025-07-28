<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        @php
            $jadwalData = $this->getJadwalData();
            $kelasList = $this->getKelasList();
            $timeSlots = $this->getTimeSlots();
            $subjectColors = $this->getSubjectColors();
            $user = Auth::user();
            $isGuru = $user && \Spatie\Permission\Models\Role::whereHas('users', function($query) use ($user) {
                $query->where('model_id', $user->id);
            })->where('name', 'guru')->exists();
            $guru = $isGuru ? \App\Models\Guru::where('user_id', $user->id)->first() : null;
        @endphp

        @if($isGuru && $guru)
            <!-- Header Info untuk Guru -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-blue-200 dark:border-gray-600">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Jadwal Mengajar - {{ ucfirst($this->selectedHari) }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                            {{ $guru->nama_lengkap }} | {{ $guru->bidang_keahlian }} | {{ $this->selectedSemester }} {{ $this->selectedTahunAjaran }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Jadwal Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-6 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                            <span class="mr-3 text-3xl">📚</span>
                            Jadwal {{ ucfirst($this->selectedHari) }}
                        </h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mt-2 flex items-center">
                            <span class="mr-2">📅</span>
                            Semester {{ ucfirst($this->selectedSemester) }} - {{ $this->selectedTahunAjaran }}
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Kelas</div>
                            <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $kelasList->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                        <tr>
                            <th class="px-4 py-4 text-center text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-500 w-24">
                                <div class="flex items-center justify-center">
                                    <span class="mr-2">🕐</span>
                                    Jam
                                </div>
                            </th>
                            @foreach($kelasList as $kelas)
                                <th class="px-3 py-4 text-center text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-500 min-w-32">
                                    <div class="flex items-center justify-center">
                                        <span class="mr-1">🎓</span>
                                        {{ $kelas->nama_kelas }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @foreach($timeSlots as $time => $timeLabel)
                            @php
                                $isBreakTime = $time === '12:00:00';
                            @endphp
                            <tr class="{{ $isBreakTime ? 'bg-amber-25 dark:bg-amber-950' : '' }}">
                                <td class="px-4 py-4 text-sm font-bold {{ $isBreakTime ? 'text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-900' : 'text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700' }} border-r border-gray-300 dark:border-gray-500 w-24">
                                    <div class="text-center">
                                        <div class="font-bold text-sm">{{ $timeLabel }}</div>
                                        @if($isBreakTime)
                                            <div class="text-xs font-medium text-amber-700 dark:text-amber-300 mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-800 text-xs">
                                                    🍽️ Istirahat
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                @foreach($kelasList as $kelas)
                                    <td class="px-1 py-1 border-r border-gray-200 dark:border-gray-600 align-top h-16 relative min-w-28">
                                        @if($isBreakTime)
                                            <div class="h-full flex items-center justify-center">
                                                <span class="text-amber-600 dark:text-amber-400 text-xs font-medium">Istirahat</span>
                                            </div>
                                        @else
                                            @php
                                                $jadwal = $this->getJadwalForKelasAndTime($kelas->id, $time);
                                            @endphp
                                            @if($jadwal)
                                                @php
                                                    $mataPelajaran = $jadwal->mataPelajaran->nama_mata_pelajaran;
                                                    $colors = $subjectColors[$mataPelajaran] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300'];
                                                @endphp
                                                <div class="h-full {{ $colors['bg'] }} {{ $colors['border'] }} border-l-4 p-1 rounded text-xs shadow-sm">
                                                    @php
                                                        $kodeMapel = $this->getSubjectCode($mataPelajaran);
                                                    @endphp
                                                    <div class="font-bold {{ $colors['text'] }} text-xs leading-tight mb-1">
                                                        ({{ $kodeMapel }}) {{ \Illuminate\Support\Str::limit($mataPelajaran, 12) }}
                                                    </div>
                                                    <div class="text-xs {{ $colors['text'] }} opacity-75 leading-tight">
                                                        {{ \Illuminate\Support\Str::limit($jadwal->guru->user->name, 12) }}
                                                    </div>
                                                    <div class="text-xs {{ $colors['text'] }} opacity-75 leading-tight">
                                                        {{ $jadwal->ruangan->nama_ruangan }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $totalJadwal = $jadwalData->flatten()->count();

                if ($isGuru) {
                    // Statistics untuk guru - hanya jadwal mereka
                    $jadwalGuru = $jadwalData->flatten();
                    $totalJam = $jadwalGuru->sum(function($jadwal) {
                        $mulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
                        $selesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                        return $mulai->diffInMinutes($selesai);
                    });
                    $totalJamFormatted = floor($totalJam / 60) . ' jam ' . ($totalJam % 60) . ' menit';
                    $totalKelas = $jadwalGuru->pluck('kelas.nama_kelas')->unique()->count();
                } else {
                    // Statistics untuk admin - semua jadwal
                    $totalMataPelajaran = $jadwalData->flatten()->pluck('mata_pelajaran_id')->unique()->count();
                    $totalGuru = $jadwalData->flatten()->pluck('guru_id')->unique()->count();
                }
            @endphp

            @if($isGuru)
                <!-- Statistics untuk Guru -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jadwal Mengajar</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJadwal }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Jam Mengajar</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalJamFormatted ?? '0 jam' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kelas Diampu</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalKelas ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Statistics untuk Admin -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Jadwal</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJadwal }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mata Pelajaran</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMataPelajaran ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Guru</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalGuru ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
