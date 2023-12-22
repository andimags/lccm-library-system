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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->string('format')->default('Books');
            $table->string('title');
            $table->string('edition')->nullable();
            $table->string('series_title')->nullable();
            $table->string('isbn')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('copyright_year')->nullable();
            $table->text('physical_description')->nullable();
            // $table->string('call_prefix')->nullable();
            $table->string('call_main')->nullable(); 
            $table->string('call_cutter')->nullable(); 
            $table->string('call_suffix')->nullable();
            $table->integer('total_copies')->default(0); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
