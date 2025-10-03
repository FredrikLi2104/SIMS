<?php

use App\Models\TemplateAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmpl_actionables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TemplateAction::class)->constrained();
            $table->morphs('tmpl_actionable');
            $table->softDeletes();
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
        Schema::dropIfExists('tmpl_actionables');
    }
};
