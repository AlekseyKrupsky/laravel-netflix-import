<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

class UsersImportCommand extends AbstractNetflixImportCommand
{
    protected $signature = 'app:users-import {--dry-run : Execute the command in dry-run mode without making actual changes.}';

    protected $description = 'Imports users from csv file.';

    protected function getFileName(): string
    {
        return 'users.csv';
    }

    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getValidatedBatch(): array
    {
        $duplicatedIdsInBatch = $this->filterUniqueInsertsByField('id');
        $duplicatedEmailsInBatch = $this->filterUniqueInsertsByField('email');

        $duplicatedIds = $this->getDuplicatedInsertKeysInDatabase('users', 'id');
        $duplicatedEmails = $this->getDuplicatedInsertKeysInDatabase('users', 'email');

        foreach (array_merge($duplicatedIds, $duplicatedIdsInBatch) as $duplicatedId) {
            $this->warn(sprintf('Skip row with duplicated id: %s', $duplicatedId));
        }

        foreach (array_merge($duplicatedEmails, $duplicatedEmailsInBatch) as $duplicatedEmail) {
            $this->warn(sprintf('Skip row with duplicated email: %s', $duplicatedEmail));
        }

        return array_filter(
            $this->inserts,
            static fn ($item) =>
                !in_array($item['id'], $duplicatedIds)
                && !in_array($item['email'], $duplicatedEmails)
        );
    }

    protected function mapRowData(array $data): array
    {
        $age = $data[4] ?: null;
        $id = intval(str_replace(self::USER_ID_PREFIX, '', $data[0]));

        if ($age !== null && $age < 0) {
            $this->warn(sprintf('Invalid age (%s) fallback to null. Row id: %s', $age, $id));

            $age = null;
        }

        return [
            'id' => $id,
            'email' => $data[1],
            'first_name' => $data[2],
            'last_name' => $data[3],
            'age' => $age,
            'gender' => $data[5] ?: null,
            'country' => $data[6],
            'state_province' => $data[7],
            'city' => $data[8],
            'subscription_plan' => $data[9],
            'subscription_start_date' => $data[10],
            'is_active' => $data[11] === self::TRUE_VALUE,
            'monthly_spend' => $data[12] ?: null,
            'primary_device' => $data[13],
            'household_size' => $data[14] ?: null,
            'created_at' => $data[15],
        ];
    }
}
