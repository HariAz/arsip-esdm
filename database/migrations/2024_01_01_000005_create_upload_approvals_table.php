<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Header approval upload (khusus dokumen sangat_rahasia)
        Schema::create('upload_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                  ->constrained('documents')
                  ->cascadeOnDelete();
            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Detail tiap langkah approval upload (sequential)
        Schema::create('upload_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_approval_id')
                  ->constrained('upload_approvals')
                  ->cascadeOnDelete();

            // step_order: 1 = Bagian Umum, 2 = Kepala Divisi
            $table->tinyInteger('step_order')->unsigned();

            // Snapshot data approver saat request dibuat
            // (supaya history tetap akurat walau data divisi berubah)
            $table->string('approver_name', 100);
            $table->string('approver_email', 150);

            // Magic Link
            $table->string('token', 100)->unique();
            $table->timestamp('token_expires_at');
            $table->timestamp('token_used_at')->nullable();

            // Keputusan
            $table->enum('status', [
                'waiting',   // belum dikirim (menunggu step sebelumnya)
                'sent',      // magic link sudah dikirim
                'approved',  // disetujui
                'rejected',  // ditolak
            ])->default('waiting');

            $table->timestamp('decided_at')->nullable(); // audit trail
            $table->text('rejection_reason')->nullable();
            $table->string('ip_address', 45)->nullable(); // verifikasi tambahan

            $table->timestamps();

            $table->index(['upload_approval_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_approval_steps');
        Schema::dropIfExists('upload_approvals');
    }
};
