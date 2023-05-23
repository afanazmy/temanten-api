<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class InvitationImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
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
        $invitationPhoneNumbers = [];

        foreach ($rows as $row) {
            $id = $row['Code'] ?? Str::orderedUuid();

            array_push($invitations, [
                'id' => $id,
                'recipient_name' => $row['Recipient Name'],
                'is_group' => $row['Is Group'],
                'is_family_member' => $row['Is Family Member'],
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ]);

            array_push($invitationPhoneNumbers, [
                'invitation_id' => $id,
                'phone_number' => $row['Whatsapp No. #1']
            ]);

            array_push($invitationPhoneNumbers, [
                'invitation_id' => $id,
                'phone_number' => $row['Whatsapp No. #2']
            ]);
        }

        DB::table('invitations')->delete();
        DB::table('invitations')->insert($invitations);

        DB::table('invitation_phone_numbers')->delete();
        DB::table('invitation_phone_numbers')->insert($invitationPhoneNumbers);
    }
}
