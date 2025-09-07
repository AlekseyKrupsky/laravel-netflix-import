<?php

declare(strict_types=1);

namespace App\Service\FileReader;

interface FileReaderInterface
{
    public function getRows(string $filePath, array $options = []): \Generator;
    public function closeFileIfOpened(): void;
}
