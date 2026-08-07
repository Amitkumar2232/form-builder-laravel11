<div>
    @if($submitted)
        <div class="bg-white rounded-xl shadow p-8 text-center">
            <div class="text-4xl mb-4">✅</div>
            <h2 class="text-xl font-bold mb-2">{{ $form->settings['success_message'] ?? 'Thank you!' }}</h2>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-8">
            <h1 class="text-2xl font-bold mb-2">{{ $form->title }}</h1>
            @if($form->description)
                <p class="text-gray-500 mb-6">{{ $form->description }}</p>
            @endif

            <form wire:submit="submit">
                @php $validationBuilder = app(\App\Services\FormValidationBuilder::class); @endphp

                @foreach($form->schema['sections'] ?? [] as $section)
                    @if($section['title'] !== 'General' || count($form->schema['sections']) > 1)
                        <h3 class="font-semibold text-lg mt-6 mb-3 border-b pb-2">{{ $section['title'] }}</h3>
                    @endif

                    @foreach($section['fields'] ?? [] as $field)
                        @php
                            $visible = $validationBuilder->isFieldVisible($field, $data);
                        @endphp

                        @if($field['type'] === 'heading')
                            <h4 class="font-semibold mt-4 mb-2">{{ $field['label'] }}</h4>
                        @elseif($field['type'] === 'description')
                            <p class="text-gray-500 text-sm mb-3">{{ $field['label'] }}</p>
                        @elseif($field['type'] === 'newline')
                            <div class="mb-2"></div>
                        @elseif(\App\Services\FieldTypeRegistry::isInputType($field['type']))
                            <div class="mb-4" @if(!$visible) style="display:none" @endif
                                x-data x-show="$wire.data['{{ $field['conditions']['show_when']['field'] ?? '' }}'] !== undefined ? true : true">
                                <label class="block text-sm font-medium mb-1">
                                    {{ $field['label'] }}
                                    @if($field['required']) <span class="text-red-500">*</span> @endif
                                </label>

                                @switch($field['type'])
                                    @case('textarea')
                                        <textarea wire:model="data.{{ $field['key'] }}" rows="3"
                                            class="w-full border rounded-lg px-3 py-2 @error('data.'.$field['key']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] }}"></textarea>
                                        @break
                                    @case('dropdown')
                                        <select wire:model="data.{{ $field['key'] }}" class="w-full border rounded-lg px-3 py-2">
                                            <option value="">Select...</option>
                                            @foreach($field['options'] ?? [] as $opt)
                                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('radio')
                                        <div class="space-y-1">
                                            @foreach($field['options'] ?? [] as $opt)
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" wire:model="data.{{ $field['key'] }}" value="{{ $opt['value'] }}">
                                                    {{ $opt['label'] }}
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case('checkbox')
                                        <div class="space-y-1">
                                            @foreach($field['options'] ?? [] as $opt)
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" wire:model="data.{{ $field['key'] }}" value="{{ $opt['value'] }}">
                                                    {{ $opt['label'] }}
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case('file')
                                        <input type="file" wire:model="data.{{ $field['key'] }}" class="w-full text-sm">
                                        @break
                                    @case('rating')
                                        <div class="flex gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" wire:click="$set('data.{{ $field['key'] }}', {{ $i }})"
                                                    class="text-2xl {{ ($data[$field['key']] ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }}">★</button>
                                            @endfor
                                        </div>
                                        @break
                                    @default
                                        <input type="{{ $field['type'] === 'phone' ? 'tel' : $field['type'] }}"
                                            wire:model="data.{{ $field['key'] }}"
                                            class="w-full border rounded-lg px-3 py-2 @error('data.'.$field['key']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] }}">
                                @endswitch

                                @if($field['help_text'])
                                    <p class="text-xs text-gray-400 mt-1">{{ $field['help_text'] }}</p>
                                @endif
                                @error('data.'.$field['key']) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    @endforeach
                @endforeach

                @error('form') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 mt-4">
                    {{ $form->settings['submit_button_text'] ?? 'Submit' }}
                </button>
            </form>
        </div>
    @endif
</div>
