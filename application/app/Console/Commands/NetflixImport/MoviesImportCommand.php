<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use App\Exception\ImportRowValidationFailed;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $id = intval(str_replace(self::MOVIE_ID_PREFIX, '', $data[0]));

        $row = [
            'id' => $id,
            'title' => $data[1],
            'content_type' => $data[2],
            'genre_primary' => $data[3] ?: null,
            'genre_secondary' => $data[4] ?: null,
            'release_year' => $data[5],
            'duration_minutes' => intval($data[6]),
            'rating' => $data[7],
            'language' => $data[8],
            'country_of_origin' => $data[9],
            'imdb_rating' => $data[10] ?: null,
            'production_budget' => $data[11] ?: null,
            'box_office_revenue' => $data[12] ?: null,
            'number_of_seasons' => intval($data[13]) ?: null,
            'number_of_episodes' => intval($data[14]) ?: null,
            'is_netflix_original' => $data[15] === self::TRUE_VALUE,
            'added_to_platform' => $data[16],
            'content_warning' => $data[17] === self::TRUE_VALUE,
        ];

        $validator = Validator::make($row, [
            'id' => 'required|integer|min:0',
            'title' => 'required|string|max:100',
            'genre_primary' => 'nullable|string|max:100',
            'genre_secondary' => 'nullable|string|max:100',
            'release_year' => 'required|date_format:Y',
            'duration_minutes' => 'required|integer|min:0',
            'rating' => 'required|string|max:10',
            'language' => 'required|string|max:15',
            'country_of_origin' => 'required|string|max:20',
            'imdb_rating' => ['nullable', Rule::numeric()->min(0.0)->max(10.0)->decimal(1)],
            'production_budget' => ['nullable', Rule::numeric()->min(0.0)->max(9999999999999.9)->decimal(1)],
            'box_office_revenue' => ['nullable', Rule::numeric()->min(0.0)->max(9999999999999.9)->decimal(1)],
            'number_of_seasons' => 'nullable|integer|min:1',
            'number_of_episodes' => 'nullable|integer|min:1',
            'is_netflix_original' => 'required|boolean',
            'added_to_platform' => 'required|date',
            'content_warning' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $details = json_encode($validator->errors());

            throw new ImportRowValidationFailed(sprintf('Validation failed. Row id: %s. Details: %s %s', $id, PHP_EOL, $details));
        }

        return $row;
    }
}
