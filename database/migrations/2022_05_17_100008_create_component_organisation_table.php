<?php

use App\Models\Component;
use App\Models\Organisation;
use App\Models\Period;
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
        Schema::create('component_organisation', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Component::class)->constrained();
            $table->foreignIdFor(Organisation::class)->constrained();
            $table->foreignIdFor(Period::class)->constrained();
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
        Schema::dropIfExists('component_organisation');
    }
};
