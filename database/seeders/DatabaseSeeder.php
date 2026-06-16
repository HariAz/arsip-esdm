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
        // 2. DIVISIONS — 8 Divisi
        // Ganti data dengan nama dan email nyata sesuai instansi
        // =============================================
        $divisions = [
            [
                'code'                  => 'DIV-01',
                'name'                  => 'Divisi Perencanaan',
                'general_affairs_name'  => 'Bagian Umum Divisi Perencanaan',
                'general_affairs_email' => 'bagum.perencanaan@esdm.go.id',
                'head_name'             => 'Kepala Divisi Perencanaan',
                'head_email'            => 'kadiv.perencanaan@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-02',
                'name'                  => 'Divisi Keuangan',
                'general_affairs_name'  => 'Bagian Umum Divisi Keuangan',
                'general_affairs_email' => 'bagum.keuangan@esdm.go.id',
                'head_name'             => 'Kepala Divisi Keuangan',
                'head_email'            => 'kadiv.keuangan@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-03',
                'name'                  => 'Divisi SDM & Umum',
                'general_affairs_name'  => 'Bagian Umum Divisi SDM',
                'general_affairs_email' => 'bagum.sdm@esdm.go.id',
                'head_name'             => 'Kepala Divisi SDM',
                'head_email'            => 'kadiv.sdm@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-04',
                'name'                  => 'Divisi Teknik',
                'general_affairs_name'  => 'Bagian Umum Divisi Teknik',
                'general_affairs_email' => 'bagum.teknik@esdm.go.id',
                'head_name'             => 'Kepala Divisi Teknik',
                'head_email'            => 'kadiv.teknik@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-05',
                'name'                  => 'Divisi Hukum',
                'general_affairs_name'  => 'Bagian Umum Divisi Hukum',
                'general_affairs_email' => 'bagum.hukum@esdm.go.id',
                'head_name'             => 'Kepala Divisi Hukum',
                'head_email'            => 'kadiv.hukum@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-06',
                'name'                  => 'Divisi IT & Sistem',
                'general_affairs_name'  => 'Bagian Umum Divisi IT',
                'general_affairs_email' => 'bagum.it@esdm.go.id',
                'head_name'             => 'Kepala Divisi IT',
                'head_email'            => 'kadiv.it@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-07',
                'name'                  => 'Divisi Laboratorium',
                'general_affairs_name'  => 'Bagian Umum Divisi Lab',
                'general_affairs_email' => 'bagum.lab@esdm.go.id',
                'head_name'             => 'Kepala Divisi Lab',
                'head_email'            => 'kadiv.lab@esdm.go.id',
                'head_phone'            => null,
            ],
            [
                'code'                  => 'DIV-08',
                'name'                  => 'Divisi Pengawasan',
                'general_affairs_name'  => 'Bagian Umum Divisi Pengawasan',
                'general_affairs_email' => 'bagum.pengawasan@esdm.go.id',
                'head_name'             => 'Kepala Divisi Pengawasan',
                'head_email'            => 'kadiv.pengawasan@esdm.go.id',
                'head_phone'            => null,
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
