<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classes;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function downloadTemplateSiswa()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TEMPLAT IMPORT DATA SISWA PKL');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Petunjuk: Isi data sesuai format. Kolom dengan tanda * wajib diisi. Email dan Password akan digenerate otomatis jika dikosongkan.');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $headers = ['NISN*', 'Nama Lengkap*', 'Kelas', 'Jurusan', 'Email (kosongkan untuk auto-generate)', 'Password (kosongkan untuk default)', 'No. Telepon', 'Status (Aktif/Nonaktif)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');
            $sheet->getStyle($col . '4')->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($col . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $sheet->setCellValue('A5', '0012345678');
        $sheet->setCellValue('B5', 'Ahmad Fauzi');
        $sheet->setCellValue('C5', 'XII RPL 1');
        $sheet->setCellValue('D5', 'Rekayasa Perangkat Lunak');
        $sheet->setCellValue('E5', '');
        $sheet->setCellValue('F5', '');
        $sheet->setCellValue('G5', '628123456789');
        $sheet->setCellValue('H5', 'Aktif');

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ];
        $sheet->getStyle('A4:H5')->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Siswa_PKL.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function downloadTemplateGuru()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TEMPLAT IMPORT DATA GURU PEMBIMBING PKL');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Petunjuk: Isi data sesuai format. Kolom dengan tanda * wajib diisi. Email dan Password akan digenerate otomatis jika dikosongkan.');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $headers = ['NIP*', 'Nama Lengkap*', 'Email (kosongkan untuk auto-generate)', 'Password (kosongkan untuk default)', 'No. Telepon', 'Mata Pelajaran', 'Status (Aktif/Nonaktif)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');
            $sheet->getStyle($col . '4')->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($col . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $sheet->setCellValue('A5', '197501012005011001');
        $sheet->setCellValue('B5', 'Budi Santoso');
        $sheet->setCellValue('C5', '');
        $sheet->setCellValue('D5', '');
        $sheet->setCellValue('E5', '628123456789');
        $sheet->setCellValue('F5', 'Pemrograman Web');
        $sheet->setCellValue('G5', 'Aktif');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ];
        $sheet->getStyle('A4:G5')->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Guru_PKL.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak memiliki data. Minimal 1 baris data (baris ke-5 dan seterusnya).'
                ], 400);
            }

            $imported = 0;
            $errors = [];
            $defaultPassword = 'siswa123';

            for ($i = 4; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNum = $i + 1;

                $nisn = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $kelas = trim($row[2] ?? '');
                $jurusan = trim($row[3] ?? '');
                $email = trim($row[4] ?? '');
                $password = trim($row[5] ?? '');
                $phone = trim($row[6] ?? '');
                $status = trim($row[7] ?? 'Aktif');

                if (empty($nisn) && empty($nama)) {
                    continue;
                }

                if (empty($nisn)) {
                    $errors[] = "Baris {$rowNum}: NISN wajib diisi";
                    continue;
                }
                if (empty($nama)) {
                    $errors[] = "Baris {$rowNum}: Nama lengkap wajib diisi";
                    continue;
                }

                if (User::where('nisn', $nisn)->exists()) {
                    $errors[] = "Baris {$rowNum}: NISN '{$nisn}' sudah terdaftar";
                    continue;
                }

                if (empty($email)) {
                    $email = strtolower(str_replace(' ', '', $nama)) . '.' . $nisn . '@student.sch.id';
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$rowNum}: Email '{$email}' sudah terdaftar";
                    continue;
                }

                $finalPassword = !empty($password) ? $password : $defaultPassword;

                $kelasModel = null;
                if (!empty($kelas)) {
                    $kelasModel = Classes::where('name', $kelas)->first();
                }

                $user = User::create([
                    'nisn' => $nisn,
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($finalPassword),
                    'kelas' => $kelas,
                    'jurusan' => $jurusan,
                    'phone' => normalizePhone($phone),
                    'class_id' => $kelasModel ? $kelasModel->id : null,
                    'role_id' => 2,
                    'is_active' => strtolower($status) === 'aktif' || empty($status),
                    'registration_status' => 'approved',
                    'approved_at' => now(),
                ]);

                $imported++;
            }

            $message = "Berhasil mengimport {$imported} data siswa.";
            if (count($errors) > 0) {
                $message .= ' ' . count($errors) . ' baris dilewati karena error.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'imported' => $imported,
                    'errors' => $errors,
                    'total_processed' => $imported + count($errors),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Import siswa error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importGuru(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak memiliki data. Minimal 1 baris data (baris ke-5 dan seterusnya).'
                ], 400);
            }

            $imported = 0;
            $errors = [];
            $defaultPassword = 'guru123';

            for ($i = 4; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNum = $i + 1;

                $nip = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $email = trim($row[2] ?? '');
                $password = trim($row[3] ?? '');
                $phone = trim($row[4] ?? '');
                $mataPelajaran = trim($row[5] ?? '');
                $status = trim($row[6] ?? 'Aktif');

                if (empty($nip) && empty($nama)) {
                    continue;
                }

                if (empty($nip)) {
                    $errors[] = "Baris {$rowNum}: NIP wajib diisi";
                    continue;
                }
                if (empty($nama)) {
                    $errors[] = "Baris {$rowNum}: Nama lengkap wajib diisi";
                    continue;
                }

                if (User::where('nip', $nip)->exists()) {
                    $errors[] = "Baris {$rowNum}: NIP '{$nip}' sudah terdaftar";
                    continue;
                }

                if (empty($email)) {
                    $email = strtolower(str_replace(' ', '', $nama)) . '.' . $nip . '@guru.sch.id';
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$rowNum}: Email '{$email}' sudah terdaftar";
                    continue;
                }

                $finalPassword = !empty($password) ? $password : $defaultPassword;

                User::create([
                    'nip' => $nip,
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($finalPassword),
                    'phone' => normalizePhone($phone),
                    'mata_pelajaran' => $mataPelajaran,
                    'role_id' => 3,
                    'is_active' => strtolower($status) === 'aktif' || empty($status),
                    'registration_status' => 'approved',
                    'approved_at' => now(),
                ]);

                $imported++;
            }

            $message = "Berhasil mengimport {$imported} data guru.";
            if (count($errors) > 0) {
                $message .= ' ' . count($errors) . ' baris dilewati karena error.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'imported' => $imported,
                    'errors' => $errors,
                    'total_processed' => $imported + count($errors),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Import guru error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }
}
