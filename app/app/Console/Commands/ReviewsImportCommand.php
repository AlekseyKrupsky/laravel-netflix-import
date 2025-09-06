<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReviewsImportCommand extends Command
{
    private const BATCH_SIZE = 1;

    protected $signature = 'app:reviews-import';

    protected $description = 'Command description'; // update

    public function handle(): int
    {
        $delimiter = ',';

        $handle = fopen('reviews.csv', 'r');

        if ($handle === false) {
            // error
        }

        $headers = fgetcsv($handle, 0, $delimiter);

        $inserts = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {

            $inserts[] = [
                'id' => intval(str_replace('review_', '', $data[0])),
                'user_id' => intval(str_replace('user_', '', $data[1])), // use const
                'movie_id' => intval(str_replace('movie_', '', $data[2])), // use const
                'rating' => $data[3],
                'review_date' => $data[4],
                'device_type' => $data[5],
                'is_verified_watch' => $data[6] === 'True',
                'helpful_votes' => $data[7] ?: null,
                'total_votes' => $data[8] ?: null,
                'review_text' => $data[9],
                'sentiment' => $data[10],
                'sentiment_score' => $data[11] ?: null,
            ];

            if (count($inserts) === self::BATCH_SIZE) {
                // try catch / batch failed

                $ids = array_column($inserts, 'id');

                $duplicatedIds = DB::table('reviews')->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

//                dump($ids);
//                dump($duplicatedIds);

                foreach ($duplicatedIds as $duplicatedId) {
                    echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
                }


                $userIds = array_column($inserts, 'user_id');
                $existedUsers = DB::table('users')->select('id')->whereIn('id', $userIds)->pluck('id')->toArray();
                $notExistedUsers = array_diff($userIds, $existedUsers);

                foreach ($notExistedUsers as $notExistedUser) {
                    echo sprintf('Skip review due to not existed user id: %s', $notExistedUser).PHP_EOL; // change
                }

                $uniqueInserts = array_filter($inserts, fn ($item) => !in_array($item['id'], $duplicatedIds) && !in_array($item['user_id'], $notExistedUsers));

                DB::table('reviews')->insert($uniqueInserts);

                $inserts = [];
            }
        }

        if (!empty($inserts)) {
            // dup code
            $ids = array_column($inserts, 'id');

            $duplicatedIds = DB::table('reviews')->select('id')->where('id', 'in', $ids)->get()->toArray();

            foreach ($duplicatedIds as $duplicatedId) {
                echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
            }

            $uniqueInserts = array_filter($inserts, fn ($item) => !in_array($item['id'], $duplicatedIds));

            DB::table('reviews')->insert($uniqueInserts);
        }

        return 0;
    }
}
