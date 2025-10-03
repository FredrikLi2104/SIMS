<?php

namespace App\Console\Commands;

use App\Models\Dpa;
use App\Services\SanctionLoader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SanctionsLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:sanctions-load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'go to gdpr hub, load DPAs and sanctions for each DPA';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SanctionLoader $sanctionLoader)
    {
        try {
            $data = $sanctionLoader->loadSanctions();
            //$this->info(var_dump($data));
            return 0;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
