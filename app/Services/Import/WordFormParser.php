<?php

namespace App\Services\Import;

use App\Services\FieldTypeRegistry;
use App\Services\FormSchemaNormalizer;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIO;
use PhpOffice\PhpWord\IOFactory as WordIO;

class WordFormParser
{
    public function __construct(
        protected FormSchemaNormalizer $normalizer,
    ) {}

    public function parse(string $path): array
    {
        $phpWord = WordIO::load($path);
        $sections = [];
        $currentSection = null;
        $warnings = [];

        foreach ($phpWord->getSections() as $wordSection) {
            foreach ($wordSection->getElements() as $element) {
                $text = $this->extractText($element);

                if ($text === '') {
                    continue;
                }

                if ($this->isHeading($element)) {
                    if ($currentSection) {
                        $sections[] = $currentSection;
                    }
                    $currentSection = [
                        'id' => (string) Str::uuid(),
                        'title' => $text,
                        'fields' => [],
                    ];
                } elseif ($currentSection) {
                    $field = $this->parseQuestion($text, $warnings);
                    if ($field) {
                        $currentSection['fields'][] = $field;
                    }
                } else {
                    $currentSection = [
                        'id' => (string) Str::uuid(),
                        'title' => 'General',
                        'fields' => [],
                    ];
                    $field = $this->parseQuestion($text, $warnings);
                    if ($field) {
                        $currentSection['fields'][] = $field;
                    }
                }
            }
        }

        if ($currentSection) {
            $sections[] = $currentSection;
        }

        if (empty($sections)) {
            $warnings[] = 'No parseable content found in Word document.';
            $sections = [[
                'id' => (string) Str::uuid(),
                'title' => 'Imported',
                'fields' => [],
            ]];
        }

        return [
            'schema' => $this->normalizer->normalize([
                'title' => 'Imported from Word',
                'sections' => $sections,
            ]),
            'warnings' => $warnings,
        ];
    }

    protected function extractText($element): string
    {
        if (method_exists($element, 'getText')) {
            return trim($element->getText());
        }

        if (method_exists($element, 'getElements')) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $parts[] = $this->extractText($child);
            }

            return trim(implode(' ', array_filter($parts)));
        }

        return '';
    }

    protected function isHeading($element): bool
    {
        if (! method_exists($element, 'getStyle')) {
            return false;
        }

        $style = $element->getStyle();

        return $style && method_exists($style, 'getName') && Str::startsWith(strtolower($style->getName() ?? ''), 'heading');
    }

    protected function parseQuestion(string $text, array &$warnings): ?array
    {
        $text = preg_replace('/^\d+[\.\)]\s*/', '', $text);
        $text = trim($text);

        if (strlen($text) < 2) {
            return null;
        }

        $field = FieldTypeRegistry::defaultField('text');
        $field['label'] = rtrim($text, '?');
        $field['key'] = Str::snake(Str::limit($field['label'], 40, ''));

        if (preg_match('/\[([^\]]+)\]/', $text, $matches)) {
            $options = array_map('trim', preg_split('/[,|;]/', $matches[1]));
            $field['type'] = count($options) <= 3 ? 'radio' : 'dropdown';
            $field['options'] = array_map(fn ($o) => [
                'label' => $o,
                'value' => Str::snake($o),
            ], $options);
        } elseif (preg_match('/\(\s*check all|select all|\[\s*\]/i', $text)) {
            $field['type'] = 'checkbox';
        } elseif (preg_match('/email/i', $text)) {
            $field['type'] = 'email';
        } elseif (preg_match('/phone|mobile|tel/i', $text)) {
            $field['type'] = 'phone';
        } elseif (preg_match('/date|dob|birth/i', $text)) {
            $field['type'] = 'date';
        } elseif (preg_match('/upload|attach|resume|file/i', $text)) {
            $field['type'] = 'file';
            $field['validation']['file_types'] = ['pdf', 'doc', 'docx'];
            $field['validation']['max_file_size_kb'] = 5120;
        } elseif (preg_match('/describe|explain|comment|details/i', $text)) {
            $field['type'] = 'textarea';
        } elseif (preg_match('/\?\s*$/', $text)) {
            $field['required'] = true;
        }

        if (Str::contains($text, '*')) {
            $field['required'] = true;
            $field['label'] = str_replace('*', '', $field['label']);
        }

        return $field;
    }
}
