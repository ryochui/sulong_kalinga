<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\FamilyMembersExport.php

namespace App\Exports;

use App\Models\FamilyMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FamilyMembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $familyMemberIds;

    public function __construct($familyMemberIds)
    {
        $this->familyMemberIds = $familyMemberIds;
    }

    public function collection()
    {
        return FamilyMember::with(['beneficiary'])
            ->whereIn('family_member_id', $this->familyMemberIds)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Mobile Number',
            'Relationship',
            'Registered Beneficiary',
            'Access Status',
            'Address'
        ];
    }

    public function map($familyMember): array
    {
        return [
            $familyMember->first_name . ' ' . $familyMember->last_name,
            $familyMember->mobile ?? 'N/A',
            $familyMember->relationship ?? 'N/A',
            $familyMember->beneficiary->first_name . ' ' . $familyMember->beneficiary->last_name,
            $familyMember->status ?? 'N/A',
            $familyMember->address ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest row number
        $highestRow = $sheet->getHighestRow();
        
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, 
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9']
                    ]
                ]
            ],
            
            // Style for all cells - add padding, borders, and alignment
            'A1:F'.$highestRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true,
                    'indent' => 1
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9']
                    ],
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get worksheet
                $sheet = $event->sheet->getDelegate();
                
                // Apply striped rows for better readability
                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5'] // Light grey for even rows
                            ]
                        ]);
                    }
                }
                
                // Set row height for better padding
                $sheet->getDefaultRowDimension()->setRowHeight(22);
                $sheet->getRowDimension(1)->setRowHeight(26);
                
                // Adjust column widths
                $event->sheet->getColumnDimension('A')->setWidth(30); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(20); // Mobile
                $event->sheet->getColumnDimension('C')->setWidth(20); // Relationship
                $event->sheet->getColumnDimension('D')->setWidth(30); // Beneficiary
                $event->sheet->getColumnDimension('E')->setWidth(15); // Status
                $event->sheet->getColumnDimension('F')->setWidth(40); // Address
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:F' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}