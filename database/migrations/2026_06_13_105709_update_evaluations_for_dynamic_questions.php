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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['q1', 'q2', 'q3', 'q4']);
            $table->json('answers')->nullable()->after('jadwal_id');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('answers');
            $table->integer('q1')->nullable();
            $table->integer('q2')->nullable();
            $table->integer('q3')->nullable();
            $table->integer('q4')->nullable();
        });
    }
};
