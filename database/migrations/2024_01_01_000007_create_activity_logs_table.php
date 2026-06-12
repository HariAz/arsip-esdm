<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Nullable karena aksi bisa dari approver eksternal (tidak punya akun)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('document_id')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete();

            // Kode aksi — lihat konstanta di Model ActivityLog
            $table->string('action', 100);

            // Deskripsi human-readable
            $table->text('description')->nullable();

            // Untuk approver eksternal (tidak punya akun di sistem)
            $table->string('actor_name', 100)->nullable();
            $table->string('actor_email', 150)->nullable();

            // Metadata teknis
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Data fleksibel tambahan (misal: nama file, step approval, dll)
            $table->json('metadata')->nullable();

            // Hanya created_at, tidak perlu updated_at (log tidak pernah diubah)
            $table->timestamp('created_at')->useCurrent();

            // Index untuk query log
            $table->index('action');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
