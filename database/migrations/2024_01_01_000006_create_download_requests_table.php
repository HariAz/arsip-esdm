<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Header permintaan download
        Schema::create('download_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                  ->constrained('documents')
                  ->restrictOnDelete();
            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->text('reason')->nullable(); // alasan download (opsional)
            $table->enum('status', [
                'pending',    // menunggu approval
                'approved',   // disetujui, bisa download
                'rejected',   // ditolak
                'downloaded', // sudah didownload
            ])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            $table->index(['requested_by', 'status']);
            $table->index(['document_id', 'status']);
        });

        // Detail tiap langkah approval download (sequential)
        Schema::create('download_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('download_request_id')
                  ->constrained('download_requests')
                  ->cascadeOnDelete();

            // step_order: 1 = Bagian Umum, 2 = Kepala Divisi
            $table->tinyInteger('step_order')->unsigned();

            // Snapshot data approver
            $table->string('approver_name', 100);
            $table->string('approver_email', 150);

            // Magic Link
            $table->string('token', 100)->unique();
            $table->timestamp('token_expires_at');
            $table->timestamp('token_used_at')->nullable();

            // Keputusan
            $table->enum('status', [
                'waiting',
                'sent',
                'approved',
                'rejected',
            ])->default('waiting');

            $table->timestamp('decided_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['download_request_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_approval_steps');
        Schema::dropIfExists('download_requests');
    }
};
