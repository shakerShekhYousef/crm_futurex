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
        Schema::create('opportunities', function (Blueprint $table) {

                $table->bigIncrements('id');
                $table->integer('candidate_client_id');
                $table->date('follow_up_date')->nullable();
                $table->string('contact_method')->nullable();
                $table->string('status');
                $table->text('current_notes');
                $table->text('future_notes');
                $table->date('next_follow_up_date');
                $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
