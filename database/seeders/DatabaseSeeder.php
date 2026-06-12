<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. USERS — 3 Arsiparis
        // =============================================
        DB::table('users')->insert([
            [
                'name'       => 'Arsiparis Satu',
                'email'      => 'arsiparis1@esdm.go.id',
                'password'   => Hash::make('password123'),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Arsiparis Dua',
                'email'      => 'arsiparis2@esdm.go.id',
                'password'   => Hash::make('password123'),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Arsiparis Tiga',
                'email'      => 'arsiparis3@esdm.go.id',
                'password'   => Hash::make('password123'),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =============================================
        // 2. DIVISIONS — 8 Divisi (contoh, sesuaikan nama asli)
        // Ganti head_name dan head_email dengan data nyata
        // =============================================
        $divisions = [
            [
                'code'       => 'DIV-01',
                'name'       => 'Divisi Perencanaan',
                'head_name'  => 'Kepala Divisi Perencanaan',
                'head_email' => 'kadiv.perencanaan@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-02',
                'name'       => 'Divisi Keuangan',
                'head_name'  => 'Kepala Divisi Keuangan',
                'head_email' => 'kadiv.keuangan@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-03',
                'name'       => 'Divisi SDM & Umum',
                'head_name'  => 'Kepala Divisi SDM',
                'head_email' => 'kadiv.sdm@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-04',
                'name'       => 'Divisi Teknik',
                'head_name'  => 'Kepala Divisi Teknik',
                'head_email' => 'kadiv.teknik@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-05',
                'name'       => 'Divisi Hukum',
                'head_name'  => 'Kepala Divisi Hukum',
                'head_email' => 'kadiv.hukum@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-06',
                'name'       => 'Divisi IT & Sistem',
                'head_name'  => 'Kepala Divisi IT',
                'head_email' => 'kadiv.it@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-07',
                'name'       => 'Divisi Laboratorium',
                'head_name'  => 'Kepala Divisi Lab',
                'head_email' => 'kadiv.lab@esdm.go.id',
                'head_phone' => null,
            ],
            [
                'code'       => 'DIV-08',
                'name'       => 'Divisi Pengawasan',
                'head_name'  => 'Kepala Divisi Pengawasan',
                'head_email' => 'kadiv.pengawasan@esdm.go.id',
                'head_phone' => null,
            ],
        ];

        foreach ($divisions as $division) {
            DB::table('divisions')->insert(array_merge($division, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
