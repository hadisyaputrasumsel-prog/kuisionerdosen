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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->integer('q1'); // Kompetensi Pedagogik
            $table->integer('q2'); // Kompetensi Profesional
            $table->integer('q3'); // Kompetensi Kepribadian
            $table->integer('q4'); // Kompetensi Sosial
            $table->text('saran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
