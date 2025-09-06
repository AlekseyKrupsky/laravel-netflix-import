<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UsersImportCommand extends Command
{
    private const BATCH_SIZE = 5;

    protected $signature = 'app:users-import';

    protected $description = 'Command description';

    public function handle(): int
    {
        $delimiter = ',';

        $handle = fopen('users.csv', 'r');

        if ($handle === false) {
            // error
        }

        $headers = fgetcsv($handle, 0, $delimiter);

        $inserts = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $age = $data[4] ?: null;

            if ($age < 0) {
                // valid error, skip
                echo 'Skip age '.$age.PHP_EOL; // + id
            } else {
                $inserts[] = [
                    'id' => intval(str_replace('user_', '', $data[0])),
                    'email' => $data[1],
                    'first_name' => $data[2],
                    'last_name' => $data[3],
                    'age' => $data[4] ?: null,
                    'gender' => $data[5] ?: null,
                    'country' => $data[6],
                    'state_province' => $data[7],
                    'city' => $data[8],
                    'subscription_plan' => $data[9],
                    'subscription_start_date' => $data[10],
                    'is_active' => $data[11] === 'True',
                    'monthly_spend' => $data[12] ?: null,
                    'primary_device' => $data[13],
                    'household_size' => $data[14] ?: null,
                    'created_at' => $data[15],
                ];
            }



            if (count($inserts) === self::BATCH_SIZE) {
                $ids = array_column($inserts, 'id');

                $duplicatedIds = DB::table('users')->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

//                dump($ids);
//                dump($duplicatedIds);

                foreach ($duplicatedIds as $duplicatedId) {
                    echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
                }


                $emails = array_column($inserts, 'email');

                $duplicatedEmails = DB::table('users')->select('email')->whereIn('email', $emails)->pluck('email')->toArray();

                foreach ($duplicatedEmails as $duplicatedEmail) {
                    echo sprintf('Skip duplicated email: %s', $duplicatedEmail).PHP_EOL; // change
                }

                $uniqueInserts = array_filter($inserts, static fn ($item) => !in_array($item['id'], $duplicatedIds) && !in_array($item['email'], $duplicatedEmails));

                DB::table('users')->insert($uniqueInserts);

                $inserts = [];
            }
        }

        if (!empty($inserts)) {
            // dup code
            $ids = array_column($inserts, 'id');

            $duplicatedIds = DB::table('users')->select('id')->where('id', 'in', $ids)->get()->toArray();

            foreach ($duplicatedIds as $duplicatedId) {
                echo sprintf('Skip duplicated id: %s', $duplicatedId).PHP_EOL; // change
            }

            $emails = array_column($inserts, 'email');

            $duplicatedEmails = DB::table('users')->select('email')->whereIn('email', $emails)->pluck('email')->toArray();

            foreach ($duplicatedEmails as $duplicatedEmail) {
                echo sprintf('Skip duplicated email: %s', $duplicatedEmail).PHP_EOL; // change
            }

            $uniqueInserts = array_filter($inserts, static fn ($item) => !in_array($item['id'], $duplicatedIds) && !in_array($item['email'], $duplicatedEmails));

            DB::table('users')->insert($uniqueInserts);
        }

        return 0;
    }
}
