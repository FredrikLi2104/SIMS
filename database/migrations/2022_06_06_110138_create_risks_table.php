<?php

use App\Models\Organisation;
use App\Models\Statement;
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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('desc');
            $table->smallInteger('probability');
            $table->smallInteger('consequence');
            $table->string('responsible')->nullable();
            $table->foreignIdFor(Organisation::class)->constrained();
            $table->foreignIdFor(Statement::class)->constrained();
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
        Schema::dropIfExists('risks');
    }
};
