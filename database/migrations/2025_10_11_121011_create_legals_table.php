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
        Schema::create('legals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('legal_city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('social_security_number');
            $table->string('filing_status');
            $table->integer('number_of_dependant')->default(0);
            $table->jsonb('spouse_information')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legals');
    }
};
