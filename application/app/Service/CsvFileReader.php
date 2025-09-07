<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ImportFileReadException;

class CsvFileReader
{
    protected const DEFAULT_DELIMITER = ',';
    protected const DEFAULT_ROW_LENGTH = 4096;

    private mixed $handle = null;

    public function openFile(string $filePath): void
    {
        $this->handle = fopen($filePath, 'r');

        if ($this->handle === false) {
            throw new ImportFileReadException(sprintf('Unable to open file: %s', $filePath));
        }
    }

    public function skipLine(): void
    {
        if (!$this->handle) {
            throw new ImportFileReadException('Unable to skip line. Handle is not set.');
        }

        fgetcsv($this->handle);
    }

    public function getRows(string $delimiter = self::DEFAULT_DELIMITER): \Generator
    {
        if (!$this->handle) {
            throw new ImportFileReadException('Unable to get row. Handle is not set.');
        }

        while (feof($this->handle) === false) {
            yield fgetcsv($this->handle, self::DEFAULT_ROW_LENGTH, $delimiter);
        }
    }

    public function closeFileIfOpened(): void
    {
        if ($this->handle) {
            fclose($this->handle);
        }
    }
}
