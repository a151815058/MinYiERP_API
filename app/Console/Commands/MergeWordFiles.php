<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class MergeWordFiles extends Command
{
    protected $signature = 'word:merge';
    protected $description = '合併多個 Word 文件';

public function handle()
{
    $wordFiles = glob(storage_path('app/docs/*.docx'));
    $outputDir = storage_path('app/pdfs');

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    foreach ($wordFiles as $wordFile) {
        $filename = pathinfo($wordFile, PATHINFO_FILENAME);
        $command = sprintf(
            'soffice --headless --convert-to pdf "%s" --outdir "%s"',
            $wordFile,
            $outputDir
        );

        $this->info("🔄 轉換中：$filename.docx → PDF");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("❌ 轉換失敗：$filename.docx");
        } else {
            $this->info("✅ 完成：$filename.pdf");
        }
    }
}
}