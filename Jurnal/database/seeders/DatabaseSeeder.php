<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // 6. Mahasiswa tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'mahasiswa2@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 7. Mata kuliah pada semester yang berbeda
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF203'],
            [
                'nama_mk' => 'Basis Data Relasional',
                'deskripsi' => 'Mempelajari perancangan basis data dan SQL.',
                'sks' => 3,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF401'],
            [
                'nama_mk' => 'Analisis Sistem Informasi',
                'deskripsi' => 'Mempelajari analisis kebutuhan sistem informasi.',
                'sks' => 2,
                'semester' => 'Ganjil 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        // 8. Enrollment mahasiswa
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);
        $course3->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 9. Dua tugas untuk pengujian
        $tugas1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Implementasi Middleware Laravel'],
            [
                'deskripsi' => 'Jelaskan fungsi middleware dan contoh penggunaannya.',
                'deadline' => now()->addDays(7),
            ]
        );

        $tugas2 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Relasi Eloquent ORM'],
            [
                'deskripsi' => 'Jelaskan perbedaan relasi belongsTo dan hasMany.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 10. Contoh tugas sudah dinilai, belum dinilai, dan belum lulus
        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Middleware menyaring request sebelum diteruskan ke controller.',
                'submitted_at' => now()->subDay(),
                'nilai' => 85,
                'catatan_dosen' => 'Penjelasan sudah tepat, tambahkan contoh penerapan.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Middleware memeriksa hak akses pengguna berdasarkan perannya.',
                'submitted_at' => now(),
                'nilai' => null,
                'catatan_dosen' => null,
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas2->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'HasMany untuk relasi banyak data dan belongsTo untuk data induk.',
                'submitted_at' => now(),
                'nilai' => 65,
                'catatan_dosen' => 'Perlu menambahkan contoh foreign key pada kedua relasi.',
            ]
        );
    }
}
