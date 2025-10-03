<?php

use App\Models\Component;
use App\Models\StatementType;
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
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->text('content_en');
            $table->text('content_se');
            $table->text('desc_en');
            $table->text('desc_se');
            $table->text('k1_en')->nullable();
            $table->text('k1_se')->nullable();
            $table->text('k2_en')->nullable();
            $table->text('k2_se')->nullable();
            $table->text('k3_en')->nullable();
            $table->text('k3_se')->nullable();
            $table->text('k4_en')->nullable();
            $table->text('k4_se')->nullable();
            $table->text('k5_en')->nullable();
            $table->text('k5_se')->nullable();
            $table->text('guide_en');
            $table->text('guide_se');
            $table->foreignIdFor(Component::class)->constrained();
            $table->foreignIdFor(StatementType::class)->constrained();
            $table->smallInteger('sort_order')->unique();
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
        Schema::dropIfExists('statements');
    }
};
