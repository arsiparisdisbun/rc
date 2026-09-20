<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait EksporExcel
{
    /**
     * Membuat dan mengunduh berkas Excel.
     * $kolomLebar (opsional): ['I' => 45] artinya kolom I dibuat lebar tetap 45
     * dengan teks melipat — dipakai untuk kolom uraian yang panjang, supaya
     * tidak melebar tak terkendali seperti autosize biasa.
     */
    protected function unduhExcel(string $namaSheet, array $kolom, iterable $baris, string $namaBerkas, array $kolomLebar = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($namaSheet);

        $jumlahKolom = count($kolom);
        $sheet->fromArray($kolom, null, 'A1');

        $hurufTerakhir = Coordinate::stringFromColumnIndex($jumlahKolom);
        $sheet->getStyle("A1:{$hurufTerakhir}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '14503B']],
        ]);

        $barisKe = 2;
        foreach ($baris as $satuBaris) {
            $sheet->fromArray($satuBaris, null, "A{$barisKe}");
            $barisKe++;
        }

        foreach (range(1, $jumlahKolom) as $i) {
            $huruf = Coordinate::stringFromColumnIndex($i);

            if (isset($kolomLebar[$huruf])) {
                $sheet->getColumnDimension($huruf)->setWidth($kolomLebar[$huruf]);
                $sheet->getStyle("{$huruf}2:{$huruf}{$barisKe}")->getAlignment()->setWrapText(true);
            } else {
                $sheet->getColumnDimension($huruf)->setAutoSize(true);
            }
        }

        $sheet->freezePane('A2'); // Header tetap terlihat saat digulir ke bawah

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaBerkas, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}