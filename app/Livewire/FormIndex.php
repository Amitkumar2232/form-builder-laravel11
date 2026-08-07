<?php

namespace App\Livewire;

use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;

class FormIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function deleteForm(int $formId): void
    {
        Form::findOrFail($formId)->delete();
        session()->flash('message', 'Form deleted.');
    }

    public function render()
    {
        $forms = Form::query()
            ->where('is_template', false)
            ->withCount('submissions')
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->orderByDesc('updated_at')
            ->paginate(10);

        $templates = Form::where('is_template', true)->orderBy('title')->get();

        return view('livewire.form-index', compact('forms', 'templates'))
            ->layout('layouts.app');
    }
}
