<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel sessions untuk menyimpan sesi chat antara member dan CS
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('cs_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('status', ['open', 'pending', 'closed'])->default('open');
            
            // Rating pelayanan
            $table->unsignedTinyInteger('rating_pelayanan')->nullable();
            $table->timestamp('rating_pelayanan_at')->nullable();
            
            // Informasi sesi
            $table->string('last_message')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->string('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

