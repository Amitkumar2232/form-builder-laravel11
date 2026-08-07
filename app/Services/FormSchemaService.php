<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormVersion;
use Illuminate\Support\Str;

class FormSchemaService
{
    public function __construct(
        protected FormSchemaNormalizer $normalizer,
        protected FormSchemaValidator $validator,
    ) {}

    public function saveForm(Form $form, array $schema, array $settings = [], ?string $changeNote = null): Form
    {
        $schema = $this->validator->validate($schema);
        $settings = array_merge($this->normalizer->normalize($schema)['settings'], $settings);

        if ($form->exists && $form->schema) {
            FormVersion::create([
                'form_id' => $form->id,
                'version_number' => $form->version,
                'schema' => $form->schema,
                'settings' => $form->settings,
                'change_note' => $changeNote ?? 'Auto-saved before update',
            ]);
        }

        $form->fill([
            'schema' => $schema,
            'settings' => $settings,
            'version' => $form->exists ? $form->version + 1 : 1,
        ]);

        if ($form->isDirty('schema') && $form->title !== ($schema['title'] ?? $form->title)) {
            $form->title = $schema['title'];
        }

        $form->save();

        return $form->fresh();
    }

    public function rollback(Form $form, int $versionNumber): Form
    {
        $version = $form->versions()->where('version_number', $versionNumber)->firstOrFail();

        return $this->saveForm(
            $form,
            $version->schema,
            $version->settings ?? [],
            "Rolled back to version {$versionNumber}"
        );
    }

    public function duplicateAsTemplate(Form $form, string $category = 'general'): Form
    {
        $copy = $form->replicate(['uuid', 'slug', 'status', 'is_template']);
        $copy->uuid = (string) Str::uuid();
        $copy->slug = Str::slug($form->title).'-template-'.Str::random(4);
        $copy->title = $form->title.' (Template)';
        $copy->is_template = true;
        $copy->template_category = $category;
        $copy->status = 'draft';
        $copy->save();

        return $copy;
    }

    public function createFromTemplate(Form $template): Form
    {
        $form = new Form([
            'title' => str_replace(' (Template)', '', $template->title),
            'description' => $template->description,
            'schema' => $template->schema,
            'settings' => $template->settings,
            'status' => 'draft',
        ]);
        $form->save();

        return $form;
    }
}
