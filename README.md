# 📚 JadwalKu - Sistem Manajemen Jadwal Sekolah

<div align="center">

![JadwalKu Logo](https://via.placeholder.com/200x100/4F46E5/FFFFFF?text=JadwalKu)

**Solusi Lengkap Manajemen Jadwal Sekolah dengan Algoritma Genetika**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=php)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)

[Demo](https://jadwalku-demo.com) • [Dokumentasi](RANGKUMAN_APLIKASI_JADWALKU.md) • [Instalasi](INSTALLATION_GUIDE.md) • [Technical Docs](TECHNICAL_DOCUMENTATION.md)

</div>

---

## 🎯 **Tentang JadwalKu**

JadwalKu adalah sistem manajemen jadwal sekolah modern yang dibangun dengan Laravel dan Filament. Aplikasi ini menyediakan solusi lengkap untuk pengelolaan jadwal pelajaran, data siswa, guru, dan materi pembelajaran dengan menggunakan algoritma genetika untuk optimasi jadwal otomatis.

### ✨ **Fitur Utama**

- 🤖 **Auto-Generate Jadwal** dengan Algoritma Genetika
- 👨‍🎓 **Manajemen Siswa** lengkap dengan bulk operations
- 👨‍🏫 **Manajemen Guru** dan assignment mata pelajaran
- 📚 **Sistem Materi** dengan file upload dan download
- 📥 **Download Materi** dengan keamanan tinggi dan UI modern
- 📊 **Dashboard Analytics** dengan visualisasi data
- 🎓 **Portal Siswa** terpisah dengan UI modern
- 🔐 **Role-based Access Control** (Admin, Guru, Siswa)
- 📱 **Responsive Design** untuk semua device

---

## 🚀 **Quick Start**

### **Prerequisites**
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js & NPM

### **Installation**
```bash
# Clone repository
git clone https://github.com/your-username/jadwalku.git
cd jadwalku

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### **Default Login**
```
Student Portal (Root): http://localhost:8000/
Email: ahmad.2024001@siswa.sekolah.com
Password: siswa2024001

Admin Panel: http://localhost:8000/admin
Email: admin@jadwalku.com
Password: password
```

📖 **[Panduan Instalasi Lengkap](INSTALLATION_GUIDE.md)**

---

## 🏗️ **Arsitektur Sistem**

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Admin Panel   │    │  Student Portal │    │   API Endpoints │
│   (Filament)    │    │   (Blade/CSS)   │    │    (Future)     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
         ┌─────────────────────────────────────────────────┐
         │              Laravel Backend                    │
         │  ┌─────────────┐  ┌─────────────┐  ┌──────────┐ │
         │  │   Models    │  │ Controllers │  │ Services │ │
         │  └─────────────┘  └─────────────┘  └──────────┘ │
         └─────────────────────────────────────────────────┘
                                 │
         ┌─────────────────────────────────────────────────┐
         │                MySQL Database                   │
         │  Users • Siswa • Guru • Jadwal • Materi • etc   │
         └─────────────────────────────────────────────────┘
```

---

## 👥 **User Roles & Permissions**

### 🔑 **Admin (Super User)**
- ✅ Full access ke semua fitur
- ✅ Manajemen users dan roles
- ✅ Generate jadwal otomatis
- ✅ Analytics dan reporting
- ✅ System configuration

### 👨‍🏫 **Guru**
- ✅ Akses admin panel terbatas
- ✅ Upload dan manage materi
- ✅ Lihat jadwal mengajar
- ✅ Manage data siswa di kelas yang diajar

### 🎓 **Siswa**
- ✅ Portal siswa terpisah
- ✅ Lihat jadwal pelajaran
- ✅ Download materi pembelajaran
- ✅ Dashboard personal

---

## 🧬 **Algoritma Genetika**

JadwalKu menggunakan algoritma genetika untuk mengoptimalkan penjadwalan otomatis:

```
1. 🧬 Initialization → Generate populasi jadwal random
2. 📊 Fitness Evaluation → Hitung skor berdasarkan constraints
3. 🎯 Selection → Pilih jadwal terbaik untuk breeding
4. 🔄 Crossover → Kombinasi jadwal untuk offspring
5. 🎲 Mutation → Random changes untuk variasi
6. 🔁 Iteration → Repeat hingga optimal
```

### **Constraints yang Dipertimbangkan:**
- ❌ Guru tidak bentrok waktu
- ❌ Ruangan tidak double booking
- ❌ Kelas tidak ada jadwal bersamaan
- ✅ Distribusi mata pelajaran merata
- ✅ Preferensi waktu guru

---

## 📱 **Screenshots**

<div align="center">

### Admin Dashboard
![Admin Dashboard](https://via.placeholder.com/800x400/4F46E5/FFFFFF?text=Admin+Dashboard)

### Student Portal
![Student Portal](https://via.placeholder.com/800x400/10B981/FFFFFF?text=Student+Portal)

### Schedule Generator
![Schedule Generator](https://via.placeholder.com/800x400/F59E0B/FFFFFF?text=Schedule+Generator)

</div>

---

## 🛠️ **Tech Stack**

### **Backend**
- **Framework**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Auth + Spatie Permission
- **File Storage**: Laravel Storage

### **Frontend**
- **CSS Framework**: Tailwind CSS 3.x
- **Icons**: Heroicons + Font Awesome
- **JavaScript**: Alpine.js (via Filament)
- **Build Tool**: Vite

### **Development**
- **PHP**: 8.1+
- **Composer**: 2.0+
- **Node.js**: 16.0+
- **NPM**: 8.0+

---

## 📊 **Database Schema**

```sql
users
├── id, nama, email, password
├── nomor_telepon, alamat, tanggal_lahir
└── is_active, created_at, updated_at

siswas
├── id, user_id, kelas_id
├── nama_lengkap, nisn, nis
├── tahun_masuk, status_siswa
└── nama_orang_tua, nomor_telepon_orang_tua

jadwals
├── id, kelas_id, mata_pelajaran_id
├── guru_id, ruangan_id, tahun_ajaran_id
├── hari, jam_mulai, jam_selesai
└── is_active, created_at, updated_at

materis
├── id, judul, deskripsi, file_path
├── guru_id, mata_pelajaran_id, kelas_id
└── created_at, updated_at
```

📖 **[Database Schema Lengkap](TECHNICAL_DOCUMENTATION.md#database-schema)**

---

## 🎯 **Fitur Unggulan**

### 🤖 **Auto-Generate Jadwal**
- Algoritma genetika untuk optimasi
- Conflict detection otomatis
- Parameter yang dapat disesuaikan
- Export ke PDF/Excel

### 👨‍🎓 **Manajemen Siswa**
- Import/export data Excel
- Bulk create accounts
- Auto-generate email & password
- Status tracking

### 📚 **Sistem Materi**
- Upload multiple file types
- Kategorisasi per mata pelajaran
- Access control per kelas
- Download tracking

### 📥 **Download Materi**
- Secure file download dengan access control
- Support multiple file formats (PDF, DOC, PPT, Video, dll)
- File size display dan type icons
- Download logging untuk audit
- Responsive file listing interface

### 📊 **Analytics Dashboard**
- Real-time statistics
- Chart visualizations
- Performance metrics
- User activity logs

---

## 🔐 **Security Features**

- 🛡️ **Role-based Access Control** dengan Spatie Permission
- 🔒 **CSRF Protection** untuk semua forms
- 🔐 **Password Hashing** dengan bcrypt
- 🚫 **SQL Injection Protection** via Eloquent ORM
- 🛡️ **XSS Protection** dengan input sanitization
- 📁 **File Upload Validation** untuk keamanan

---

## 📈 **Performance**

- ⚡ **Optimized Queries** dengan eager loading
- 🗄️ **Database Indexing** pada kolom penting
- 💾 **Caching Strategy** untuk data frequently accessed
- 📦 **Asset Optimization** dengan Vite bundling
- 🔄 **Lazy Loading** untuk improved UX

---

## 🧪 **Testing**

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test tests/Feature/StudentPortalTest.php
```

---

## 📚 **Documentation**

- 📖 **[Rangkuman Aplikasi](RANGKUMAN_APLIKASI_JADWALKU.md)** - Overview lengkap fitur dan cara kerja
- 🔧 **[Technical Documentation](TECHNICAL_DOCUMENTATION.md)** - Detail implementasi untuk developer
- 🚀 **[Installation Guide](INSTALLATION_GUIDE.md)** - Panduan instalasi step-by-step
- 📥 **[Fitur Download Materi](FITUR_DOWNLOAD_MATERI.md)** - Dokumentasi lengkap fitur download
- 📋 **[API Documentation](API_DOCUMENTATION.md)** - API endpoints (coming soon)

---

## 🤝 **Contributing**

Kami menyambut kontribusi dari komunitas! Silakan baca [CONTRIBUTING.md](CONTRIBUTING.md) untuk guidelines.

### **Development Setup**
```bash
# Clone for development
git clone https://github.com/your-username/jadwalku.git
cd jadwalku

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations with test data
php artisan migrate:fresh --seed

# Start development
php artisan serve
npm run dev
```

---

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 **Acknowledgments**

- [Laravel](https://laravel.com) - The PHP Framework
- [Filament](https://filamentphp.com) - Admin Panel Framework
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
- [Spatie](https://spatie.be) - Laravel Packages
- [Heroicons](https://heroicons.com) - Icon Library

---

## 📞 **Support**

- 📧 **Email**: support@jadwalku.com
- 🐛 **Issues**: [GitHub Issues](https://github.com/your-username/jadwalku/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/your-username/jadwalku/discussions)
- 📖 **Wiki**: [GitHub Wiki](https://github.com/your-username/jadwalku/wiki)

---

<div align="center">

**⭐ Jika project ini membantu, jangan lupa berikan star! ⭐**

Made with ❤️ by [Your Name](https://github.com/your-username)

</div>
