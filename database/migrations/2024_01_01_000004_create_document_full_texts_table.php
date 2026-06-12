<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_full_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                  ->unique()
                  ->constrained('documents')
                  ->cascadeOnDelete();
            $table->longText('content');          // hasil ekstraksi teks PDF
            $table->timestamp('extracted_at')->nullable();
            $table->timestamps();
        });

        // Tambahkan FULLTEXT index secara manual
        // (Blueprint tidak support FULLTEXT langsung di Laravel)
        DB::statement('ALTER TABLE document_full_texts ADD FULLTEXT INDEX ft_content (content)');
    }

    public function down(): void
    {
        Schema::dropIfExists('document_full_texts');
    }
};
