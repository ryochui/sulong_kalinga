<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\AdministratorsExport.php

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

class AdministratorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $administratorIds;

    public function __construct($administratorIds)
    {
        $this->administratorIds = $administratorIds;
    }

    public function collection()
    {
        return User::with(['organizationRole'])
            ->whereIn('id', $this->administratorIds)
            ->where('role_id', '1')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Mobile',
            'Organization Role',
            'Area',
            'Address',
            'Status'
        ];
    }

    public function map($administrator): array
    {
        // Helper function to format area nicely
        $formatArea = function($area) {
            if (!$area) return 'N/A';
            // Convert snake_case to Title Case (e.g., luzon_region to Luzon Region)
            return ucwords(str_replace('_', ' ', $area));
        };

        return [
            $administrator->first_name . ' ' . $administrator->last_name,
            $administrator->email ?? 'N/A',
            $administrator->mobile ?? 'N/A',
            $administrator->organizationRole ? ucwords(str_replace('_', ' ', $administrator->organizationRole->role_name)) : 'N/A',
            $formatArea($administrator->organizationRole->area ?? null),
            $administrator->address ?? 'N/A',
            $administrator->volunteer_status ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
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
            'A1:G'.$highestRow => [
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
                $sheet = $event->sheet->getDelegate();
                
                // Apply striped rows for better readability
                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A'.$row.':G'.$row)->applyFromArray([
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
                $event->sheet->getColumnDimension('B')->setWidth(35); // Email
                $event->sheet->getColumnDimension('C')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('D')->setWidth(25); // Organization Role
                $event->sheet->getColumnDimension('E')->setWidth(20); // Area
                $event->sheet->getColumnDimension('F')->setWidth(40); // Address
                $event->sheet->getColumnDimension('G')->setWidth(15); // Status
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:G' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}