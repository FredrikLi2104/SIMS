<?php

use App\Models\Article;
use App\Models\Sanction;
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
        Schema::create('article_sanction', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Article::class)->constrained();
            $table->foreignIdFor(Sanction::class)->constrained();
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
        Schema::dropIfExists('article_sanction');
    }
};
