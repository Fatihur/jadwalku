# 🔧 TECHNICAL DOCUMENTATION - JADWALKU

## 📋 **PROJECT STRUCTURE**

```
jadwalku/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   └── JadwalKalender.php
│   │   └── Resources/
│   │       ├── SiswaResource.php
│   │       ├── GuruResource.php
│   │       ├── JadwalResource.php
│   │       ├── MateriResource.php
│   │       ├── KelasResource.php
│   │       ├── MataPelajaranResource.php
│   │       ├── RuanganResource.php
│   │       └── TahunAjaranResource.php
│   ├── Http/Controllers/
│   │   └── StudentPortalController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Siswa.php
│   │   ├── Guru.php
│   │   ├── Kelas.php
│   │   ├── MataPelajaran.php
│   │   ├── Ruangan.php
│   │   ├── Jadwal.php
│   │   ├── Materi.php
│   │   └── TahunAjaran.php
│   └── Services/
│       └── GeneticAlgorithmService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
│       ├── student/
│       │   ├── landing.blade.php
│       │   ├── dashboard.blade.php
│       │   ├── jadwal.blade.php
│       │   └── materi.blade.php
│       └── filament/
└── routes/
    ├── web.php
    └── api.php (future)
```

---

## 🗄️ **DATABASE SCHEMA**

### **Core Tables:**

#### **users**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL, -- Compatibility with Filament
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    nomor_telepon VARCHAR(20) NULL,
    alamat TEXT NULL,
    tanggal_lahir DATE NULL,
    jenis_kelamin ENUM('L', 'P') NULL,
    is_active BOOLEAN DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### **kelas**
```sql
CREATE TABLE kelas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(100) NOT NULL,
    tingkat VARCHAR(10) NOT NULL,
    kapasitas INT DEFAULT 30,
    wali_kelas_id BIGINT UNSIGNED NULL,
    tahun_ajaran_id BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (wali_kelas_id) REFERENCES gurus(id),
    FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajarans(id)
);
```

#### **jadwals**
```sql
CREATE TABLE jadwals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kelas_id BIGINT UNSIGNED NOT NULL,
    mata_pelajaran_id BIGINT UNSIGNED NOT NULL,
    guru_id BIGINT UNSIGNED NOT NULL,
    ruangan_id BIGINT UNSIGNED NULL,
    tahun_ajaran_id BIGINT UNSIGNED NOT NULL,
    hari ENUM('senin','selasa','rabu','kamis','jumat','sabtu','minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id),
    FOREIGN KEY (mata_pelajaran_id) REFERENCES mata_pelajarans(id),
    FOREIGN KEY (guru_id) REFERENCES gurus(id),
    FOREIGN KEY (ruangan_id) REFERENCES ruangans(id),
    FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajarans(id)
);
```

### **Indexes for Performance:**
```sql
-- Jadwal table indexes
CREATE INDEX idx_jadwal_kelas_hari ON jadwals(kelas_id, hari);
CREATE INDEX idx_jadwal_guru_hari ON jadwals(guru_id, hari);
CREATE INDEX idx_jadwal_ruangan_hari ON jadwals(ruangan_id, hari);
CREATE INDEX idx_jadwal_tahun_ajaran ON jadwals(tahun_ajaran_id);

-- User table indexes
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_user_active ON users(is_active);

-- Siswa table indexes
CREATE INDEX idx_siswa_kelas ON siswas(kelas_id);
CREATE INDEX idx_siswa_user ON siswas(user_id);
```

---

## 🔧 **KEY SERVICES & ALGORITHMS**

### **Genetic Algorithm Service:**

#### **Class Structure:**
```php
class GeneticAlgorithmService
{
    private $populationSize = 100;
    private $maxGenerations = 50;
    private $mutationRate = 0.1;
    private $crossoverRate = 0.8;
    
    public function generateSchedule($tahunAjaranId, $semester)
    {
        // 1. Initialize population
        $population = $this->initializePopulation();
        
        // 2. Evolution loop
        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            // Evaluate fitness
            $fitness = $this->evaluatePopulation($population);
            
            // Selection
            $parents = $this->selection($population, $fitness);
            
            // Crossover
            $offspring = $this->crossover($parents);
            
            // Mutation
            $offspring = $this->mutation($offspring);
            
            // Replace population
            $population = $this->replacement($population, $offspring, $fitness);
            
            // Check termination
            if ($this->isOptimal($fitness)) break;
        }
        
        return $this->getBestChromosome($population);
    }
}
```

#### **Fitness Function:**
```php
private function calculateFitness($chromosome)
{
    $score = 0;
    $maxScore = 0;
    
    // Hard constraints (must be satisfied)
    $score += $this->checkTeacherConflicts($chromosome) * 0.4;
    $score += $this->checkRoomConflicts($chromosome) * 0.3;
    $score += $this->checkClassConflicts($chromosome) * 0.3;
    
    // Soft constraints (preferences)
    $score += $this->checkDistribution($chromosome) * 0.1;
    $score += $this->checkTeacherPreferences($chromosome) * 0.1;
    
    $maxScore = 1.0; // Perfect score
    
    return $score / $maxScore;
}
```

---

## 🎨 **FRONTEND ARCHITECTURE**

### **Filament Resources Pattern:**

#### **Base Resource Structure:**
```php
class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Manajemen Siswa';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form components
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns
            ])
            ->filters([
                // Filters
            ])
            ->actions([
                // Row actions
            ])
            ->bulkActions([
                // Bulk actions
            ]);
    }
}
```

#### **Custom Actions Implementation:**
```php
// Bulk create accounts action
Tables\Actions\BulkAction::make('createAccounts')
    ->label('Buat Akun Login')
    ->icon('heroicon-o-user-plus')
    ->action(function (Collection $records) {
        $created = 0;
        foreach ($records as $siswa) {
            if (!$siswa->user_id) {
                $user = $this->createStudentAccount($siswa);
                $siswa->update(['user_id' => $user->id]);
                $created++;
            }
        }
        
        Notification::make()
            ->title("Berhasil membuat {$created} akun siswa")
            ->success()
            ->send();
    })
    ->requiresConfirmation()
    ->visible(fn () => auth()->user()->can('create_student_accounts'));
```

### **Student Portal Architecture:**

#### **Controller Pattern:**
```php
class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Verify student role
        if (!$user->hasRole('siswa')) {
            return redirect()->route('student.index');
        }
        
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        // Get today's schedule
        $today = Carbon::now()->locale('id');
        $hariIni = strtolower($today->dayName);
        
        $jadwalHariIni = Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->orderBy('jam_mulai')
            ->get();
        
        return view('student.dashboard', compact('siswa', 'jadwalHariIni'));
    }
}
```

#### **Blade Template Pattern:**
```blade
{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">
            Selamat Datang, {{ $siswa->nama_lengkap }}! 👋
        </h2>
        <p class="text-indigo-100">
            Kelas {{ $siswa->kelas->nama_kelas }} • {{ ucfirst($hariIni) }}
        </p>
    </div>
    
    {{-- Content sections --}}
</div>
@endsection
```

---

## 🔐 **SECURITY IMPLEMENTATION**

### **Authentication Flow:**
```php
// Student login process
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        
        // Role verification
        if ($user->hasRole('siswa')) {
            return redirect()->route('student.dashboard');
        } else {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun ini bukan akun siswa.']);
        }
    }

    return back()->withErrors(['email' => 'Email atau password salah.']);
}
```

### **Authorization Middleware:**
```php
// Route protection - Student Portal as Root
Route::get('/', [StudentPortalController::class, 'index'])->name('home');
Route::post('/login', [StudentPortalController::class, 'login'])->name('student.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/jadwal', [StudentPortalController::class, 'jadwal'])->name('student.jadwal');
    Route::get('/materi', [StudentPortalController::class, 'materi'])->name('student.materi');
    Route::post('/logout', [StudentPortalController::class, 'logout'])->name('student.logout');
});
```

### **Data Access Control:**
```php
// Ensure students only see their class data
public function materi()
{
    $user = Auth::user();
    $siswa = Siswa::where('user_id', $user->id)->first();

    $materiList = Materi::with(['guru', 'mataPelajaran'])
        ->where('kelas_id', $siswa->kelas_id) // Data isolation
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('student.materi', compact('siswa', 'materiList'));
}
```

---

## ⚡ **PERFORMANCE OPTIMIZATION**

### **Database Optimization:**

#### **Eager Loading:**
```php
// Prevent N+1 queries
$jadwalMingguIni = Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
    ->where('kelas_id', $siswa->kelas_id)
    ->where('is_active', true)
    ->orderBy('hari')
    ->orderBy('jam_mulai')
    ->get()
    ->groupBy('hari');
```

#### **Query Optimization:**
```php
// Use specific columns
$siswas = Siswa::select(['id', 'nama_lengkap', 'nis', 'kelas_id'])
    ->with(['kelas:id,nama_kelas', 'user:id,email'])
    ->where('status_siswa', 'aktif')
    ->get();
```

### **Caching Strategy:**
```php
// Cache frequently accessed data
$jadwalCache = Cache::remember("jadwal_kelas_{$kelasId}", 3600, function () use ($kelasId) {
    return Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
        ->where('kelas_id', $kelasId)
        ->where('is_active', true)
        ->get();
});
```

### **Asset Optimization:**
```javascript
// Vite configuration for asset bundling
export default defineConfig({
    plugins: [laravel({
        input: [
            'resources/css/app.css',
            'resources/js/app.js',
        ],
        refresh: true,
    })],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                    filament: ['@filament/forms', '@filament/tables']
                }
            }
        }
    }
});
```

---

## 🧪 **TESTING STRATEGY**

### **Unit Tests:**
```php
class SiswaTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_siswa_can_be_created_with_valid_data()
    {
        $kelas = Kelas::factory()->create();
        
        $siswaData = [
            'nama_lengkap' => 'Test Siswa',
            'nis' => '2024001',
            'kelas_id' => $kelas->id,
            'status_siswa' => 'aktif'
        ];
        
        $siswa = Siswa::create($siswaData);
        
        $this->assertDatabaseHas('siswas', $siswaData);
        $this->assertEquals('Test Siswa', $siswa->nama_lengkap);
    }
}
```

### **Feature Tests:**
```php
class StudentPortalTest extends TestCase
{
    public function test_student_can_login_and_access_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');
        
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);
        
        $response = $this->post('/student/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
```

---

## 📊 **MONITORING & LOGGING**

### **Custom Logging:**
```php
// Log important events
Log::info('Jadwal generation started', [
    'tahun_ajaran_id' => $tahunAjaranId,
    'semester' => $semester,
    'user_id' => auth()->id()
]);

Log::error('Jadwal generation failed', [
    'error' => $exception->getMessage(),
    'trace' => $exception->getTraceAsString()
]);
```

### **Performance Monitoring:**
```php
// Monitor slow queries
DB::listen(function ($query) {
    if ($query->time > 1000) { // Log queries > 1 second
        Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time
        ]);
    }
});
```

---

## 🚀 **DEPLOYMENT CHECKLIST**

### **Pre-deployment:**
- [ ] Run tests: `php artisan test`
- [ ] Check code style: `./vendor/bin/pint`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Build assets: `npm run build`
- [ ] Clear caches: `php artisan optimize:clear`

### **Production Setup:**
```bash
# Environment
cp .env.example .env.production
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder

# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage
php artisan storage:link
chmod -R 755 storage bootstrap/cache

# Queue worker (if using)
php artisan queue:work --daemon
```

### **Post-deployment:**
- [ ] Verify all routes work
- [ ] Test login flows
- [ ] Check file uploads
- [ ] Monitor error logs
- [ ] Performance testing

---

**🎯 Technical documentation complete - Ready for development and deployment! 🎯**
