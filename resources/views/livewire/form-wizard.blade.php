<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Form Builder</h1>

    {{-- Stepper --}}
    <div class="flex items-center mb-8">
        @foreach(['Details', 'Builder', 'Settings', 'Finish'] as $i => $label)
            @php $num = $i + 1; @endphp
            <button wire:click="goToStep({{ $num }})" class="flex items-center gap-2 {{ $step >= $num ? 'text-blue-600' : 'text-gray-400' }}">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold border-2
                    {{ $step > $num ? 'bg-blue-600 border-blue-600 text-white' : ($step === $num ? 'border-blue-600 text-blue-600' : 'border-gray-300') }}">
                    @if($step > $num) ✓ @else {{ $num }} @endif
                </span>
                <span class="font-medium hidden sm:inline">{{ $label }}</span>
            </button>
            @if($i < 3)
                <div class="step-line flex-1 h-0.5 mx-2 {{ $step > $num ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
            @endif
        @endforeach
    </div>

    {{-- Step 1: Details --}}
    @if($step === 1)
        <div class="bg-white rounded-xl border border-gray-200 p-8 max-w-2xl mx-auto">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-blue-600 font-semibold text-lg">Form basics</h2>
                    <p class="text-gray-500 text-sm mt-1">Enter the primary details for your new data-collection form.</p>
                </div>
                <span class="bg-blue-50 text-blue-600 text-xs font-medium px-3 py-1 rounded-full">Survey form</span>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Form title <span class="text-red-500">*</span></label>
                <input type="text" wire:model.live="title" maxlength="200"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Fall 2024 Registration">
                <div class="flex justify-between mt-1">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @else <span></span> @enderror
                    <span class="text-xs text-gray-400">{{ strlen($title) }}/200</span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea wire:model="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional description"></textarea>
            </div>

            @if($formId)
                <p class="text-sm text-gray-500 mb-6">Public URL: <a href="{{ url('/submit/'.(\App\Models\Form::find($formId)?->slug ?? '')) }}" class="text-blue-600" target="_blank">{{ url('/submit/'.(\App\Models\Form::find($formId)?->slug ?? '')) }}</a></p>
            @else
                <p class="text-sm text-gray-500 mb-6">Public URL will be generated on save.</p>
            @endif

            {{-- AI Generation --}}
            <div class="border border-dashed border-purple-300 rounded-lg p-4 mb-6 bg-purple-50">
                <h3 class="font-medium text-purple-800 mb-2">✨ Generate with AI</h3>
                <textarea wire:model="aiPrompt" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mb-2"
                    placeholder="e.g., Internship application with education history, skills and resume upload"></textarea>
                @error('aiPrompt') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                <div class="flex gap-2">
                    <button wire:click="startAiGeneration('create')" class="text-sm bg-purple-600 text-white px-4 py-1.5 rounded-lg hover:bg-purple-700">Generate Form</button>
                    @if($formId)
                        <button wire:click="startAiGeneration('edit')" class="text-sm bg-purple-100 text-purple-700 px-4 py-1.5 rounded-lg hover:bg-purple-200">AI Edit Existing</button>
                    @endif
                </div>
                @if($aiJobUuid)
                    <div wire:poll.2s="pollAiStatus" class="mt-2 text-sm text-purple-700">
                        Status: {{ $aiStatus }}...
                    </div>
                @endif
            </div>

            {{-- Import --}}
            <div class="border border-dashed border-green-300 rounded-lg p-4 bg-green-50">
                <h3 class="font-medium text-green-800 mb-2">📄 Import from Word / Excel</h3>
                <input type="file" wire:model="importFile" accept=".docx,.xlsx" class="text-sm mb-2">
                @error('importFile') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                <button wire:click="startImport" class="text-sm bg-green-600 text-white px-4 py-1.5 rounded-lg hover:bg-green-700">Upload & Parse</button>
                @if($importUuid)
                    <div wire:poll.2s="pollImportStatus" class="mt-2 text-sm text-green-700">
                        Import status: {{ $importStatus }}
                        @foreach($importWarnings as $w)
                            <p class="text-yellow-700">⚠ {{ $w }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Step 2: Builder --}}
    @if($step === 2)
        @error('schema') <div class="bg-red-50 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm">{{ $message }}</div> @enderror

        <div class="flex gap-4">
            {{-- Canvas --}}
            <div class="flex-1">
                <div class="flex gap-2 mb-4">
                    @foreach($schema['sections'] as $section)
                        <button wire:click="$set('selectedSectionId', '{{ $section['id'] }}')"
                            class="px-3 py-1 rounded-lg text-sm {{ $selectedSectionId === $section['id'] ? 'bg-blue-600 text-white' : 'bg-white border text-gray-600' }}">
                            {{ $section['title'] }}
                        </button>
                    @endforeach
                    <button wire:click="addSection" class="px-3 py-1 rounded-lg text-sm border border-dashed text-gray-500 hover:border-blue-400">+ Section</button>
                </div>

                @foreach($schema['sections'] as $section)
                    @if($section['id'] === $selectedSectionId)
                        <div class="canvas-area border-2 border-dashed border-gray-300 rounded-xl p-6 min-h-96 bg-white"
                            x-data="{ initSortable() {
                                const el = this.$refs.sortable;
                                if (el && !el._sortable) {
                                    el._sortable = Sortable.create(el, {
                                        animation: 150,
                                        handle: '.drag-handle',
                                        onEnd: (evt) => {
                                            const ids = [...el.children].map(c => c.dataset.fieldId);
                                            @this.reorderFields('{{ $section['id'] }}', ids);
                                        }
                                    });
                                }
                            }}"
                            x-init="initSortable()">
                            <div x-ref="sortable">
                                @forelse($section['fields'] as $field)
                                    <div data-field-id="{{ $field['id'] }}"
                                        wire:click="selectField('{{ $field['id'] }}', '{{ $section['id'] }}')"
                                        class="field-card border rounded-lg p-4 mb-3 bg-white cursor-pointer hover:shadow-sm {{ $selectedFieldId === $field['id'] ? 'selected ring-2 ring-blue-500' : '' }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center gap-2">
                                                <span class="drag-handle cursor-grab text-gray-400">⠿</span>
                                                <div>
                                                    <span class="font-medium">{{ $field['label'] }}</span>
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded ml-2">{{ $field['type'] }}</span>
                                                    @if($field['required']) <span class="text-red-500 text-xs">*</span> @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-1">
                                                <button wire:click.stop="duplicateField('{{ $section['id'] }}', '{{ $field['id'] }}')" class="p-1 text-gray-400 hover:text-blue-600" title="Duplicate">⧉</button>
                                                <button wire:click.stop="deleteField('{{ $section['id'] }}', '{{ $field['id'] }}')" class="p-1 text-gray-400 hover:text-red-600" title="Delete">🗑</button>
                                            </div>
                                        </div>
                                        @if(in_array($field['type'], ['text','email','number','phone','date','textarea','dropdown']))
                                            <div class="mt-2">
                                                <input disabled class="w-full border rounded px-3 py-1.5 text-sm bg-gray-50" placeholder="{{ $field['placeholder'] ?: $field['label'] }}">
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-400 text-center py-12">Click a field type on the right to add fields, or drag them here.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="mt-4 flex gap-2">
                    <button wire:click="toggleJsonEditor" class="text-sm text-gray-600 border px-3 py-1.5 rounded-lg hover:bg-gray-50">
                        {{ $showJsonEditor ? 'Hide' : 'Show' }} JSON Editor
                    </button>
                </div>

                @if($showJsonEditor)
                    <div class="mt-4">
                        <textarea wire:model="schemaJson" rows="15" class="w-full font-mono text-xs border rounded-lg p-3"></textarea>
                        @error('schemaJson') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        <button wire:click="applyJsonSchema" class="mt-2 text-sm bg-gray-800 text-white px-4 py-1.5 rounded-lg">Apply JSON</button>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="w-72 bg-white border rounded-xl p-4 h-fit sticky top-4">
                @if($selectedField)
                    <h3 class="font-semibold text-gray-800 mb-3">Field options</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="block text-gray-600 mb-1">Label</label>
                            <input type="text" value="{{ $selectedField['label'] }}"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'label', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Key</label>
                            <input type="text" value="{{ $selectedField['key'] }}"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'key', $event.target.value)"
                                class="w-full border rounded px-2 py-1 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Placeholder</label>
                            <input type="text" value="{{ $selectedField['placeholder'] ?? '' }}"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'placeholder', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Help text</label>
                            <input type="text" value="{{ $selectedField['help_text'] ?? '' }}"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'help_text', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" {{ $selectedField['required'] ? 'checked' : '' }}
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'required', $event.target.checked)">
                            Required
                        </label>
                        @if(is_array($selectedField) && \App\Services\FieldTypeRegistry::needsOptions($selectedField['type']))
                            <div>
                                <label class="block text-gray-600 mb-1">Options</label>
                                @if(is_array($selectedField['options'] ?? null))
                                    @foreach($selectedField['options'] as $oi => $opt)
                                        <div class="flex gap-1 mb-1">
                                            <input type="text" value="{{ is_array($opt) ? ($opt['label'] ?? '') : $opt }}" class="flex-1 border rounded px-2 py-1 text-xs"
                                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'options.{{ $oi }}.label', $event.target.value)">
                                            <button wire:click="removeFieldOption('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', {{ $oi }})" class="text-red-500 text-xs">×</button>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-xs text-gray-500">No options available.</p>
                                @endif
                                <button wire:click="addFieldOption('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}')" class="text-xs text-blue-600">+ Add option</button>
                            </div>
                        @endif
                        <div class="border-t pt-3">
                            <p class="text-gray-600 font-medium mb-2">Validation</p>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" placeholder="Min length" value="{{ $selectedField['validation']['min_length'] ?? '' }}"
                                    wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'validation.min_length', $event.target.value)"
                                    class="border rounded px-2 py-1 text-xs">
                                <input type="number" placeholder="Max length" value="{{ $selectedField['validation']['max_length'] ?? '' }}"
                                    wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'validation.max_length', $event.target.value)"
                                    class="border rounded px-2 py-1 text-xs">
                            </div>
                        </div>
                        <div class="border-t pt-3">
                            <p class="text-gray-600 font-medium mb-2">Conditional logic</p>
                            <input type="text" placeholder="Show when field key" value="{{ $selectedField['conditions']['show_when']['field'] ?? '' }}"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'conditions.show_when.field', $event.target.value)"
                                class="w-full border rounded px-2 py-1 text-xs mb-1">
                            <select class="w-full border rounded px-2 py-1 text-xs"
                                wire:change="updateFieldProperty('{{ $selectedSectionId }}', '{{ $selectedField['id'] }}', 'conditions.show_when.operator', $event.target.value)">
                                <option value="equals" {{ ($selectedField['conditions']['show_when']['operator'] ?? '') === 'equals' ? 'selected' : '' }}>Equals</option>
                                <option value="not_equals" {{ ($selectedField['conditions']['show_when']['operator'] ?? '') === 'not_equals' ? 'selected' : '' }}>Not equals</option>
                                <option value="not_empty">Not empty</option>
                            </select>
                        </div>
                    </div>
                @else
                    <h3 class="font-semibold text-gray-500 text-xs uppercase tracking-wide mb-3">Standard Fields</h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($this->fieldTypes as $type => $label)
                            <button wire:click="addField('{{ $type }}')"
                                class="text-left text-xs border rounded-lg px-2 py-2 hover:bg-blue-50 hover:border-blue-300 transition">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Step 3: Settings --}}
    @if($step === 3)
        <div class="bg-white rounded-xl border p-8 max-w-2xl mx-auto space-y-4">
            <h2 class="text-lg font-semibold">Form Settings</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Submit button text</label>
                <input type="text" wire:model="settings.submit_button_text" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Success message</label>
                <textarea wire:model="settings.success_message" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="settings.allow_multiple_submissions">
                Allow multiple submissions
            </label>
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select wire:model="status" class="border rounded-lg px-3 py-2">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            @if($versions->count())
                <div class="border-t pt-4">
                    <h3 class="font-medium mb-2">Version History</h3>
                    @foreach($versions as $version)
                        <div class="flex justify-between items-center text-sm py-1">
                            <span>v{{ $version->version_number }} — {{ $version->change_note ?? 'Saved' }} ({{ $version->created_at->diffForHumans() }})</span>
                            <button wire:click="rollbackVersion({{ $version->version_number }})" class="text-blue-600 text-xs">Rollback</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Step 4: Finish --}}
    @if($step === 4)
        <div class="bg-white rounded-xl border p-8 max-w-2xl mx-auto text-center">
            <div class="text-5xl mb-4">🎉</div>
            <h2 class="text-xl font-bold mb-2">Ready to launch!</h2>
            <p class="text-gray-500 mb-6">Your form "{{ $title }}" has {{ collect($schema['sections'])->sum(fn($s) => count($s['fields'])) }} fields across {{ count($schema['sections']) }} sections.</p>
            @if($formId)
                <p class="text-sm mb-4">Public URL: <a href="{{ route('forms.public', \App\Models\Form::find($formId)?->slug) }}" class="text-blue-600" target="_blank">{{ route('forms.public', \App\Models\Form::find($formId)?->slug) }}</a></p>
            @endif
            <div class="flex justify-center gap-3">
                <button wire:click="save(false)" class="border border-gray-300 px-6 py-2 rounded-lg hover:bg-gray-50">Save Draft</button>
                <button wire:click="save(true)" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Publish Form</button>
            </div>
        </div>
    @endif

    {{-- Footer nav --}}
    <div class="flex justify-between mt-8">
        @if($step > 1)
            <button wire:click="prevStep" class="border border-gray-300 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-50">← Back</button>
        @else
            <a href="{{ route('forms.index') }}" class="border border-red-300 text-red-600 px-6 py-2 rounded-lg hover:bg-red-50">Cancel</a>
        @endif

        @if($step < 4)
            <button wire:click="nextStep" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Next: {{ ['', 'Builder', 'Settings', 'Finish'][$step] }} →
            </button>
        @elseif($step === 4)
            <span></span>
        @endif
    </div>
</div>
