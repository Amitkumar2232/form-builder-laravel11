<?php

namespace App\Livewire;

use App\Jobs\GenerateFormFromPromptJob;
use App\Jobs\ImportDocumentJob;
use App\Models\AiGenerationLog;
use App\Models\Form;
use App\Models\FormImport;
use App\Services\FieldTypeRegistry;
use App\Services\FormSchemaNormalizer;
use App\Services\FormSchemaService;
use App\Services\FormSchemaValidator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormWizard extends Component
{
    use WithFileUploads;

    public ?int $formId = null;

    public int $step = 1;

    public string $title = '';

    public string $description = '';

    public array $schema = [];

    public array $settings = [];

    public string $schemaJson = '';

    public bool $showJsonEditor = false;

    public ?string $selectedSectionId = null;

    public ?string $selectedFieldId = null;

    public string $aiPrompt = '';

    public ?string $aiJobUuid = null;

    public string $aiStatus = '';

    public $importFile;

    public ?string $importUuid = null;

    public string $importStatus = '';

    public array $importWarnings = [];

    public array $importMapping = [];

    public string $status = 'draft';

    protected FormSchemaNormalizer $normalizer;

    protected FormSchemaValidator $validator;

    protected FormSchemaService $schemaService;

    public function boot(
        FormSchemaNormalizer $normalizer,
        FormSchemaValidator $validator,
        FormSchemaService $schemaService,
    ): void {
        $this->normalizer = $normalizer;
        $this->validator = $validator;
        $this->schemaService = $schemaService;
    }

    public function mount(?Form $form = null, ?int $templateId = null): void
    {
        if ($templateId) {
            $template = Form::where('is_template', true)->findOrFail($templateId);
            $form = $this->schemaService->createFromTemplate($template);
        }

        if ($form?->exists) {
            $this->formId = $form->id;
            $this->title = $form->title;
            $this->description = $form->description ?? '';
            $this->schema = $this->normalizer->normalize($form->schema);
            $this->settings = $form->settings ?? [];
            $this->status = $form->status;
        } else {
            $this->schema = $this->normalizer->emptySchema();
        }

        $this->selectedSectionId = $this->schema['sections'][0]['id'] ?? null;
        $this->syncJsonFromSchema();
    }

    public function getFieldTypesProperty(): array
    {
        return FieldTypeRegistry::LABELS;
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate(['title' => 'required|string|max:200']);
            $this->schema['title'] = $this->title;
            $this->schema['description'] = $this->description;
        }

        if ($this->step === 2) {
            try {
                $this->schema = $this->validator->validate($this->schema);
            } catch (ValidationException $e) {
                $this->addError('schema', collect($e->errors())->flatten()->first());

                return;
            }
        }

        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 4) {
            $this->step = $step;
        }
    }

    public function addSection(): void
    {
        $this->schema['sections'][] = [
            'id' => (string) Str::uuid(),
            'title' => 'New Section',
            'fields' => [],
        ];
        $this->selectedSectionId = end($this->schema['sections'])['id'];
        $this->syncJsonFromSchema();
    }

    public function addField(string $type, ?string $sectionId = null): void
    {
        $sectionId ??= $this->selectedSectionId;
        $field = FieldTypeRegistry::defaultField($type);

        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] === $sectionId) {
                $section['fields'][] = $field;
                $this->selectedFieldId = $field['id'];
                break;
            }
        }

        $this->syncJsonFromSchema();
    }

    public function selectField(string $fieldId, string $sectionId): void
    {
        $this->selectedFieldId = $fieldId;
        $this->selectedSectionId = $sectionId;
    }

    public function duplicateField(string $sectionId, string $fieldId): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] !== $sectionId) {
                continue;
            }

            foreach ($section['fields'] as $field) {
                if ($field['id'] === $fieldId) {
                    $copy = $field;
                    $copy['id'] = (string) Str::uuid();
                    $copy['key'] = $field['key'].'_copy';
                    $copy['label'] = $field['label'].' (copy)';
                    $section['fields'][] = $copy;
                    $this->selectedFieldId = $copy['id'];
                    break 2;
                }
            }
        }

        $this->syncJsonFromSchema();
    }

    public function deleteField(string $sectionId, string $fieldId): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] === $sectionId) {
                $section['fields'] = array_values(array_filter(
                    $section['fields'],
                    fn ($f) => $f['id'] !== $fieldId
                ));
                break;
            }
        }

        if ($this->selectedFieldId === $fieldId) {
            $this->selectedFieldId = null;
        }

        $this->syncJsonFromSchema();
    }

    public function deleteSection(string $sectionId): void
    {
        if (count($this->schema['sections']) <= 1) {
            return;
        }

        $this->schema['sections'] = array_values(array_filter(
            $this->schema['sections'],
            fn ($s) => $s['id'] !== $sectionId
        ));

        $this->selectedSectionId = $this->schema['sections'][0]['id'] ?? null;
        $this->syncJsonFromSchema();
    }

    public function reorderFields(string $sectionId, array $orderedIds): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] !== $sectionId) {
                continue;
            }

            $byId = collect($section['fields'])->keyBy('id');
            $section['fields'] = collect($orderedIds)
                ->map(fn ($id) => $byId->get($id))
                ->filter()
                ->values()
                ->all();
        }

        $this->syncJsonFromSchema();
    }

    public function updateFieldProperty(string $sectionId, string $fieldId, string $property, $value): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] !== $sectionId) {
                continue;
            }

            foreach ($section['fields'] as &$field) {
                if ($field['id'] === $fieldId) {
                    if (str_contains($property, '.')) {
                        $parts = explode('.', $property);
                        $target = &$field;

                        while (count($parts) > 1) {
                            $part = array_shift($parts);

                            if (! isset($target[$part]) || ! is_array($target[$part])) {
                                $target[$part] = [];
                            }

                            $target = &$target[$part];
                        }

                        $target[array_shift($parts)] = $value;
                    } else {
                        $field[$property] = $value;
                    }
                    break 2;
                }
            }
        }

        $this->syncJsonFromSchema();
    }

    public function addFieldOption(string $sectionId, string $fieldId): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] !== $sectionId) {
                continue;
            }

            foreach ($section['fields'] as &$field) {
                if ($field['id'] === $fieldId) {
                    $n = count($field['options']) + 1;
                    $field['options'][] = ['label' => "Option {$n}", 'value' => "option_{$n}"];
                    break 2;
                }
            }
        }

        $this->syncJsonFromSchema();
    }

    public function removeFieldOption(string $sectionId, string $fieldId, int $index): void
    {
        foreach ($this->schema['sections'] as &$section) {
            if ($section['id'] !== $sectionId) {
                continue;
            }

            foreach ($section['fields'] as &$field) {
                if ($field['id'] === $fieldId) {
                    unset($field['options'][$index]);
                    $field['options'] = array_values($field['options']);
                    break 2;
                }
            }
        }

        $this->syncJsonFromSchema();
    }

    public function toggleJsonEditor(): void
    {
        $this->showJsonEditor = ! $this->showJsonEditor;
        $this->syncJsonFromSchema();
    }

    public function applyJsonSchema(): void
    {
        try {
            $this->schema = $this->validator->tryParseJson($this->schemaJson);
            $this->title = $this->schema['title'];
            $this->description = $this->schema['description'] ?? '';
            session()->flash('message', 'JSON schema applied successfully.');
        } catch (ValidationException $e) {
            $this->addError('schemaJson', collect($e->errors())->flatten()->first());
        }
    }

    public function syncJsonFromSchema(): void
    {
        $this->schemaJson = $this->normalizer->toJson($this->schema);
    }

    public function startAiGeneration(string $mode = 'create'): void
    {
        $this->validate(['aiPrompt' => 'required|string|min:10|max:2000']);

        $log = AiGenerationLog::create([
            'job_uuid' => (string) Str::uuid(),
            'form_id' => $this->formId,
            'mode' => $mode,
            'prompt' => $this->aiPrompt,
            'status' => 'pending',
        ]);

        $this->aiJobUuid = $log->job_uuid;
        $this->aiStatus = 'pending';

        GenerateFormFromPromptJob::dispatch($log);
    }

    public function pollAiStatus(): void
    {
        if (! $this->aiJobUuid) {
            return;
        }

        $log = AiGenerationLog::where('job_uuid', $this->aiJobUuid)->first();

        if (! $log) {
            return;
        }

        $this->aiStatus = $log->status;

        if ($log->status === 'completed' && $log->result_schema) {
            $this->schema = $log->result_schema;
            $this->title = $this->schema['title'];
            $this->description = $this->schema['description'] ?? '';
            $this->syncJsonFromSchema();
            $this->step = 2;
            session()->flash('message', 'AI form generated successfully.');
            $this->aiJobUuid = null;
        }

        if ($log->status === 'failed') {
            $this->addError('aiPrompt', $log->error_message ?? 'AI generation failed.');
            $this->aiJobUuid = null;
        }
    }

    public function startImport(): void
    {
        $this->validate(['importFile' => 'required|file|mimes:docx,xlsx|max:10240']);

        $ext = $this->importFile->getClientOriginalExtension();
        $path = $this->importFile->store('imports', 'local');

        $import = FormImport::create([
            'import_uuid' => (string) Str::uuid(),
            'form_id' => $this->formId,
            'original_filename' => $this->importFile->getClientOriginalName(),
            'file_type' => $ext,
            'stored_path' => $path,
            'status' => 'pending',
        ]);

        $this->importUuid = $import->import_uuid;
        $this->importStatus = 'pending';

        ImportDocumentJob::dispatch($import);
    }

    public function pollImportStatus(): void
    {
        if (! $this->importUuid) {
            return;
        }

        $import = FormImport::where('import_uuid', $this->importUuid)->first();

        if (! $import) {
            return;
        }

        $this->importStatus = $import->status;
        $this->importWarnings = $import->warnings ?? [];

        if ($import->status === 'preview' && $import->parsed_schema) {
            $this->schema = $import->parsed_schema;
            $this->title = $this->schema['title'];
            $this->syncJsonFromSchema();
            $this->step = 2;
        }

        if ($import->status === 'failed') {
            $this->addError('importFile', $import->error_message ?? 'Import failed.');
            $this->importUuid = null;
        }
    }

    public function applyImportMapping(): void
    {
        if (! $this->importUuid) {
            return;
        }

        $import = FormImport::where('import_uuid', $this->importUuid)->firstOrFail();
        $import->update(['mapping_overrides' => $this->importMapping, 'status' => 'completed']);

        $this->schema = $import->parsed_schema;
        $this->title = $this->schema['title'];
        $this->syncJsonFromSchema();
        $this->importUuid = null;
        session()->flash('message', 'Import applied successfully.');
    }

    public function save(bool $publish = false): void
    {
        $this->schema['title'] = $this->title;
        $this->schema['description'] = $this->description;
        $this->schema['settings'] = array_merge($this->schema['settings'] ?? [], $this->settings);

        $form = $this->formId ? Form::findOrFail($this->formId) : new Form;

        if (! $this->formId) {
            $form->slug = Str::slug($this->title).'-'.Str::random(6);
        }

        $form->title = $this->title;
        $form->description = $this->description;
        $form->status = $publish ? 'published' : $this->status;

        $this->schemaService->saveForm($form, $this->schema, $this->settings);
        $this->formId = $form->id;

        session()->flash('message', $publish ? 'Form published!' : 'Form saved as draft.');

        if ($publish) {
            $this->redirect(route('forms.show', $form));
        }
    }

    public function rollbackVersion(int $versionNumber): void
    {
        if (! $this->formId) {
            return;
        }

        $form = $this->schemaService->rollback(Form::findOrFail($this->formId), $versionNumber);
        $this->schema = $form->schema;
        $this->settings = $form->settings ?? [];
        $this->syncJsonFromSchema();
        session()->flash('message', "Rolled back to version {$versionNumber}.");
    }

    public function render()
    {
        $versions = $this->formId
            ? Form::find($this->formId)?->versions()->limit(10)->get() ?? collect()
            : collect();

        $selectedField = null;
        if ($this->selectedFieldId && $this->selectedSectionId) {
            foreach ($this->schema['sections'] as $section) {
                if ($section['id'] === $this->selectedSectionId) {
                    foreach ($section['fields'] as $field) {
                        if ($field['id'] === $this->selectedFieldId) {
                            $selectedField = $field;
                            break 2;
                        }
                    }
                }
            }
        }

        return view('livewire.form-wizard', [
            'versions' => $versions,
            'selectedField' => $selectedField,
        ])->layout('layouts.app');
    }
}
