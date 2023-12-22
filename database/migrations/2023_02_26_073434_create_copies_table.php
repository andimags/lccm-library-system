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
        Schema::create('copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->string('barcode');
            $table->string('fund')->nullable();
            $table->string('vendor')->nullable();
            $table->decimal('price', 11, 2)->default(0.00);
            $table->date('date_acquired')->nullable();
            $table->string('availability')->default('available');
            $table->boolean('is_important')->default(0);
            $table->string('call_prefix')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copies');
    }
};
