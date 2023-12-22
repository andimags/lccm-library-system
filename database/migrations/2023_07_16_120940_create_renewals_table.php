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
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->datetime('old_due_at');
            $table->datetime('new_due_at');
            $table->foreignId('off_site_circulation_id')->constrained('off_site_circulations')->cascadeOnDelete();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewals');
    }
};
