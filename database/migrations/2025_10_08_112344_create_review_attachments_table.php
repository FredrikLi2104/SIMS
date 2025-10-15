<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id')->nullable(); // FK to reviews table
            $table->unsignedBigInteger('statement_id'); // FK to statements table
            $table->unsignedBigInteger('organisation_id'); // FK to organisations table
            $table->unsignedBigInteger('user_id'); // Who uploaded the file
            $table->string('file_name'); // Original filename
            $table->string('file_path'); // Path in storage
            $table->string('file_type')->nullable(); // MIME type
            $table->integer('file_size')->nullable(); // File size in bytes
            $table->text('description')->nullable(); // Optional description
            $table->enum('attachment_type', ['test_evidence', 'interview_notes', 'webform_response', 'other'])->default('test_evidence');
            $table->timestamps();

            // Foreign keys
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('statement_id')->references('id')->on('statements')->onDelete('cascade');
            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('review_id');
            $table->index('statement_id');
            $table->index('organisation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_attachments');
    }
};
