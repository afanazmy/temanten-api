<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvitationExport  implements FromCollection, WithStyles, WithHeadings, ShouldAutoSize, WithStrictNullComparison, WithColumnFormatting
{
    public function headings(): array
    {
        return [
            'Code', 'Recipient Name', 'Is Group', 'Is Family Member', 'Whatsapp No. #1', 'Whatsapp No. #2'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $phones = DB::table('invitation_phone_numbers')->get();
        $invitations = DB::table('invitations')
            ->select(['id', 'recipient_name', 'is_group', 'is_family_member'])
            ->whereNull('deleted_at')
            ->get();

        $phones = $phones->groupBy('invitation_id')->toArray();

        $invitations = $invitations->map(function ($item, int $key) use ($phones) {
            $item->phone_1 = $phones[$item->id][0]->phone_number ?? null;
            $item->phone_2 = $phones[$item->id][1]->phone_number ?? null;
            return $item;
        });

        return $invitations;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
