<?php

namespace App\Jobs;

use App\Models\FormImport;
use App\Services\Import\DocumentImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportDocumentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FormImport $import,
    ) {}

    public function handle(DocumentImportService $service): void
    {
        $service->process($this->import);
    }
}
