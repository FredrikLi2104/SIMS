<?php

use App\Models\Plan;
use App\Models\Review;
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
            //
            $table->dropForeignIdFor(Review::class);
            $table->dropColumn('review_id');
            $table->foreignIdFor(Plan::class)->after('statement_id')->constrained();

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
            //
        });
    }
};
