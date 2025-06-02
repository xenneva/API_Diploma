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
        Schema::create('auto_test_passes_iterations', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('intermediate_result');
            $table->foreignId('auto_test_pass_id')->constrained('auto_test_passes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_test_passes_iterations');
    }
};
