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
        Schema::create('m_tmdb', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('page')->nullable();
            $table->integer('last_page')->nullable();
            $table->integer('repeat')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('m_tmdb');
    }
};
