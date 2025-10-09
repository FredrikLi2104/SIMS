<?php

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
        Schema::table('organisations', function (Blueprint $table) {
            $table->foreignIdFor(Template::class, 'onboarding_template_id')
                ->nullable()
                ->after('id')
                ->constrained('templates')
                ->nullOnDelete()
                ->comment('Mall som användes vid onboarding');

            $table->timestamp('onboarding_completed_at')
                ->nullable()
                ->after('onboarding_template_id')
                ->comment('När onboarding slutfördes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropForeign(['onboarding_template_id']);
            $table->dropColumn(['onboarding_template_id', 'onboarding_completed_at']);
        });
    }
};
