<?php

namespace App\Services;

class FormValidationBuilder
{
    public function rulesFromSchema(array $schema): array
    {
        $rules = [];
        $attributes = [];

        foreach ($schema['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (! FieldTypeRegistry::isInputType($field['type'])) {
                    continue;
                }

                $key = $field['key'];
                $fieldRules = $this->buildFieldRules($field);
                $rules[$key] = $fieldRules;
                $attributes[$key] = $field['label'] ?? $key;
            }
        }

        return ['rules' => $rules, 'attributes' => $attributes];
    }

    protected function buildFieldRules(array $field): array
    {
        $type = $field['type'];
        $rules = [];
        $validation = $field['validation'] ?? [];

        if ($type === 'file') {
            $rules[] = $field['required'] ? 'required' : 'nullable';
            $rules[] = 'file';

            if (! empty($validation['max_file_size_kb'])) {
                $rules[] = 'max:'.((int) $validation['max_file_size_kb']);
            }

            if (! empty($validation['file_types'])) {
                $mimes = implode(',', array_map(fn ($t) => ltrim($t, '.'), $validation['file_types']));
                $rules[] = 'mimes:'.$mimes;
            }

            return $rules;
        }

        if ($type === 'checkbox') {
            $rules[] = $field['required'] ? 'required' : 'nullable';
            $rules[] = 'array';
            if ($field['required']) {
                $rules[] = 'min:1';
            }

            return $rules;
        }

        $rules[] = $field['required'] ? 'required' : 'nullable';

        match ($type) {
            'email' => $rules[] = 'email',
            'number', 'rating' => $rules[] = 'numeric',
            'date' => $rules[] = 'date',
            'phone' => $rules[] = 'regex:/^[+]?[\d\s\-().]{7,20}$/',
            default => null,
        };

        if (! empty($validation['min_length'])) {
            $rules[] = 'min:'.(int) $validation['min_length'];
        }
        if (! empty($validation['max_length'])) {
            $rules[] = 'max:'.(int) $validation['max_length'];
        }
        if (isset($validation['min']) && $validation['min'] !== null) {
            $rules[] = 'min:'.(float) $validation['min'];
        }
        if (isset($validation['max']) && $validation['max'] !== null) {
            $rules[] = 'max:'.(float) $validation['max'];
        }
        if (! empty($validation['regex'])) {
            $rules[] = 'regex:'.$validation['regex'];
        }

        if (in_array($type, ['dropdown', 'radio'], true) && ! empty($field['options'])) {
            $values = array_column($field['options'], 'value');
            $rules[] = 'in:'.implode(',', $values);
        }

        if ($type === 'rating') {
            $rules[] = 'integer';
            $rules[] = 'between:1,5';
        }

        return $rules;
    }

    public function evaluateConditions(array $schema, array $data): array
    {
        $visible = [];

        foreach ($schema['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (! FieldTypeRegistry::isInputType($field['type'])) {
                    continue;
                }

                $visible[$field['key']] = $this->isFieldVisible($field, $data);
            }
        }

        return $visible;
    }

    public function isFieldVisible(array $field, array $data): bool
    {
        $conditions = $field['conditions'] ?? null;

        if (empty($conditions) || empty($conditions['show_when'])) {
            return true;
        }

        $cond = $conditions['show_when'];
        $refValue = $data[$cond['field'] ?? ''] ?? null;
        $operator = $cond['operator'] ?? 'equals';
        $expected = $cond['value'] ?? null;

        return match ($operator) {
            'equals' => $refValue == $expected,
            'not_equals' => $refValue != $expected,
            'contains' => is_array($refValue) ? in_array($expected, $refValue) : str_contains((string) $refValue, (string) $expected),
            'not_empty' => ! empty($refValue),
            'empty' => empty($refValue),
            default => true,
        };
    }
}
