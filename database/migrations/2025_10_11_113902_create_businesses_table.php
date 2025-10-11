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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description');
            $table->string('address_line_one');
            $table->string('address_line_two')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('work_phone');
            $table->string('home_phone')->nullable();
            $table->string('website')->nullable();
            $table->boolean('has_1099_misc')->default(false);
            $table->boolean('is_license_requirement')->default(false);
            $table->boolean('has_business_license')->default(false);
            $table->boolean('file_taxed_for_tax_year')->default(false);
            $table->string('business_advertisement')->nullable();
            $table->jsonb('advertise_through')->nullable();
            $table->jsonb('records')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
