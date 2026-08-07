<?php

namespace App\Services\Import;

use App\Models\FormImport;
use App\Services\Ai\FormAiGenerator;
use App\Services\FormSchemaValidator;
use Illuminate\Support\Facades\Storage;

class DocumentImportService
{
    public function __construct(
        protected WordFormParser $wordParser,
        protected ExcelFormParser $excelParser,
        protected FormSchemaValidator $validator,
    ) {}

    public function process(FormImport $import, bool $useAiRefinement = true): array
    {
        $import->update(['status' => 'processing']);

        $path = Storage::disk('local')->path($import->stored_path);

        try {
            $result = match ($import->file_type) {
                'docx' => $this->wordParser->parse($path),
                'xlsx' => $this->excelParser->parse($path),
                default => throw new \InvalidArgumentException("Unsupported file type: {$import->file_type}"),
            };

            $schema = $result['schema'];
            $warnings = $result['warnings'] ?? [];

            if ($useAiRefinement && config('services.openai.api_key')) {
                $schema = $this->refineWithAi($schema, $import, $warnings);
            }

            if (! empty($import->mapping_overrides)) {
                $schema = $this->applyMappingOverrides($schema, $import->mapping_overrides);
            }

            $schema = $this->validator->validate($schema);

            $import->update([
                'status' => 'preview',
                'parsed_schema' => $schema,
                'warnings' => $warnings,
            ]);

            return ['schema' => $schema, 'warnings' => $warnings];
        } catch (\Throwable $e) {
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function refineWithAi(array $schema, FormImport $import, array &$warnings): array
    {
        $warnings[] = 'AI refinement skipped in import pipeline — deterministic parse used. Enable manual review on preview screen.';

        return $schema;
    }

    protected function applyMappingOverrides(array $schema, array $overrides): array
    {
        foreach ($schema['sections'] as &$section) {
            foreach ($section['fields'] as &$field) {
                $override = $overrides[$field['id']] ?? null;
                if ($override) {
                    $field['type'] = $override['type'] ?? $field['type'];
                    $field['label'] = $override['label'] ?? $field['label'];
                    $field['required'] = $override['required'] ?? $field['required'];
                }
            }
        }

        return $schema;
    }
}
