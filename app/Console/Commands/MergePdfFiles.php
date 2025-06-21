<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use setasign\Fpdi\Fpdi;

class MergePdfFiles extends Command
{
    protected $signature = 'pdf:merge {folder=app/pdfs}';
    protected $description = '合併指定資料夾內所有 PDF 檔案';

    public function handle()
    {

        $folderPath = storage_path('app/pdfs');


        if (!is_dir($folderPath)) {
            $this->error("❌ 資料夾不存在：$folderPath");
            return;
        }

        $pdfFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.pdf');
        if (empty($pdfFiles)) {
            $this->warn("⚠️ 資料夾中沒有 PDF 檔案：$folderPath");
            return;
        }

        $this->info("📄 共發現 " . count($pdfFiles) . " 份 PDF，開始合併...");

        $pdf = new Fpdi();

        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
            $this->line("✅ 合併：" . basename($file));
        }

        $outputPath = $folderPath . DIRECTORY_SEPARATOR . 'merged.pdf';
        $pdf->Output($outputPath, 'F');

        $this->info("✅ 合併完成，檔案已儲存至：$outputPath");
    }
}