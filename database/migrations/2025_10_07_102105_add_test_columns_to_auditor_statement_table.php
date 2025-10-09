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
        Schema::table('auditor_statement', function (Blueprint $table) {
            $table->text('test_plan')->nullable()->after('guide');
            $table->string('test_method')->nullable()->after('test_plan');
            $table->text('test_result')->nullable()->after('test_method');
            $table->text('test_evidence')->nullable()->after('test_result');
            $table->datetime('test_date')->nullable()->after('test_evidence');
            $table->enum('test_status', ['planned', 'in_progress', 'completed'])->nullable()->after('test_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auditor_statement', function (Blueprint $table) {
            $table->dropColumn(['test_plan', 'test_method', 'test_result', 'test_evidence', 'test_date', 'test_status']);
        });
    }
};
