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
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('priority')->default(0)->comment('The higher the number, The higher the priority');
            $table->string('payload')->comment('Job info');
            $table->integer('attempts')->default(0)->comment('Number Of Retrires');
            $table->timestamp('available_at');
            $table->string('status')->default('queued')->comment('queued|processing|completed|failed|paused|retrying');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_jobs');
    }
};
