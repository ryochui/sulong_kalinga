<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\CareManagersExport.php

namespace App\Exports;

use App\Models\User;
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

class CareManagersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $careManagerIds;

    public function __construct($careManagerIds)
    {
        $this->careManagerIds = $careManagerIds;
    }

    public function collection()
    {
        return User::with(['municipality'])
            ->whereIn('id', $this->careManagerIds)
            ->where('role_id', '2')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Mobile',
            'Municipality',
            'Address',
            'Status'
        ];
    }

    public function map($careManager): array
    {
        return [
            $careManager->first_name . ' ' . $careManager->last_name,
            $careManager->email ?? 'N/A',
            $careManager->mobile ?? 'N/A',
            $careManager->municipality->municipality_name ?? 'N/A',
            $careManager->address ?? 'N/A',
            $careManager->volunteer_status ?? 'N/A'
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
            
            // Style for all cells
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
                $event->sheet->getColumnDimension('B')->setWidth(30); // Email
                $event->sheet->getColumnDimension('C')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('D')->setWidth(20); // Municipality
                $event->sheet->getColumnDimension('E')->setWidth(40); // Address
                $event->sheet->getColumnDimension('F')->setWidth(15); // Status
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:F' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}