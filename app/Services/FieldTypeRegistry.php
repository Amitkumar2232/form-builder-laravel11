<?php

namespace App\Services;

class FieldTypeRegistry
{
    public const INPUT_TYPES = [
        'text', 'textarea', 'number', 'email', 'phone', 'date',
        'dropdown', 'radio', 'checkbox', 'file', 'rating', 'hidden',
    ];

    public const LAYOUT_TYPES = [
        'heading', 'description', 'newline',
    ];

    public const ALL_TYPES = [...self::INPUT_TYPES, ...self::LAYOUT_TYPES];

    public const OPTION_TYPES = ['dropdown', 'radio', 'checkbox'];

    public const LABELS = [
        'text' => 'Text input',
        'textarea' => 'Text area',
        'number' => 'Number input',
        'email' => 'Email input',
        'phone' => 'Phone input',
        'date' => 'Date picker',
        'dropdown' => 'Dropdown',
        'radio' => 'Radio buttons',
        'checkbox' => 'Checkboxes',
        'file' => 'File upload',
        'rating' => 'Rating',
        'hidden' => 'Hidden field',
        'heading' => 'Section heading',
        'description' => 'Description',
        'newline' => 'New line',
    ];

    public static function defaultField(string $type = 'text'): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => $type,
            'key' => 'field_'.substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8),
            'label' => self::LABELS[$type] ?? 'Field',
            'placeholder' => '',
            'help_text' => '',
            'default' => null,
            'required' => false,
            'options' => self::needsOptions($type) ? [
                ['label' => 'Option 1', 'value' => 'option_1'],
                ['label' => 'Option 2', 'value' => 'option_2'],
            ] : [],
            'validation' => [
                'min' => null,
                'max' => null,
                'min_length' => null,
                'max_length' => null,
                'regex' => null,
                'file_types' => [],
                'max_file_size_kb' => null,
            ],
            'conditions' => null,
        ];
    }

    public static function needsOptions(string $type): bool
    {
        return in_array($type, self::OPTION_TYPES, true);
    }

    public static function isInputType(string $type): bool
    {
        return in_array($type, self::INPUT_TYPES, true);
    }

    public static function normalizeType(string $type): string
    {
        $aliases = [
            'string' => 'text',
            'select' => 'dropdown',
            'tel' => 'phone',
            'section' => 'heading',
            'paragraph' => 'description',
            'upload' => 'file',
        ];

        $type = strtolower(trim($type));

        return $aliases[$type] ?? (in_array($type, self::ALL_TYPES, true) ? $type : 'text');
    }
}
