<?php

use App\Models\Statement;
use App\Models\Template;
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
        Schema::create('template_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Template::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Statement::class)->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Prevent duplicate template-statement combinations
            $table->unique(['template_id', 'statement_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_statements');
    }
};
