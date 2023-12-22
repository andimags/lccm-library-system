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
        Schema::create('temp_check_out_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('librarian_id')->constrained('patrons')->cascadeOnDelete();  
            $table->unsignedBigInteger('copy_id')->constrained('copies')->cascadeOnDelete();
            $table->datetime('due_at');
            $table->integer('grace_period_days');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_check_out_items');
    }
};
