<?php

namespace App\Livewire;

use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;

class FormSubmissions extends Component
{
    use WithPagination;

    public Form $form;

    public string $search = '';

    public function mount(Form $form): void
    {
        $this->form = $form;
    }

    public function render()
    {
        $submissions = $this->form->submissions()
            ->when($this->search, fn ($q) => $q->where('data', 'like', '%'.$this->search.'%'))
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('livewire.form-submissions', compact('submissions'))
            ->layout('layouts.app');
    }
}
