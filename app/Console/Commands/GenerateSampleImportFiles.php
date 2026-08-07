<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class GenerateSampleImportFiles extends Command
{
    protected $signature = 'forms:generate-samples';

    protected $description = 'Generate sample Word and Excel files for import testing';

    public function handle(): int
    {
        $dir = base_path('samples');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->generateWordSample("{$dir}/sample-survey.docx");
        $this->generateExcelHeaderSample("{$dir}/sample-header-row.xlsx");
        $this->generateExcelDefinitionSample("{$dir}/sample-field-definition.xlsx");

        $this->info('Sample files created in samples/');

        return self::SUCCESS;
    }

    protected function generateWordSample(string $path): void
    {
        $word = new PhpWord;
        $section = $word->addSection();

        $section->addTitle('Personal Information', 1);
        $section->addText('1. What is your full name? *');
        $section->addText('2. What is your email address?');
        $section->addText('3. Phone number');

        $section->addTitle('Preferences', 1);
        $section->addText('4. How did you hear about us? [Website, Friend, Social Media, Other]');
        $section->addText('5. Please describe your experience in detail.');
        $section->addText('6. Upload your resume');

        IOFactory::createWriter($word, 'Word2007')->save($path);
    }

    protected function generateExcelHeaderSample(string $path): void
    {
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([
            ['Full Name', 'Email Address', 'Phone', 'Date of Birth', 'Resume Upload'],
            ['text', 'email', 'phone', 'date', 'file'],
        ]);

        (new Xlsx($sheet))->save($path);
    }

    protected function generateExcelDefinitionSample(string $path): void
    {
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([
            ['label', 'type', 'required', 'options', 'placeholder'],
            ['Full Name', 'text', 'yes', '', 'Enter your name'],
            ['Email', 'email', 'yes', '', 'you@example.com'],
            ['Department', 'dropdown', 'yes', 'Engineering|Sales|Marketing', ''],
            ['Comments', 'textarea', 'no', '', 'Optional feedback'],
        ]);

        (new Xlsx($sheet))->save($path);
    }
}
