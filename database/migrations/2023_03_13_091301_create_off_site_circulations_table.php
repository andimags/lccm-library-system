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
        Schema::create('off_site_circulations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('patrons')->cascadeOnDelete();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->foreignId('copy_id')->nullable()->constrained('copies')->cascadeOnDelete();
            $table->decimal('total_fines', 11, 2)->default(0.00);
            $table->datetime('due_at');
            $table->integer('grace_period_days');
            $table->datetime('checked_in_at')->nullable();
            $table->datetime('checked_out_at');
            $table->string('fines_status')->default('unpaid');
            $table->string('status')->nullable(); //CHECKED-OUT, CHECKED-IN, LOST, REPLACED
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_site_circulations');
    }
};
