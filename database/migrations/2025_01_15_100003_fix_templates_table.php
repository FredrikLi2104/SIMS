<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Kontrollera om kolumnerna redan finns innan vi lägger till dem
        Schema::table('templates', function (Blueprint $table) {
            $columns = Schema::getColumnListing('templates');

            if (!in_array('organization_type', $columns)) {
                $table->string('organization_type')->nullable()->comment('Offentlig/Privat/Ideell etc.');
            }
            if (!in_array('organization_size', $columns)) {
                $table->string('organization_size')->nullable()->comment('Liten/Mellan/Stor');
            }
            if (!in_array('requires_existing_gdpr', $columns)) {
                $table->boolean('requires_existing_gdpr')->default(false)->comment('Kräver befintligt dataskyddsarbete');
            }
            if (!in_array('summary_en', $columns)) {
                $table->text('summary_en')->nullable()->comment('Sammanfattning av vad mallen innehåller');
            }
            if (!in_array('summary_se', $columns)) {
                $table->text('summary_se')->nullable()->comment('Sammanfattning av vad mallen innehåller');
            }
            if (!in_array('estimated_months', $columns)) {
                $table->integer('estimated_months')->nullable()->comment('Förväntad tid att bli klar i månader');
            }
            if (!in_array('is_active', $columns)) {
                $table->boolean('is_active')->default(true)->comment('Om mallen är aktiv och kan väljas');
            }
            if (!in_array('sort_order', $columns)) {
                $table->integer('sort_order')->default(0);
            }
        });

        // Ta bort gamla kolumner om de finns
        if (Schema::hasColumn('templates', 'task_status_id')) {
            Schema::table('templates', function (Blueprint $table) {
                $table->dropForeign(['task_status_id']);
            });
        }

        Schema::table('templates', function (Blueprint $table) {
            $columns = Schema::getColumnListing('templates');

            $columnsToRemove = [];
            if (in_array('start', $columns)) {
                $columnsToRemove[] = 'start';
            }
            if (in_array('end', $columns)) {
                $columnsToRemove[] = 'end';
            }
            if (in_array('hours', $columns)) {
                $columnsToRemove[] = 'hours';
            }
            if (in_array('task_status_id', $columns)) {
                $columnsToRemove[] = 'task_status_id';
            }

            if (count($columnsToRemove) > 0) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Vi återställer inte eftersom det skulle förstöra data
    }
};
