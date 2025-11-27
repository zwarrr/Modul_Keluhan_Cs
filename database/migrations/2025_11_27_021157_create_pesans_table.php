<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('chat_sesis')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('member_id')->constrained('users')->onDelete('cascade'); // opsi pengembangan
            $table->text('message');
            $table->enum('status', ['sent', 'read'])->default('sent');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
