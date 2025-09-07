<?php

declare(strict_types=1);

namespace App\Console\Commands\NetflixImport;

use App\Enum\User\Gender;
use App\Enum\User\PrimaryDevice;
use App\Enum\User\SubscriptionPlan;
use App\Exception\ImportRowValidationFailed;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $id = intval(str_replace(self::USER_ID_PREFIX, '', $data[0]));

        $row = [
            'id' => $id,
            'email' => $data[1],
            'first_name' => $data[2],
            'last_name' => $data[3],
            'age' => intval($data[4]) ?: null,
            'gender' => $data[5] ?: null,
            'country' => $data[6],
            'state_province' => $data[7],
            'city' => $data[8],
            'subscription_plan' => $data[9],
            'subscription_start_date' => $data[10],
            'is_active' => $data[11] === self::TRUE_VALUE,
            'monthly_spend' => $data[12] ?: null,
            'primary_device' => $data[13],
            'household_size' => intval($data[14]) ?: null,
            'created_at' => $data[15],
        ];

        $validator = Validator::make($row, [
            'id' => 'required|integer|min:0',
            'email' => 'required|email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:0|max:125',
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'country' => 'required|string|max:20',
            'state_province' => 'required|string|max:25',
            'city' => 'required|string|max:25',
            'subscription_plan' => ['required', Rule::enum(SubscriptionPlan::class)],
            'subscription_start_date' => 'required|date',
            'is_active' => 'required|boolean',
            'monthly_spend' => ['nullable', Rule::numeric()->min(0.00)->max(9999.99)->decimal(0, 2)],
            'primary_device' => ['required', Rule::enum(PrimaryDevice::class)],
            'household_size' => 'nullable|integer|min:0',
            'created_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            $details = json_encode($validator->errors());

            throw new ImportRowValidationFailed(sprintf('Validation failed. Row id: %s. Details: %s %s', $id, PHP_EOL, $details));
        }

        return $row;
    }
}
