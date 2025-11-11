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
        Schema::create('tax_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained();
            $table->year('tax_year');
            $table->string('full_name');
            $table->string('ssn')->unique();
            $table->text('specific_request')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_requests');
    }
};
