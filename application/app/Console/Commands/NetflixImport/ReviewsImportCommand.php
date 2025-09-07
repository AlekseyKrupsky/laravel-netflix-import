<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use Illuminate\Support\Facades\DB;

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
        // TODO DTO + validation
        return [
            'id' => intval(str_replace(self::REVIEW_ID_PREFIX, '', $data[0])),
            'user_id' => intval(str_replace(self::USER_ID_PREFIX, '', $data[1])),
            'movie_id' => intval(str_replace(self::MOVIE_ID_PREFIX, '', $data[2])),
            'rating' => $data[3],
            'review_date' => $data[4],
            'device_type' => $data[5],
            'is_verified_watch' => $data[6] === self::TRUE_VALUE,
            'helpful_votes' => $data[7] ?: null,
            'total_votes' => $data[8] ?: null,
            'review_text' => $data[9],
            'sentiment' => $data[10],
            'sentiment_score' => $data[11] ?: null,
        ];
    }

    private function getNotExistedRelations(string $table, string $field): array
    {
        $ids = array_column($this->inserts, $field);
        $existed = DB::table($table)->select('id')->whereIn('id', $ids)->pluck('id')->toArray();

        return array_diff($ids, $existed);
    }
}
