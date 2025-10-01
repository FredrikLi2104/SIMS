<?php

use App\Models\Kpi;
use App\Models\User;
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
        Schema::create('kpicomments', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('target');
            $table->smallInteger('value');
            $table->string('comment');
            $table->foreignIdFor(Kpi::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpicomments');
    }
};
