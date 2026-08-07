<?php

namespace App\Livewire;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\FormValidationBuilder;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PublicFormFill extends Component
{
    use WithFileUploads;
    use WithPagination;

    public Form $form;

    public array $data = [];

    public bool $submitted = false;

    public function mount(string $slug): void
    {
        $this->form = Form::where('slug', $slug)->where('status', 'published')->firstOrFail();

        foreach ($this->form->schema['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (! empty($field['key']) && isset($field['default'])) {
                    $this->data[$field['key']] = $field['default'];
                }
            }
        }
    }

    public function submit(FormValidationBuilder $validationBuilder): void
    {
        $key = 'form-submit:'.$this->form->id.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('form', 'Too many submissions. Please try again later.');

            return;
        }

        RateLimiter::hit($key, 60);

        $visible = $validationBuilder->evaluateConditions($this->form->schema, $this->data);
        $built = $validationBuilder->rulesFromSchema($this->form->schema);

        $rules = [];
        $attributes = [];

        foreach ($built['rules'] as $fieldKey => $fieldRules) {
            if ($visible[$fieldKey] ?? true) {
                $rules["data.{$fieldKey}"] = $fieldRules;
                $attributes["data.{$fieldKey}"] = $built['attributes'][$fieldKey] ?? $fieldKey;
            }
        }

        $validated = $this->validate($rules, [], $attributes);
        $validatedData = $validated['data'] ?? [];

        $stored = [];
        foreach ($validatedData as $k => $v) {
            if ($v instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $stored[$k] = $v->store('submissions/'.$this->form->id, 'public');
            } else {
                $stored[$k] = $v;
            }
        }

        FormSubmission::create([
            'form_id' => $this->form->id,
            'data' => $stored,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'submitted_at' => now(),
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public-form-fill')
            ->layout('layouts.public');
    }
}
