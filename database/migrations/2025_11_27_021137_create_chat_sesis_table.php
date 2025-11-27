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
        Schema::create('chat_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cs_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->enum('status', ['open', 'onprogress', 'closed'])->default('open');
            $table->string('rating')->nullable();
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
        Schema::dropIfExists('chat_sesis');
    }
};
