<?php

namespace App\Exports;

use App\Models\Beneficiary;
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

class BeneficiariesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $beneficiaryIds;

    public function __construct($beneficiaryIds)
    {
        $this->beneficiaryIds = $beneficiaryIds;
    }

    public function collection()
    {
        return Beneficiary::with(['category', 'barangay', 'municipality', 'status'])
            ->whereIn('beneficiary_id', $this->beneficiaryIds)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Category',
            'Mobile',
            'Barangay',
            'Municipality',
            'Status',
            'Birth Date',
            'Gender',
            'Address'
        ];
    }

    public function map($beneficiary): array
    {
        return [
            $beneficiary->first_name . ' ' . $beneficiary->last_name,
            $beneficiary->category->category_name ?? 'N/A',
            $beneficiary->mobile ?? 'N/A',
            $beneficiary->barangay->barangay_name ?? 'N/A',
            $beneficiary->municipality->municipality_name ?? 'N/A',
            $beneficiary->status->status_name ?? 'N/A',
            $beneficiary->birthday ? date('F j, Y', strtotime($beneficiary->birthday)) : 'N/A',
            $beneficiary->gender ?? 'N/A',
            $beneficiary->street_address ?? 'N/A',
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
            'A1:I'.$highestRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true,
                    'indent' => 1 // This adds space at the start (padding)
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
                        $sheet->getStyle('A'.$row.':I'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5'] // Light grey for even rows
                            ]
                        ]);
                    }
                }
                
                // Set row height for better padding
                $sheet->getDefaultRowDimension()->setRowHeight(22);
                $sheet->getRowDimension(1)->setRowHeight(26); // Make header slightly taller
                
                // Adjust column widths (slightly increased for better readability)
                $event->sheet->getColumnDimension('A')->setWidth(30); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(20); // Category
                $event->sheet->getColumnDimension('C')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('D')->setWidth(20); // Barangay
                $event->sheet->getColumnDimension('E')->setWidth(20); // Municipality
                $event->sheet->getColumnDimension('F')->setWidth(15); // Status
                $event->sheet->getColumnDimension('G')->setWidth(20); // Birth Date
                $event->sheet->getColumnDimension('H')->setWidth(12); // Gender
                $event->sheet->getColumnDimension('I')->setWidth(40); // Address
                
                // Add auto-filter
                $lastRow = $sheet->getHighestRow();
                $sheet->setAutoFilter('A1:I' . $lastRow);
                
                // Add a bit of top/bottom padding using margin
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}