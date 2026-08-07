<?php

use Livewire\Component;

new class extends Component
{
    public array $sections = [];


    public function mount()
    {
        $this->sections = [
            [
                'id' => uniqid(),
                'title' => 'Personal Information',
                'fields' => []
            ]
        ];
    }


    public function addSection()
    {
        $this->sections[] = [
            'id' => uniqid(),
            'title' => 'New Section',
            'fields' => []
        ];
    }


    public function removeSection($sectionId)
    {
        $this->sections = collect($this->sections)
            ->reject(fn($section) => $section['id'] === $sectionId)
            ->values()
            ->toArray();
    }


    public function addField($sectionId)
    {
        foreach ($this->sections as &$section) {

            if ($section['id'] === $sectionId) {

                $section['fields'][] = [
                    'id' => uniqid(),
                    'type' => 'text',
                    'label' => 'New Field',
                    'key' => 'new_field',
                    'placeholder' => '',
                    'required' => false,
                    'validation' => []
                ];

                break;
            }
        }

        $this->sections = $this->sections;
    }


    public function removeField($sectionId, $fieldId)
    {
        foreach ($this->sections as &$section) {

            if ($section['id'] === $sectionId) {

                $section['fields'] = collect($section['fields'])
                    ->reject(fn($field) => $field['id'] === $fieldId)
                    ->values()
                    ->toArray();

                break;
            }
        }

        $this->sections = $this->sections;
    }
};

?>


@livewireStyles


<div class="container mt-4">


    <h2 class="mb-3">
        AI Form Builder
    </h2>



    <button
        class="btn btn-primary mb-3"
        wire:click="addSection">

        + Add Section

    </button>



    @foreach($sections ?? [] as $section)


        <div class="card mb-3">


            <div class="card-header d-flex justify-content-between align-items-center">


                <strong>
                    {{ $section['title'] }}
                </strong>



                <button
                    class="btn btn-danger btn-sm"
                    wire:click="removeSection('{{ $section['id'] }}')">

                    Delete Section

                </button>


            </div>




            <div class="card-body">


                <button
                    class="btn btn-success btn-sm mb-3"
                    wire:click="addField('{{ $section['id'] }}')">

                    + Add Field

                </button>




                @forelse($section['fields'] ?? [] as $field)


                    <div class="border rounded p-3 mb-2">


                        <div class="d-flex justify-content-between align-items-center">


                            <div>


                                <strong>
                                    {{ $field['label'] }}
                                </strong>



                                <span class="badge bg-secondary ms-2">

                                    {{ $field['type'] }}

                                </span>


                            </div>




                            <button
                                class="btn btn-danger btn-sm"
                                wire:click="removeField('{{ $section['id'] }}','{{ $field['id'] }}')">

                                Delete Field

                            </button>



                        </div>



                    </div>



                @empty


                    <p class="text-muted">
                        No fields added yet
                    </p>


                @endforelse



            </div>


        </div>



    @endforeach



</div>



@livewireScripts