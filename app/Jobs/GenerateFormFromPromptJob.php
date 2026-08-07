<?php

namespace App\Jobs;

use App\Models\AiGenerationLog;
use App\Services\Ai\FormAiGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFormFromPromptJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public AiGenerationLog $log,
    ) {}

    public function handle(FormAiGenerator $generator): void
    {
        $generator->generate($this->log);
    }
}
