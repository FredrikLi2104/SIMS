<?php

use App\Models\Template;
use App\Models\TaskStatus;
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
        Schema::create('template_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Template::class)->constrained()->onDelete('cascade');
            $table->string('title_en');
            $table->string('title_se');
            $table->text('desc_en')->nullable();
            $table->text('desc_se')->nullable();
            $table->integer('offset_days')->default(0)->comment('Dagar från organisationens skapandedatum');
            $table->integer('duration_days')->default(30)->comment('Längd på uppgiften i dagar');
            $table->decimal('hours', 8, 2)->default(0);
            $table->foreignIdFor(TaskStatus::class)->constrained();
            $table->string('action_type')->nullable()->comment('Plan, Do eller Review');
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('template_tasks');
    }
};
