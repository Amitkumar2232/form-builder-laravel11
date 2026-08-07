<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Services\FieldTypeRegistry;
use App\Services\FormSchemaNormalizer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $normalizer = app(FormSchemaNormalizer::class);

        $templates = [
            [
                'title' => 'Contact Us (Template)',
                'category' => 'contact',
                'fields' => [
                    ['type' => 'text', 'key' => 'name', 'label' => 'Full Name', 'required' => true],
                    ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
                    ['type' => 'phone', 'key' => 'phone', 'label' => 'Phone Number'],
                    ['type' => 'textarea', 'key' => 'message', 'label' => 'Message', 'required' => true],
                ],
            ],
            [
                'title' => 'Job Application (Template)',
                'category' => 'hr',
                'fields' => [
                    ['type' => 'text', 'key' => 'full_name', 'label' => 'Full Name', 'required' => true],
                    ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
                    ['type' => 'dropdown', 'key' => 'position', 'label' => 'Position', 'required' => true,
                        'options' => [['label' => 'Developer', 'value' => 'dev'], ['label' => 'Designer', 'value' => 'design']]],
                    ['type' => 'file', 'key' => 'resume', 'label' => 'Resume', 'required' => true],
                ],
            ],
            [
                'title' => 'Event Registration (Template)',
                'category' => 'events',
                'fields' => [
                    ['type' => 'text', 'key' => 'name', 'label' => 'Name', 'required' => true],
                    ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
                    ['type' => 'radio', 'key' => 'attendance', 'label' => 'Will you attend?', 'required' => true,
                        'options' => [['label' => 'Yes', 'value' => 'yes'], ['label' => 'No', 'value' => 'no']]],
                    ['type' => 'checkbox', 'key' => 'dietary', 'label' => 'Dietary requirements',
                        'options' => [['label' => 'Vegetarian', 'value' => 'veg'], ['label' => 'Vegan', 'value' => 'vegan']]],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            $fields = [];
            foreach ($tpl['fields'] as $f) {
                $field = FieldTypeRegistry::defaultField($f['type']);
                $fields[] = array_merge($field, $f);
            }

            Form::create([
                'uuid' => (string) Str::uuid(),
                'slug' => Str::slug($tpl['title']).'-'.Str::random(4),
                'title' => $tpl['title'],
                'schema' => $normalizer->normalize([
                    'title' => $tpl['title'],
                    'sections' => [['id' => (string) Str::uuid(), 'title' => 'General', 'fields' => $fields]],
                ]),
                'status' => 'draft',
                'is_template' => true,
                'template_category' => $tpl['category'],
            ]);
        }
    }
}
