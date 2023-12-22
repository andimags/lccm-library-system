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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('off_site_circulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('patrons')->cascadeOnDelete();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
