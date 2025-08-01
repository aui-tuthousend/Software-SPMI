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
        Schema::create('peningkatans', function (Blueprint $table) {
            $table->id();
            $table->string('komentar');
            $table->string('edited_by');
            $table->unsignedBigInteger('id_pengendalian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peningkatans');
    }
};
