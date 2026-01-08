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
        Schema::create('m_movie', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title', 64)->nullable();

            $table->integer("vote_average")->default(0)->nullable();
            $table->integer("vote_count")->default(0)->nullable();

            // $table->string('status', 16)->nullable();
            $table->dateTime("release_date")->nullable()->useCurrent();

            // $table->integer('revenue')->default(0)->nullable();
            // $table->integer('runtime')->default(0)->nullable();

            $table->boolean('adult')->default(false)->nullable();
            $table->string('backdrop_path', 64)->nullable();
            $table->string('poster_path', 128)->nullable();

            // $table->integer('budget')->default(0)->nullable();

            // $table->string('imdb_id', 24)->nullable();
            $table->longText('overview')->nullable();
            $table->integer('popularity')->default(0)->nullable();
            // $table->string('genres', 24)->nullable();

            // $table->string('companies', 24)->nullable();
            // $table->string('countries', 24)->nullable();
            $table->string('languages', 24)->nullable();

            $table->boolean('is_updated_by_user')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_movie');
    }
};
