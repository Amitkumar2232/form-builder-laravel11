<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormSchemaValidator
{
    public function __construct(
        protected FormSchemaNormalizer $normalizer,
    ) {}

    public function validate(array $schema): array
    {
        $schema = $this->normalizer->normalize($schema);
        $errors = [];

        if (empty($schema['title'])) {
            $errors['title'] = 'Form title is required.';
        }

        if (empty($schema['sections']) || ! is_array($schema['sections'])) {
            $errors['sections'] = 'At least one section is required.';
        }

        $keys = [];
        foreach ($schema['sections'] ?? [] as $si => $section) {
            if (empty($section['title'])) {
                $errors["sections.{$si}.title"] = 'Section title is required.';
            }

            foreach ($section['fields'] ?? [] as $fi => $field) {
                $type = $field['type'] ?? '';
                if (! in_array($type, FieldTypeRegistry::ALL_TYPES, true)) {
                    $errors["sections.{$si}.fields.{$fi}.type"] = "Invalid field type: {$type}";
                }

                if (FieldTypeRegistry::isInputType($type)) {
                    if (empty($field['key'])) {
                        $errors["sections.{$si}.fields.{$fi}.key"] = 'Field key is required.';
                    } elseif (! preg_match('/^[a-z][a-z0-9_]*$/', $field['key'])) {
                        $errors["sections.{$si}.fields.{$fi}.key"] = 'Field key must be snake_case.';
                    } elseif (isset($keys[$field['key']])) {
                        $errors["sections.{$si}.fields.{$fi}.key"] = "Duplicate key: {$field['key']}";
                    } else {
                        $keys[$field['key']] = true;
                    }

                    if (empty($field['label']) && $type !== 'hidden') {
                        $errors["sections.{$si}.fields.{$fi}.label"] = 'Field label is required.';
                    }
                }

                if (FieldTypeRegistry::needsOptions($type) && empty($field['options'])) {
                    $errors["sections.{$si}.fields.{$fi}.options"] = 'Options are required for this field type.';
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $schema;
    }

    public function tryParseJson(string $json): array
    {
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                'schema_json' => 'Invalid JSON: '.json_last_error_msg(),
            ]);
        }

        return $this->validate($decoded);
    }

    public function repairPartial(array $partial): array
    {
        $partial['version'] = $partial['version'] ?? 1;
        $partial['title'] = $partial['title'] ?? 'Untitled Form';
        $partial['sections'] = $partial['sections'] ?? [];

        foreach ($partial['sections'] as &$section) {
            $section['id'] = $section['id'] ?? (string) Str::uuid();
            $section['title'] = $section['title'] ?? 'Section';
            $section['fields'] = $section['fields'] ?? [];

            foreach ($section['fields'] as &$field) {
                $field['type'] = FieldTypeRegistry::normalizeType($field['type'] ?? 'text');
                $field['id'] = $field['id'] ?? (string) Str::uuid();
                $field['key'] = $field['key'] ?? Str::snake($field['label'] ?? 'field');
                $field['label'] = $field['label'] ?? 'Field';
                $field['placeholder'] = $field['placeholder'] ?? '';
                $field['help_text'] = $field['help_text'] ?? '';
                $field['required'] = (bool) ($field['required'] ?? false);
                $field['options'] = $field['options'] ?? [];
                $field['validation'] = array_merge(
                    FieldTypeRegistry::defaultField($field['type'])['validation'],
                    $field['validation'] ?? []
                );
            }
        }

        return $this->validate($partial);
    }
}
