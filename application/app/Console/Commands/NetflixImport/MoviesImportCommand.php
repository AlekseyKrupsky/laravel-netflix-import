<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

class MoviesImportCommand extends AbstractNetflixImportCommand
{
    protected $signature = 'app:movies-import {--dry-run : Execute the command in dry-run mode without making actual changes.}';

    protected $description = 'Imports movies from csv file.';

    protected function getFileName(): string
    {
        return 'movies.csv';
    }

    protected function getTableName(): string
    {
        return 'movies';
    }

    protected function getValidatedBatch(): array
    {
        $duplicatedIdsInBatch = $this->filterUniqueInsertsByField('id');
        $duplicatedIds = $this->getDuplicatedInsertKeysInDatabase('movies', 'id');

        foreach (array_merge($duplicatedIds, $duplicatedIdsInBatch) as $duplicatedId) {
            $this->warn(sprintf('Skip row with duplicated id: %s', $duplicatedId));
        }

        return array_filter($this->inserts, static fn ($item) => !in_array($item['id'], $duplicatedIds));
    }

    protected function mapRowData(array $data): array
    {
        // TODO DTO + validation
        return [
            'id' => intval(str_replace(self::MOVIE_ID_PREFIX, '', $data[0])),
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
            'is_netflix_original' => $data[15] === self::TRUE_VALUE,
            'added_to_platform' => $data[16],
            'content_warning' => $data[17] === self::TRUE_VALUE,
        ];
    }
}
