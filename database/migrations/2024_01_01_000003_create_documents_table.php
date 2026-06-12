<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();

            // Atribut Dokumen
            $table->string('document_number', 100)->unique(); // Nomor Surat
            $table->string('title', 255);                     // Perihal
            $table->date('document_date');                    // Tanggal surat
            $table->year('year');                             // Untuk filter pencarian
            $table->string('document_type', 100)->nullable(); // SK, Laporan, Undangan, dll

            // Klasifikasi (Kepmen ESDM No. 167.K/04/MEM/2020)
            $table->enum('classification', [
                'biasa',
                'terbatas',
                'rahasia',
                'sangat_rahasia',
            ])->default('biasa');

            // File
            $table->string('file_path', 500);       // path relatif: 2024/DIV-01/namafile.pdf
            $table->string('file_name', 255);        // nama file asli saat upload
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('file_hash', 64)->nullable();         // SHA-256

            // Status Dokumen
            $table->enum('status', [
                'pending_approval', // menunggu approval upload (sangat_rahasia)
                'active',           // aktif, bisa dicari
                'rejected',         // ditolak saat approval upload
                'archived',         // diarsipkan
            ])->default('active');

            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // Index untuk pencarian
            $table->index('year');
            $table->index('classification');
            $table->index('status');
            $table->index(['division_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
