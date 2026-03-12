<?php

use Core\Database;

// Load PhpSpreadsheet
require_once base_path('vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls; // Changed from Xlsx to Xls
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$config = require base_path('config/config.php');
$db = new Database($config['database']);

// Get format parameter (default to xls)
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'xls';

if ($format === 'csv') {
    // Generate CSV (for reference only)
    generateCSVTemplate($db);
} else {
    // Generate XLS (default - for upload)
    generateXLSTemplate($db);
}

function generateXLSTemplate($db)
{
    try {
        // Get all employees with their shift information
        $employees = $db->query("
            SELECT 
                e.employee_number,
                e.full_name as employee_name,
                e.department,
                COALESCE(e.shift_id, 1) as shift_id
            FROM employees e
            WHERE e.onboarding_status IN ('Onboarding', 'In Progress', 'Onboarded')
            ORDER BY e.department ASC, e.full_name ASC
        ")->find();

        if (empty($employees)) {
            throw new Exception("No employees found to generate template");
        }

        // Handle single record
        if (isset($employees['employee_number'])) {
            $employees = [$employees];
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Schedule Template');

        // Set column headers
        $headers = [
            'A1' => 'employee_id',
            'B1' => 'employee_name',
            'C1' => 'date',
            'D1' => 'time_in',
            'E1' => 'time_out',
            'F1' => 'shift_id',
            'G1' => 'department',
            'H1' => 'shift_code'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Add data rows
        $row = 2;
        foreach ($employees as $employee) {
            $shiftId = $employee['shift_id'] ?? 1;

            // Set time in/out based on shift_id
            switch ($shiftId) {
                case 1:
                    $timeIn = '06:00';
                    $timeOut = '14:00';
                    $shiftCode = 'MORNING';
                    break;
                case 2:
                    $timeIn = '14:00';
                    $timeOut = '22:00';
                    $shiftCode = 'AFTERNOON';
                    break;
                case 3:
                    $timeIn = '22:00';
                    $timeOut = '06:00';
                    $shiftCode = 'GRAVEYARD';
                    break;
                default:
                    $timeIn = '06:00';
                    $timeOut = '14:00';
                    $shiftCode = 'MORNING';
            }

            $sheet->setCellValue('A' . $row, $employee['employee_number']);
            $sheet->setCellValue('B' . $row, $employee['employee_name']);
            $sheet->setCellValue('C' . $row, ''); // Empty date
            $sheet->setCellValue('D' . $row, $timeIn);
            $sheet->setCellValue('E' . $row, $timeOut);
            $sheet->setCellValue('F' . $row, $shiftId);
            $sheet->setCellValue('G' . $row, $employee['department'] ?? '');
            $sheet->setCellValue('H' . $row, $shiftCode);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add border to data
        $lastRow = $row - 1;
        $sheet->getStyle('A2:H' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Add instructions as a separate sheet
        $instructionSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Instructions');
        $spreadsheet->addSheet($instructionSheet, 1);

        $instructionSheet->setCellValue('A1', 'SCHEDULE TEMPLATE INSTRUCTIONS');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $instructions = [
            ['Step', 'Description'],
            ['1', 'Fill in the DATE column for each employee (format: YYYY-MM-DD)'],
            ['2', 'DO NOT modify employee_id, employee_name, shift_id, or department columns'],
            ['3', 'Time In/Out and Shift Code will auto-populate based on Shift ID'],
            ['4', 'Shift ID Reference:'],
            ['   ', '1 = Morning Shift (6:00 AM - 2:00 PM)'],
            ['   ', '2 = Afternoon Shift (2:00 PM - 10:00 PM)'],
            ['   ', '3 = Graveyard Shift (10:00 PM - 6:00 AM)'],
            ['5', 'Save the file and upload it back to the system'],
            ['', ''],
            ['IMPORTANT:', 'This file format (.xls) is compatible with the upload form'],
            ['', ''],
            ['Generated:', date('Y-m-d H:i:s')],
            ['Total Employees:', count($employees)]
        ];

        $row = 3;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction[0]);
            $instructionSheet->setCellValue('B' . $row, $instruction[1]);
            $row++;
        }

        $instructionSheet->getColumnDimension('A')->setWidth(15);
        $instructionSheet->getColumnDimension('B')->setWidth(60);

        // Set active sheet to the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Clear any output buffers
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers for XLS download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="schedule_template_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: public');

        // Write file to output using Xls writer
        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        $_SESSION['error'][] = "Excel Template download failed: " . $e->getMessage();
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/shift-management'));
        exit;
    }
}

function generateCSVTemplate($db)
{
    try {
        // Get all employees with their shift information
        $employees = $db->query("
            SELECT 
                e.employee_number,
                e.full_name as employee_name,
                e.department,
                COALESCE(e.shift_id, 1) as shift_id
            FROM employees e
            WHERE e.onboarding_status IN ('Onboarding', 'In Progress', 'Onboarded')
            ORDER BY e.department ASC, e.full_name ASC
        ")->find();

        if (empty($employees)) {
            throw new Exception("No employees found to generate template");
        }

        // Handle single record
        if (isset($employees['employee_number'])) {
            $employees = [$employees];
        }

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="schedule_template_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write header row
        fputcsv($output, [
            'employee_id',
            'employee_name',
            'date',
            'time_in',
            'time_out',
            'shift_id',
            'department',
            'shift_code'
        ]);

        // Write data rows
        foreach ($employees as $employee) {
            $shiftId = $employee['shift_id'] ?? 1;

            // Set time in/out based on shift_id
            switch ($shiftId) {
                case 1:
                    $timeIn = '06:00';
                    $timeOut = '14:00';
                    $shiftCode = 'MORNING';
                    break;
                case 2:
                    $timeIn = '14:00';
                    $timeOut = '22:00';
                    $shiftCode = 'AFTERNOON';
                    break;
                case 3:
                    $timeIn = '22:00';
                    $timeOut = '06:00';
                    $shiftCode = 'GRAVEYARD';
                    break;
                default:
                    $timeIn = '06:00';
                    $timeOut = '14:00';
                    $shiftCode = 'MORNING';
            }

            fputcsv($output, [
                $employee['employee_number'],
                $employee['employee_name'],
                '', // Empty date for user to fill
                $timeIn,
                $timeOut,
                $shiftId,
                $employee['department'] ?? '',
                $shiftCode
            ]);
        }

        // Add instructions at the bottom
        fputcsv($output, []); // Empty line
        fputcsv($output, ['INSTRUCTIONS:']);
        fputcsv($output, ['1. Fill in the DATE column for each employee (format: YYYY-MM-DD)']);
        fputcsv($output, ['2. DO NOT modify employee_id, employee_name, shift_id, or department columns']);
        fputcsv($output, ['3. Time In/Out and Shift Code will auto-populate based on Shift ID']);
        fputcsv($output, ['4. Shift ID Reference: 1=Morning(6AM-2PM), 2=Afternoon(2PM-10PM), 3=Graveyard(10PM-6AM)']);
        fputcsv($output, ['5. NOTE: This CSV format is for reference only. Use Excel format (.xls) for uploading.']);
        fputcsv($output, []);
        fputcsv($output, ['Generated:', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Total Employees:', count($employees)]);

        fclose($output);
        exit;

    } catch (Exception $e) {
        $_SESSION['error'][] = "CSV Template download failed: " . $e->getMessage();
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/shift-management'));
        exit;
    }
}