<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');       // Storage path for uploaded file
            $table->string('file_name');       // Original file name
            $table->integer('total_rows');     // Total rows to process
            $table->integer('processed_rows')->default(0);  // Successfully processed rows
            $table->string('status');          // queued|processing|completed|failed
            $table->text('error')->nullable(); // Error details if failed
            $table->integer('imported_count')->default(0); // Successfully imported records
            $table->integer('skipped_count')->default(0);  // Skipped records
            $table->timestamps();

            // Indexes for faster queries
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_statuses');
    }
};
