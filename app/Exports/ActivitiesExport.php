<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitiesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $query;

    protected ?string $title = 'Activities';

    public function __construct($query = null, ?string $title = null)
    {
        $this->query = $query ?? Activity::query();
        if ($title) {
            $this->title = $title;
        }
    }

    public function query()
    {
        return $this->query->with(['user', 'customer', 'contact']);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'Type',
            'Sales Rep',
            'Customer',
            'Contact Person',
            'Subject',
            'Outcome',
            'Confidence',
            'Worth Keeping',
            'Started At',
            'Ended At',
            'Location',
            'Notes',
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->performed_at?->format('d M Y H:i') ?? '-',
            $activity->type,
            $activity->user?->name ?? '-',
            $activity->customer?->facility_name ?? '-',
            $activity->contact?->name ?? '-',
            $activity->subject,
            $activity->outcome ?? '-',
            $activity->confidence_level.'%',
            $activity->is_worth_keeping ? 'Yes' : 'No',
            $activity->visit_started_at?->format('H:i') ?? '-',
            $activity->visit_ended_at?->format('H:i') ?? '-',
            $activity->location ?? '-',
            $activity->description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}