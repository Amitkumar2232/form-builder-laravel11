<?php

namespace App\Services;

use Illuminate\Support\Str;

class FormSchemaNormalizer
{
    public function normalize(array $schema): array
    {
        $schema['version'] = $schema['version'] ?? 1;
        $schema['title'] = trim($schema['title'] ?? 'Untitled Form');
        $schema['description'] = $schema['description'] ?? '';
        $schema['sections'] = $schema['sections'] ?? [];
        $schema['settings'] = array_merge($this->defaultSettings(), $schema['settings'] ?? []);

        foreach ($schema['sections'] as &$section) {
            $section['id'] = $section['id'] ?? (string) Str::uuid();
            $section['title'] = trim($section['title'] ?? 'Section');
            $section['fields'] = $section['fields'] ?? [];

            foreach ($section['fields'] as &$field) {
                $field = $this->normalizeField($field);
            }
        }

        return $schema;
    }

    public function normalizeField(array $field): array
    {
        $type = FieldTypeRegistry::normalizeType($field['type'] ?? 'text');
        $defaults = FieldTypeRegistry::defaultField($type);

        return array_merge($defaults, $field, [
            'type' => $type,
            'id' => $field['id'] ?? (string) Str::uuid(),
            'key' => Str::snake($field['key'] ?? $field['label'] ?? $defaults['key']),
            'required' => (bool) ($field['required'] ?? false),
            'validation' => array_merge($defaults['validation'], $field['validation'] ?? []),
            'options' => $field['options'] ?? $defaults['options'],
        ]);
    }

    public function emptySchema(string $title = ''): array
    {
        return $this->normalize([
            'version' => 1,
            'title' => $title,
            'description' => '',
            'sections' => [
                [
                    'id' => (string) Str::uuid(),
                    'title' => 'General',
                    'fields' => [],
                ],
            ],
            'settings' => $this->defaultSettings(),
        ]);
    }

    public function toJson(array $schema): string
    {
        return json_encode($this->normalize($schema), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function defaultSettings(): array
    {
        return [
            'submit_button_text' => 'Submit',
            'success_message' => 'Thank you for your submission!',
            'allow_multiple_submissions' => true,
            'enable_conditional_logic' => true,
        ];
    }
}
