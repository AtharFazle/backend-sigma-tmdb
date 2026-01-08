<?php

namespace App\Console\Commands;


use App\Helpers\Movie\MovieHelper;
use Illuminate\Console\Command;

class FetchMovie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-movie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $helper = new MovieHelper();
        $data = $helper->fetchMovie();

        if (!$data->status) {
            $this->error($data->message);
            return;
        }

        $this->info($data->message);
    }
}
