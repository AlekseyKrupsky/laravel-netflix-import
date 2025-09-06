<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use App\Exception\ImportFileReadException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

abstract class AbstractNetflixImportCommand extends Command
{
    protected const USER_ID_PREFIX = 'user_';
    protected const REVIEW_ID_PREFIX = 'review_';
    protected const MOVIE_ID_PREFIX = 'movie_';

    protected const TRUE_VALUE = 'True';

    protected const BATCH_SIZE = 500;
    protected const DELIMITER = ',';

    protected array $inserts = [];

    protected int $processed = 0;
    protected int $inserted = 0;

    public function handle(): int
    {
        // service csv opener !
        $delimiter = ',';

        $handle = fopen($this->getFileName(), 'r');

        try {
            if ($handle === false) {
                throw new ImportFileReadException(sprintf('Unable to open file: %s', $this->getFileName()));
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
            $this->error($exception->getMessage());

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

    protected function getDuplicatedInsertKeys(string $table, string $field): array
    {
        $items = array_column($this->inserts, $field);

        return DB::table($table)->select($field)->whereIn($field, $items)->pluck($field)->toArray();
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
