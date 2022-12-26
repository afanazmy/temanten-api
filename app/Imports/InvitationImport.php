<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvitationImport implements ToCollection, WithHeadingRow
{
    const ADD = 'add';
    const REPLACE = 'replace';

    private $importType;

    public function __construct(string $importType = null)
    {
        $this->importType = $importType ?? $this::REPLACE;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $duplicates = $rows->duplicates('Recipient Name');

        if (count($duplicates) > 0) {
            throw ValidationException::withMessages(['Recipient Name' => "Duplicate data"]);
        }

        $invitations = [];

        foreach ($rows as $row) {
            array_push($invitations, [
                'id' => $row['Code'] ?? Str::orderedUuid(),
                'recipient_name' => $row['Recipient Name'],
                'is_group' => $row['Is Group'],
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ]);
        }

        DB::table('invitations')->delete();
        DB::table('invitations')->insert($invitations);
    }
}
