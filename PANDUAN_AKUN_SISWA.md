# 📚 **PANDUAN AKUN SISWA - Portal JadwalKu**

---

## 🎯 **Ringkasan**

Panduan lengkap untuk admin dalam mengelola akun login siswa di Portal JadwalKu, termasuk cara membuat akun, format kredensial, dan troubleshooting.

---

## 🔐 **Format Akun Siswa**

### **📧 Format Email:**
```
[nama_depan].[nis]@siswa.sekolah.com
```

**Contoh:**
- Ahmad Rizki Pratama (NIS: 2024001) → `ahmad.2024001@siswa.sekolah.com`
- Siti Nurhaliza (NIS: 2024002) → `siti.2024002@siswa.sekolah.com`

### **🔑 Format Password:**
```
siswa[nis]
```

**Contoh:**
- NIS: 2024001 → Password: `siswa2024001`
- NIS: 2024002 → Password: `siswa2024002`

---

## 🛠️ **Cara Membuat Akun Siswa**

### **1. 📝 Saat Menambah Siswa Baru:**

1. **Masuk ke Admin Panel** → `Siswa` → `Tambah Siswa`
2. **Isi data siswa** lengkap (nama, NIS, kelas, dll.)
3. **Scroll ke bawah** ke section "Akun Login Siswa"
4. **Aktifkan toggle** "Buat Akun Login untuk Siswa"
5. **Preview kredensial** akan muncul otomatis
6. **Simpan** - akun akan dibuat otomatis

### **2. ✏️ Untuk Siswa yang Sudah Ada:**

1. **Masuk ke Admin Panel** → `Siswa`
2. **Klik Edit** pada siswa yang belum punya akun
3. **Klik tombol** "Buat Akun Login" di header
4. **Konfirmasi** - akun akan dibuat dengan notifikasi kredensial

### **3. 📦 Bulk Create (Banyak Siswa):**

1. **Masuk ke Admin Panel** → `Siswa`
2. **Pilih siswa** yang belum punya akun (centang checkbox)
3. **Klik dropdown** "Bulk Actions"
4. **Pilih** "Buat Akun Login"
5. **Konfirmasi** - semua akun akan dibuat sekaligus

---

## 📋 **Daftar Akun Siswa yang Tersedia**

### **🎓 Kelas X IPA 1:**
- Ahmad Rizki Pratama: `ahmad.2024001@siswa.sekolah.com` / `siswa2024001`
- Siti Nurhaliza Putri: `siti.2024002@siswa.sekolah.com` / `siswa2024002`
- Budi Santoso: `budi.2024003@siswa.sekolah.com` / `siswa2024003`
- Dewi Sartika: `dewi.2024004@siswa.sekolah.com` / `siswa2024004`
- Eko Prasetyo: `eko.2024005@siswa.sekolah.com` / `siswa2024005`

### **🎓 Kelas XI IPA 1:**
- Putra Wijaya: `putra.2023001@siswa.sekolah.com` / `siswa2023001`
- Qonita Zahira: `qonita.2023002@siswa.sekolah.com` / `siswa2023002`
- Rafi Maulana: `rafi.2023003@siswa.sekolah.com` / `siswa2023003`
- Salma Kamila: `salma.2023004@siswa.sekolah.com` / `siswa2023004`

### **🎓 Kelas XII IPA 1:**
- Arief Rahman: `arief.2022001@siswa.sekolah.com` / `siswa2022001`
- Bella Safira: `bella.2022002@siswa.sekolah.com` / `siswa2022002`
- Candra Kirana: `candra.2022003@siswa.sekolah.com` / `siswa2022003`

### **🧪 Akun Test:**
- Test Siswa: `test.siswa@siswa.sekolah.com` / `siswa123`

---

## 🔧 **Troubleshooting**

### **❌ Masalah: "Email atau password salah"**

**Solusi:**
1. **Pastikan format email benar** (nama_depan.nis@siswa.sekolah.com)
2. **Pastikan password benar** (siswa + NIS)
3. **Cek apakah akun sudah dibuat** di admin panel
4. **Reset password** jika perlu melalui admin panel

### **❌ Masalah: "Akun ini bukan akun siswa"**

**Solusi:**
1. **Cek role user** di admin panel
2. **Pastikan user memiliki role 'siswa'**
3. **Re-assign role** jika perlu

### **❌ Masalah: Siswa tidak bisa akses dashboard**

**Solusi:**
1. **Pastikan user_id** di tabel siswa sudah terisi
2. **Cek status aktif** user di admin panel
3. **Pastikan siswa terhubung ke kelas**

---

## 🔄 **Reset Password Siswa**

### **Cara Reset:**
1. **Masuk ke Admin Panel** → `Siswa`
2. **Klik Edit** pada siswa yang ingin direset
3. **Klik tombol** "Reset Password" di header
4. **Konfirmasi** - password akan kembali ke default (siswa + NIS)

---

## 📊 **Monitoring Akun Siswa**

### **Cek Status Akun:**
- **Kolom "Akun Login"** di tabel siswa menunjukkan status
- **✅ Hijau** = Sudah punya akun
- **❌ Merah** = Belum punya akun

### **Cek Email Login:**
- **Kolom "Email Login"** menampilkan email yang terdaftar
- **"Belum ada akun"** = Perlu dibuat akun

---

## 🎯 **Tips untuk Admin**

### **✅ Best Practices:**
1. **Selalu buat akun** saat menambah siswa baru
2. **Catat kredensial** untuk diberikan ke siswa
3. **Test login** setelah membuat akun
4. **Monitor status akun** secara berkala

### **⚠️ Hal yang Perlu Diperhatikan:**
1. **Email harus unik** - tidak boleh duplikat
2. **NIS harus benar** - mempengaruhi email dan password
3. **Nama depan** akan digunakan untuk email
4. **Password default** mudah ditebak, sarankan siswa ganti

---

## 🚀 **Akses Portal Siswa**

### **URL Portal:**
```
http://jadwalku.test
```

### **Fitur yang Tersedia:**
- ✅ **Dashboard** - Ringkasan jadwal dan materi
- ✅ **Jadwal** - Lihat jadwal pelajaran per hari
- ✅ **Materi** - Download materi pembelajaran
- ✅ **Profile** - Informasi siswa dan kelas

---

**📞 Bantuan Teknis: Hubungi Administrator Sistem**
