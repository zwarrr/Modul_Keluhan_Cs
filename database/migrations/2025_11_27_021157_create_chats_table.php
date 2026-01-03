<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel chats untuk menyimpan pesan dalam sesi chat
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            
            // Pengirim pesan: bisa member atau CS (polymorphic-like approach)
            $table->unsignedBigInteger('sender_id'); // ID pengirim
            $table->enum('sender_type', ['member', 'cs']); // Tipe pengirim
            
            $table->text('message')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // image, file
            $table->enum('status', ['sent', 'read'])->default('sent');
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['session_id', 'sent_at']);
            $table->index(['sender_id', 'sender_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
