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
        Schema::table('templates', function (Blueprint $table) {
            // Check if columns exist before changing them
            if (Schema::hasColumn('templates', 'desc_en')) {
                $table->text('desc_en')->nullable()->change();
            }
            if (Schema::hasColumn('templates', 'desc_se')) {
                $table->text('desc_se')->nullable()->change();
            }
            if (Schema::hasColumn('templates', 'start')) {
                $table->dateTime('start')->nullable()->change();
            }
            if (Schema::hasColumn('templates', 'end')) {
                $table->dateTime('end')->nullable()->change();
            }
            if (Schema::hasColumn('templates', 'hours')) {
                $table->decimal('hours')->nullable()->change();
            }
            if (Schema::hasColumn('templates', 'task_status_id')) {
                $table->unsignedBigInteger('task_status_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'desc_en')) {
                $table->text('desc_en')->nullable(false)->change();
            }
            if (Schema::hasColumn('templates', 'desc_se')) {
                $table->text('desc_se')->nullable(false)->change();
            }
            if (Schema::hasColumn('templates', 'start')) {
                $table->dateTime('start')->nullable(false)->change();
            }
            if (Schema::hasColumn('templates', 'end')) {
                $table->dateTime('end')->nullable(false)->change();
            }
            if (Schema::hasColumn('templates', 'hours')) {
                $table->decimal('hours')->nullable(false)->change();
            }
            if (Schema::hasColumn('templates', 'task_status_id')) {
                $table->unsignedBigInteger('task_status_id')->nullable(false)->change();
            }
        });
    }
};
