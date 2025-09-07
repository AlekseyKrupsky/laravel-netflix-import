<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use App\Enum\Review\DeviceType;
use App\Enum\Review\Sentiment;
use App\Exception\ImportRowValidationFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReviewsImportCommand extends AbstractNetflixImportCommand
{
    protected $signature = 'app:reviews-import {--dry-run : Execute the command in dry-run mode without making actual changes.}';

    protected $description = 'Imports reviews from csv file.';

    protected function getFileName(): string
    {
        return 'reviews.csv';
    }

    protected function getTableName(): string
    {
        return 'reviews';
    }

    protected function getValidatedBatch(): array
    {
        $duplicatedIdsInBatch = $this->filterUniqueInsertsByField('id');
        $duplicatedIds = $this->getDuplicatedInsertKeysInDatabase('reviews', 'id');

        foreach (array_merge($duplicatedIds, $duplicatedIdsInBatch) as $duplicatedId) {
            $this->warn(sprintf('Skip row with duplicated id: %s', $duplicatedId));
        }

        $notExistedUsers = $this->getNotExistedRelations('users', 'user_id');
        $notExistedMovies = $this->getNotExistedRelations('movies', 'movie_id');

        foreach ($notExistedUsers as $notExistedUser) {
            $this->warn(sprintf('Skip review due to not existed user id: %s', $notExistedUser));
        }

        foreach ($notExistedMovies as $notExistedMovie) {
            $this->warn(sprintf('Skip review due to not existed movie id: %s', $notExistedMovie));
        }

        return array_filter(
            $this->inserts,
            static fn (array $item) =>
                !in_array($item['id'], $duplicatedIds)
                && !in_array($item['user_id'], $notExistedUsers)
                && !in_array($item['movie_id'], $notExistedMovies)
        );
    }

    protected function mapRowData(array $data): array
    {
        $id = intval(str_replace(self::REVIEW_ID_PREFIX, '', $data[0]));

        $row = [
            'id' => $id,
            'user_id' => intval(str_replace(self::USER_ID_PREFIX, '', $data[1])),
            'movie_id' => intval(str_replace(self::MOVIE_ID_PREFIX, '', $data[2])),
            'rating' => $data[3],
            'review_date' => $data[4],
            'device_type' => $data[5],
            'is_verified_watch' => $data[6] === self::TRUE_VALUE,
            'helpful_votes' => intval($data[7]) ?: null,
            'total_votes' => intval($data[8]) ?: null,
            'review_text' => $data[9] ?: null,
            'sentiment' => $data[10],
            'sentiment_score' => $data[11] ?: null,
        ];

        $validator = Validator::make($row, [
            'id' => 'required|integer|min:0',
            'user_id' => 'required|integer|min:0',
            'movie_id' => 'required|integer|min:0',
            'rating' => 'required|integer|min:0|max:5',
            'review_date' => 'required|date',
            'device_type' => ['required', Rule::enum(DeviceType::class)],
            'is_verified_watch' => 'required|boolean',
            'helpful_votes' => 'nullable|integer|min:0|max:65535',
            'total_votes' => 'nullable|integer|min:0|max:65535',
            'review_text' => 'nullable|string|max:255',
            'sentiment' => ['required', Rule::enum(Sentiment::class)],
            'sentiment_score' => ['nullable', Rule::numeric()->min(0.000)->max(1.000)->decimal(0, 3)],
        ]);

        if ($validator->fails()) {
            $details = json_encode($validator->errors());

            throw new ImportRowValidationFailed(sprintf('Validation failed. Row id: %s. Details: %s %s', $id, PHP_EOL, $details));
        }

        return $row;
    }

    private function getNotExistedRelations(string $table, string $field): array
    {
        $ids = array_column($this->inserts, $field);
        $existed = DB::table($table)->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

        return array_diff($ids, $existed);
    }
}
