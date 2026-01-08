<?php

namespace App\Console\Commands;

use App\Models\Genres;
use App\Traits\GlobalTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use GlobalTrait;
    protected $signature = 'app:fetch-genres';

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
        try {
            DB::beginTransaction();
            $client = new Client();
            $api_key = config('app.api_key_tmdb');

            $response = $client->request('GET', "https://api.themoviedb.org/3/genre/movie/list?api_key={$api_key}&language=en-US");

            if ($response->getStatusCode() >= 400) {
                throw new Exception($response->getBody());
            }

            $data = json_decode($response->getBody(), true);


            foreach ($data['genres'] as $genre) {
                $genre = [
                    'id' => $genre['id'],
                    'name' => $genre['name'],
                ];
                Genres::updateOrCreate(['id' => $genre['id']], $genre);
            }

            DB::commit();

            $this->devResponseSuccess('Genres fetched successfully');
        } catch (Exception $e) {
            DB::rollBack();
            $this->devResponse($e);
        }
    }
}
