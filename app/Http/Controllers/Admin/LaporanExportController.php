<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Collection;

class LaporanExportController  extends Controller
{
    const BONUS_HADIR  = 20000;
    const KOMISI_PIJAT = 0.25;

    public function __construct(
        private string     $label,
        private Collection $rekapHarian,
        private Collection $laporanTerapis,
        private Collection $transaksi,
        private Collection $absensi,
    ) {}

    public function generate(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Laporan Spa – ' . $this->label)
            ->setCreator('Sistem Spa');

        $this->sheetRekapHarian($spreadsheet->getActiveSheet());

        $sh2 = $spreadsheet->createSheet();
        $this->sheetPerTerapis($sh2);

        $sh3 = $spreadsheet->createSheet();
        $this->sheetDetailTransaksi($sh3);

        $sh4 = $spreadsheet->createSheet();
        $this->sheetAbsensi($sh4);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    private function sheetRekapHarian(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        $ws->setTitle('Rekap Harian');

        $ws->mergeCells('A1:I1');
        $ws->setCellValue('A1', 'LAPORAN HARIAN – ' . strtoupper($this->label));
        $this->styleTitle($ws, 'A1:I1');

        $ws->mergeCells('A2:I2');
        $ws->setCellValue('A2', 'Komisi pijat: 25% dari pendapatan · Bonus hadir: Rp 20.000/terapis/hari');
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $headers = [
            'A' => 'Tanggal',
            'B' => 'Hari',
            'C' => 'Sesi Selesai',
            'D' => 'Terapis Hadir',
            'E' => 'Pendapatan Bruto (Rp)',
            'F' => 'Komisi Pijat 25% (Rp)',
            'G' => 'Bonus Hadir (Rp)',
            'H' => 'Total Komisi (Rp)',
            'I' => 'Pendapatan Bersih Spa (Rp)',
        ];

        $row = 4;
        foreach ($headers as $col => $lbl) {
            $ws->setCellValue("{$col}{$row}", $lbl);
        }
        $this->styleHeader($ws, "A{$row}:I{$row}");

        $dataStart = $row + 1;
        foreach ($this->rekapHarian as $d) {
            $row++;
            /** @var Carbon $tgl */
            $tgl = $d['tanggal'];
            $ws->setCellValue("A{$row}", $tgl->format('d/m/Y'));
            $ws->setCellValue("B{$row}", $tgl->translatedFormat('l'));
            $ws->setCellValue("C{$row}", $d['sesi']);
            $ws->setCellValue("D{$row}", $d['hadir']);
            $ws->setCellValue("E{$row}", $d['bruto']);
            $ws->setCellValue("F{$row}", $d['komisi_pijat']);
            $ws->setCellValue("G{$row}", $d['bonus_hadir']);
            $ws->setCellValue("H{$row}", $d['total_komisi']);
            $ws->setCellValue("I{$row}", $d['bersih']);

            if ($row % 2 === 0) {
                $ws->getStyle("A{$row}:I{$row}")
                    ->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('F8FAFC');
            }

            if ($tgl->dayOfWeek === 0) {
                $ws->getStyle("A{$row}:I{$row}")->getFont()->setColor(
                    (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('9CA3AF')
                );
            }

            $this->currencyFormat($ws, "E{$row}:I{$row}");
        }

        $dataEnd = $row;

        $row++;
        $ws->setCellValue("A{$row}", 'TOTAL');
        foreach (['C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
            $ws->setCellValue("{$col}{$row}", "=SUM({$col}{$dataStart}:{$col}{$dataEnd})");
        }
        $this->styleTotals($ws, "A{$row}:I{$row}");
        $this->currencyFormat($ws, "E{$row}:I{$row}");

        $row += 2;
        $ws->mergeCells("A{$row}:B{$row}");
        $ws->setCellValue("A{$row}", 'Margin Bersih (%)');
        $totalRow = $dataEnd + 1;
        $ws->setCellValue("C{$row}", "=IFERROR(I{$totalRow}/E{$totalRow},0)");
        $ws->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.0%');

        foreach (['A' => 14, 'B' => 14, 'C' => 14, 'D' => 16, 'E' => 24, 'F' => 22, 'G' => 20, 'H' => 22, 'I' => 26] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }
    }

    private function sheetPerTerapis(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        $ws->setTitle('Per Terapis');

        $ws->mergeCells('A1:H1');
        $ws->setCellValue('A1', 'LAPORAN KOMISI TERAPIS – ' . strtoupper($this->label));
        $this->styleTitle($ws, 'A1:H1');

        $ws->mergeCells('A2:H2');
        $ws->setCellValue('A2', 'Komisi pijat = 25% dari pendapatan booking selesai · Bonus hadir = Rp 20.000 × hari hadir (present + late)');
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $headers = [
            'A' => 'Nama Terapis',
            'B' => 'Sesi Selesai',
            'C' => 'Hari Hadir',
            'D' => 'Pendapatan Bruto (Rp)',
            'E' => 'Komisi Pijat 25% (Rp)',
            'F' => 'Bonus Hadir (Rp)',
            'G' => 'Total Komisi (Rp)',
            'H' => 'Completion Rate',
        ];

        $row = 4;
        foreach ($headers as $col => $lbl) {
            $ws->setCellValue("{$col}{$row}", $lbl);
        }
        $this->styleHeader($ws, "A{$row}:H{$row}");

        $dataStart = $row + 1;
        foreach ($this->laporanTerapis as $t) {
            $row++;
            $rate = $t->total_sesi > 0 ? round(($t->sesi_selesai / $t->total_sesi) * 100) / 100 : 0;

            $ws->setCellValue("A{$row}", $t->name);
            $ws->setCellValue("B{$row}", $t->sesi_selesai);
            $ws->setCellValue("C{$row}", $t->hari_hadir);
            $ws->setCellValue("D{$row}", $t->total_bruto_fmt ?? 0);
            $ws->setCellValue("E{$row}", $t->komisi_pijat ?? 0);
            $ws->setCellValue("F{$row}", $t->bonus_hadir ?? 0);
            $ws->setCellValue("G{$row}", $t->total_komisi ?? 0);
            $ws->setCellValue("H{$row}", $rate);

            $this->currencyFormat($ws, "D{$row}:G{$row}");
            $ws->getStyle("H{$row}")->getNumberFormat()->setFormatCode('0%');

            if ($row % 2 === 0) {
                $ws->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        $dataEnd = $row;
        $row++;
        $ws->setCellValue("A{$row}", 'TOTAL');
        foreach (['B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $ws->setCellValue("{$col}{$row}", "=SUM({$col}{$dataStart}:{$col}{$dataEnd})");
        }
        $this->styleTotals($ws, "A{$row}:H{$row}");
        $this->currencyFormat($ws, "D{$row}:G{$row}");

        foreach (['A' => 22, 'B' => 14, 'C' => 14, 'D' => 24, 'E' => 22, 'F' => 20, 'G' => 22, 'H' => 16] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }
    }

    private function sheetDetailTransaksi(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        $ws->setTitle('Detail Transaksi');

        $ws->mergeCells('A1:K1');
        $ws->setCellValue('A1', 'DETAIL TRANSAKSI – ' . strtoupper($this->label));
        $this->styleTitle($ws, 'A1:K1');

        $headers = [
            'A' => 'No',
            'B' => 'Tanggal & Jam',
            'C' => 'Pelanggan',
            'D' => 'Layanan',
            'E' => 'Terapis',
            'F' => 'Status',
            'G' => 'Harga Asli (Rp)',
            'H' => 'Diskon (Rp)',
            'I' => 'Bayar (Rp)',
            'J' => 'Komisi 25% (Rp)',
            'K' => 'Metode Bayar',
        ];

        $row = 3;
        foreach ($headers as $col => $lbl) {
            $ws->setCellValue("{$col}{$row}", $lbl);
        }
        $this->styleHeader($ws, "A{$row}:K{$row}");

        $no        = 0;
        $dataStart = $row + 1;
        foreach ($this->transaksi as $b) {
            $no++;
            $row++;
            $tgl    = Carbon::parse($b->scheduled_at)->setTimezone('Asia/Jakarta');
            $komisi = $b->status === 'completed' ? $b->final_price * self::KOMISI_PIJAT : 0;

            $ws->setCellValue("A{$row}", $no);
            $ws->setCellValue("B{$row}", $tgl->format('d/m/Y H:i'));
            $ws->setCellValue("C{$row}", $b->customer->name   ?? '—');
            $ws->setCellValue("D{$row}", $b->service->name    ?? '—');
            $ws->setCellValue("E{$row}", $b->therapist->name  ?? '—');
            $ws->setCellValue("F{$row}", match ($b->status) {
                'completed' => 'Selesai',
                'cancelled' => 'Batal',
                'ongoing'   => 'Berlangsung',
                'scheduled' => 'Terjadwal',
                default     => $b->status,
            });
            $ws->setCellValue("G{$row}", $b->price);
            $ws->setCellValue("H{$row}", $b->discount ?? 0);
            $ws->setCellValue("I{$row}", $b->final_price);
            $ws->setCellValue("J{$row}", round($komisi));
            $ws->setCellValue("K{$row}", strtoupper($b->payment->method ?? '—'));

            $this->currencyFormat($ws, "G{$row}:J{$row}");

            $statusColor = match ($b->status) {
                'completed' => 'D1FAE5',
                'cancelled' => 'FEE2E2',
                'ongoing'   => 'DBEAFE',
                default     => 'FEF3C7',
            };
            $ws->getStyle("F{$row}")->getFill()
                ->setFillType('solid')->getStartColor()->setRGB($statusColor);

            if ($row % 2 === 0) {
                $ws->getStyle("A{$row}:K{$row}")
                    ->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        $dataEnd = $row;
        $row++;
        $ws->setCellValue("A{$row}", 'TOTAL');
        foreach (['G', 'H', 'I', 'J'] as $col) {
            $ws->setCellValue("{$col}{$row}", "=SUM({$col}{$dataStart}:{$col}{$dataEnd})");
        }
        $this->styleTotals($ws, "A{$row}:K{$row}");
        $this->currencyFormat($ws, "G{$row}:J{$row}");

        foreach (['A' => 5, 'B' => 18, 'C' => 22, 'D' => 24, 'E' => 20, 'F' => 13, 'G' => 18, 'H' => 15, 'I' => 18, 'J' => 18, 'K' => 14] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $ws->setAutoFilter("A3:K3");
    }

    private function sheetAbsensi(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        $ws->setTitle('Absensi Terapis');

        $ws->mergeCells('A1:H1');
        $ws->setCellValue('A1', 'ABSENSI TERAPIS – ' . strtoupper($this->label));
        $this->styleTitle($ws, 'A1:H1');

        $headers = [
            'A' => 'Tanggal',
            'B' => 'Hari',
            'C' => 'Nama Terapis',
            'D' => 'Check-In',
            'E' => 'Check-Out',
            'F' => 'Durasi',
            'G' => 'Status',
            'H' => 'Bonus Hadir (Rp)',
        ];

        $row = 3;
        foreach ($headers as $col => $lbl) {
            $ws->setCellValue("{$col}{$row}", $lbl);
        }
        $this->styleHeader($ws, "A{$row}:H{$row}");

        $dataStart = $row + 1;
        foreach ($this->absensi as $a) {
            $row++;
            $tgl      = Carbon::parse($a->attendance_date);
            $checkIn  = $a->check_in_at  ? Carbon::parse($a->check_in_at)->setTimezone('Asia/Jakarta')  : null;
            $checkOut = $a->check_out_at ? Carbon::parse($a->check_out_at)->setTimezone('Asia/Jakarta') : null;

            $durasi = '';
            if ($checkIn && $checkOut) {
                $h      = $checkIn->diffInHours($checkOut);
                $m      = $checkIn->diff($checkOut)->i;
                $durasi = "{$h}j {$m}m";
            }

            $bonus = in_array($a->status, ['present', 'late']) ? self::BONUS_HADIR : 0;

            $ws->setCellValue("A{$row}", $tgl->format('d/m/Y'));
            $ws->setCellValue("B{$row}", $tgl->translatedFormat('l'));
            $ws->setCellValue("C{$row}", $a->therapist->name ?? '—');
            $ws->setCellValue("D{$row}", $checkIn  ? $checkIn->format('H:i')  : '—');
            $ws->setCellValue("E{$row}", $checkOut ? $checkOut->format('H:i') : '—');
            $ws->setCellValue("F{$row}", $durasi);
            $ws->setCellValue("G{$row}", match ($a->status) {
                'present' => 'Hadir',
                'late'    => 'Terlambat',
                'absent'  => 'Absen',
                default   => $a->status,
            });
            $ws->setCellValue("H{$row}", $bonus);
            $this->currencyFormat($ws, "H{$row}");

            $statusColor = match ($a->status) {
                'present' => 'D1FAE5',
                'late'    => 'FEF3C7',
                'absent'  => 'FEE2E2',
                default   => 'FFFFFF',
            };
            $ws->getStyle("G{$row}")->getFill()
                ->setFillType('solid')->getStartColor()->setRGB($statusColor);

            if ($row % 2 === 0) {
                $ws->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        $dataEnd = $row;
        $row++;
        $ws->setCellValue("A{$row}", 'TOTAL BONUS HADIR');
        $ws->setCellValue("H{$row}", "=SUM(H{$dataStart}:H{$dataEnd})");
        $this->styleTotals($ws, "A{$row}:H{$row}");
        $this->currencyFormat($ws, "H{$row}");

        foreach (['A' => 13, 'B' => 14, 'C' => 22, 'D' => 11, 'E' => 11, 'F' => 11, 'G' => 13, 'H' => 20] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $ws->setAutoFilter("A3:H3");
    }

    // ── Styling helpers ───────────────────────────────────────────────────

    private function styleTitle(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(1)->setRowHeight(30);
    }

    private function styleHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '334155']]],
        ]);
    }

    private function styleTotals(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->applyFromArray([
            'font'    => ['bold' => true, 'name' => 'Arial', 'color' => ['rgb' => '1E293B']],
            'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E7FF']],
            'borders' => ['top' => ['borderStyle' => 'medium', 'color' => ['rgb' => '4F46E5']]],
        ]);
    }

    private function currencyFormat(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->getNumberFormat()->setFormatCode('#,##0;(#,##0);"-"');
    }
}
