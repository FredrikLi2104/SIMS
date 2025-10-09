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
        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned')->after('emails');
            $table->dateTime('scheduled_date')->nullable()->after('status');
            $table->dateTime('conducted_date')->nullable()->after('scheduled_date');
            $table->text('notes')->nullable()->after('conducted_date');
            $table->text('attachments')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['status', 'scheduled_date', 'conducted_date', 'notes', 'attachments']);
        });
    }
};
