<?php

namespace App\Services\Import;

use App\Services\FieldTypeRegistry;
use App\Services\FormSchemaNormalizer;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelFormParser
{
    /**
     * Supported layouts:
     * 1. Header row: Row 1 = labels, optional row 2 = types (text|email|number|date|dropdown|...)
     * 2. Field definition sheet: columns [label, type, required, options, placeholder]
     */
    public function __construct(
        protected FormSchemaNormalizer $normalizer,
    ) {}

    public function parse(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $warnings = [];

        if ($this->isFieldDefinitionLayout($rows)) {
            return $this->parseFieldDefinitionLayout($rows, $warnings);
        }

        return $this->parseHeaderRowLayout($rows, $warnings);
    }

    protected function isFieldDefinitionLayout(array $rows): bool
    {
        $header = array_map('strtolower', array_map('trim', array_values($rows[1] ?? [])));

        return in_array('label', $header, true) && in_array('type', $header, true);
    }

    protected function parseFieldDefinitionLayout(array $rows, array &$warnings): array
    {
        $header = array_map('strtolower', array_map('trim', array_values(array_shift($rows))));
        $fields = [];

        foreach ($rows as $rowNum => $row) {
            $values = array_values($row);
            $data = array_combine($header, array_pad($values, count($header), null));

            $label = trim($data['label'] ?? '');
            if ($label === '') {
                continue;
            }

            $type = FieldTypeRegistry::normalizeType($data['type'] ?? 'text');
            $field = FieldTypeRegistry::defaultField($type);
            $field['label'] = $label;
            $field['key'] = Str::snake($data['key'] ?? $label);
            $field['required'] = in_array(strtolower($data['required'] ?? ''), ['1', 'yes', 'true', 'y'], true);
            $field['placeholder'] = $data['placeholder'] ?? '';

            if (! empty($data['options'])) {
                $opts = array_map('trim', explode('|', $data['options']));
                $field['options'] = array_map(fn ($o) => ['label' => $o, 'value' => Str::snake($o)], $opts);
            }

            $fields[] = $field;
        }

        return $this->buildResult($fields, $warnings);
    }

    protected function parseHeaderRowLayout(array $rows, array &$warnings): array
    {
        $headers = array_values($rows[1] ?? []);
        $typeRow = array_values($rows[2] ?? []);
        $hasTypeRow = $this->looksLikeTypeRow($typeRow);
        $fields = [];

        foreach ($headers as $i => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }

            $type = $hasTypeRow
                ? FieldTypeRegistry::normalizeType(trim((string) ($typeRow[$i] ?? 'text')))
                : $this->inferTypeFromLabel($label);

            $field = FieldTypeRegistry::defaultField($type);
            $field['label'] = $label;
            $field['key'] = Str::snake($label);
            $fields[] = $field;
        }

        if (empty($fields)) {
            $warnings[] = 'No columns found in header row.';
        }

        return $this->buildResult($fields, $warnings);
    }

    protected function looksLikeTypeRow(array $row): bool
    {
        $known = FieldTypeRegistry::ALL_TYPES;
        $matches = 0;
        foreach ($row as $cell) {
            if (in_array(strtolower(trim((string) $cell)), $known, true)) {
                $matches++;
            }
        }

        return $matches >= 2;
    }

    protected function inferTypeFromLabel(string $label): string
    {
        $l = strtolower($label);

        return match (true) {
            str_contains($l, 'email') => 'email',
            str_contains($l, 'phone') || str_contains($l, 'mobile') => 'phone',
            str_contains($l, 'date') || str_contains($l, 'dob') => 'date',
            str_contains($l, 'age') || str_contains($l, 'number') || str_contains($l, 'count') => 'number',
            str_contains($l, 'file') || str_contains($l, 'upload') || str_contains($l, 'resume') => 'file',
            str_contains($l, 'comment') || str_contains($l, 'description') || str_contains($l, 'notes') => 'textarea',
            default => 'text',
        };
    }

    protected function buildResult(array $fields, array $warnings): array
    {
        return [
            'schema' => $this->normalizer->normalize([
                'title' => 'Imported from Excel',
                'sections' => [[
                    'id' => (string) Str::uuid(),
                    'title' => 'Imported Fields',
                    'fields' => $fields,
                ]],
            ]),
            'warnings' => $warnings,
        ];
    }
}
