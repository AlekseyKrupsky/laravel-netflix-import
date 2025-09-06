<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoviesImportCommand extends Command
{
    private const BATCH_SIZE = 500;
    // const True
    // check consts !

    protected $signature = 'app:movies-import';

    protected $description = 'Command description'; // update

    public function handle(): int
    {
        // dry run

        $delimiter = ',';

        $handle = fopen('movies.csv', 'r');

        if ($handle === false) {
            // error
        }

        // skip headers
        $headers = fgetcsv($handle, 0, $delimiter);

//        $headers[0] = 'id';


//        $insert


//        var_dump($headers);




//        $row = fgetcsv($handle, 0, $delimiter);



//        var_dump($insert);


//        dump($headers);
//        dump($row);

        $inserts = [];
//        $idsToInsert = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
//            foreach ($headers as $key => $header) {
//                $insert[$header] = $data[$key];
//            }

//            $insert = [
//                'id' => $data['movie_id'],
//                'title' => $data['title'],
//                'content_type' => $data['content_type'],
//                'genre_primary' => $data['genre_primary'] ?: null,
//                'genre_secondary' => $data['genre_secondary'] ?: null,
//                'release_year' => $data['release_year'],
//                'duration_minutes' => $data['duration_minutes'],
//                'rating' => $data['rating'],
//                'language' => $data['language'],
//                'country_of_origin' => $data['country_of_origin'],
//                'imdb_rating' => $data['imdb_rating'] ?: null,
//                'production_budget' => $data['production_budget'] ?: null,
//                'box_office_revenue' => $data['box_office_revenue'] ?: null,
//                'number_of_seasons' => $data['number_of_seasons'] ?: null,
//                'number_of_episodes' => $data['number_of_episodes'] ?: null,
//                'is_netflix_original' => $data['is_netflix_original'] === 'True',
//                'added_to_platform' => $data['added_to_platform'],
//                'content_warning' => $data['content_warning'] === 'True',
//            ];

// use dto ?

            $inserts[] = [
                'id' => intval(str_replace('movie_', '', $data[0])),
                'title' => $data[1],
                'content_type' => $data[2],
                'genre_primary' => $data[3] ?: null,
                'genre_secondary' => $data[4] ?: null,
                'release_year' => $data[5],
                'duration_minutes' => $data[6],
                'rating' => $data[7],
                'language' => $data[8],
                'country_of_origin' => $data[9],
                'imdb_rating' => $data[10] ?: null,
                'production_budget' => $data[11] ?: null,
                'box_office_revenue' => $data[12] ?: null,
                'number_of_seasons' => $data[13] ?: null,
                'number_of_episodes' => $data[14] ?: null,
                'is_netflix_original' => $data[15] === 'True',
                'added_to_platform' => $data[16],
                'content_warning' => $data[17] === 'True',
            ];


            // Convert string if movie_0001 to int 1
//            $insert['id'] = intval(str_replace('movie_', '', $insert['id']));

//            $inserts[] = array_map(function ($item) {
//                if ($item === '') {
//                    return null;
//                }
//
//                return $item;
//            }, $insert);

//            $inserts[] = $insert;

            if (count($inserts) === self::BATCH_SIZE) {
                // try catch / batch failed

                $ids = array_column($inserts, 'id');

                $duplicatedIds = DB::table('movies')->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

//                dump($ids);
//                dump($duplicatedIds);

                foreach ($duplicatedIds as $duplicatedId) {
                    echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
                }

                $uniqueInserts = array_filter($inserts, fn ($item) => !in_array($item['id'], $duplicatedIds));

                DB::table('movies')->insert($uniqueInserts);

                $inserts = [];
            }
        }

        if (!empty($inserts)) {
            // dup code
            $ids = array_column($inserts, 'id');

            $duplicatedIds = DB::table('movies')->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

            foreach ($duplicatedIds as $duplicatedId) {
                echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
            }

            $uniqueInserts = array_filter($inserts, fn ($item) => !in_array($item['id'], $duplicatedIds));

            DB::table('movies')->insert($uniqueInserts);
        }

        return 0;
    }
}
