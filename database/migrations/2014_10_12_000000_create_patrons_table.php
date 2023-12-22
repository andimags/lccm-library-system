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
        Schema::create('patrons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id2')->unique();
            $table->foreignId('librarian_id')->nullable()->constrained('patrons')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('temp_role')->nullable();
            $table->timestamps();
            $table->datetime('email_verified_at')->nullable();
            $table->string('registration_status')->default('accepted');
            $table->string('display_mode')->default('day');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrons');
    }
};
