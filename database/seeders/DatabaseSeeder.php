<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Local
        $this->call([
            //ComponentSeeder::class,
            //CountrySeeder::class,
            //MasterSeeder::class,
            //StatementSeeder::class,
            //UserSeeder::class,
            //OrganisationStatementSeeder::class,
            //TypeSeeder::class,
            //SanctionSeeder::class,
        ]);
    }
}
