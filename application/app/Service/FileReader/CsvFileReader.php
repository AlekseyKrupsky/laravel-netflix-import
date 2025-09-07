<?php

declare(strict_types=1);

namespace App\Service\FileReader;

use App\Exception\ImportFileReadException;

class CsvFileReader implements FileReaderInterface
{
    protected const DEFAULT_DELIMITER = ',';
    protected const DEFAULT_ROW_LENGTH = 4096;

    private mixed $handle = null;

    public function getRows(string $filePath, array $options = []): \Generator
    {
        $this->handle = fopen($filePath, 'r');

        if ($this->handle === false) {
            throw new ImportFileReadException(sprintf('Unable to open file: %s', $filePath));
        }

        if ($options['skip_headers'] ?? false) {
            fgetcsv($this->handle);
        }

        $delimiter = $options['delimiter'] ?? self::DEFAULT_DELIMITER;

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
