<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use App\Exception\ImportFileReadException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractNetflixImportCommand extends Command
{
    protected const USER_ID_PREFIX = 'user_';
    protected const REVIEW_ID_PREFIX = 'review_';
    protected const MOVIE_ID_PREFIX = 'movie_';

    protected const TRUE_VALUE = 'True';

    private const IMPORT_FILE_DIR_NAME = 'import';

    private const BATCH_SIZE = 500;
    protected const DELIMITER = ',';

    protected array $inserts = [];

    protected int $processed = 0;
    protected int $inserted = 0;

    public function handle(): int
    {
        // TODO: move to service csv reader ?
        $delimiter = ',';

        $filePath = sprintf('%s/%s', self::IMPORT_FILE_DIR_NAME, $this->getFileName());

        $handle = fopen($filePath, 'r');

        try {
            if ($handle === false) {
                throw new ImportFileReadException(sprintf('Unable to open file: %s', $filePath));
            }

            // Skip headers
            fgetcsv($handle, 0, $delimiter);

            while (($data = fgetcsv($handle, 0, self::DELIMITER)) !== false) {
                $this->inserts[] = $this->mapRowData($data);

                if (count($this->inserts) === self::BATCH_SIZE) {
                    $this->insertRows();
                }
            }

            if (!empty($this->inserts)) {
                $this->insertRows();
            }
        } catch (\Throwable $exception) {
            $this->error(sprintf('Export has failed. Error: %s', $exception->getMessage()));

            $this->info(sprintf('Processed total: %d', $this->processed));
            $this->info(sprintf('Inserted total: %d', $this->inserted));

            return 1;
        } finally {
            fclose($handle);
        }

        $this->line('');
        $this->info('SUCCESS!');
        $this->info(sprintf('Processed total: %d', $this->processed));
        $this->info(sprintf('Inserted total: %d', $this->inserted));

        return 0;
    }

    #[ArrayShape(['database' => "array", 'batch' => "array"])]
    protected function getDuplicatedInsertKeysInDatabase(string $table, string $field): array
    {
        $items = array_column($this->inserts, $field);

        return DB::table($table)->select($field)->whereIn($field, $items)->pluck($field)->toArray();
    }

    protected function filterUniqueInsertsByField(string $field): array
    {
        $items = array_column($this->inserts, $field);
        $uniqueItems = array_values(array_unique($items));

        $duplicated = array_diff($items, $uniqueItems);

        $this->inserts = array_filter($this->inserts, function (array $row) use ($field, &$uniqueItems) {
            if (in_array($row[$field], $uniqueItems)) {
                $key = array_search($row[$field], $uniqueItems);

                unset($uniqueItems[$key]);

                return true;
            }

            return false;
        });

        return $duplicated;
    }

    private function insertRows(): void
    {
        $isDryRun = $this->option('dry-run');

        $itemsToInsert = $this->getValidatedBatch();

        if (!$isDryRun) {
            DB::table($this->getTableName())->insert($itemsToInsert);

            $this->inserted += count($itemsToInsert);
        }

        $processed = count($this->inserts);

        $this->info(sprintf('Processed %s rows', $processed));

        $this->processed += $processed;

        $this->inserts = [];
    }

    abstract protected function getFileName(): string;

    abstract protected function getTableName(): string;

    abstract protected function getValidatedBatch(): array;

    abstract protected function mapRowData(array $data): array;
}
