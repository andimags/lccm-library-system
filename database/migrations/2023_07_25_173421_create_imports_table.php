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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('table');
            $table->integer('success_count')->unsigned()->default(0);
            $table->integer('failed_count')->unsigned()->default(0);
            $table->integer('total_records')->unsigned()->default(0);
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
