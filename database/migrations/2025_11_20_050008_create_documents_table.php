<?php

use App\Models\Attachment;
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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Attachment::class, 'w2_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignIdFor(Attachment::class, 'misc_1099_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignIdFor(Attachment::class, 'mortgage_statement_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignIdFor(Attachment::class, 'tuition_statement_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignIdFor(Attachment::class, 'shared_riders_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignIdFor(Attachment::class, 'misc_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
