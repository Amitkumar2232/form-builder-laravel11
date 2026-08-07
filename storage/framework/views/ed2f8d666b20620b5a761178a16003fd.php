<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Form Builder</h1>

    
    <div class="flex items-center mb-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Details', 'Builder', 'Settings', 'Finish']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php $num = $i + 1; ?>
            <button wire:click="goToStep(<?php echo e($num); ?>)" class="flex items-center gap-2 <?php echo e($step >= $num ? 'text-blue-600' : 'text-gray-400'); ?>">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold border-2
                    <?php echo e($step > $num ? 'bg-blue-600 border-blue-600 text-white' : ($step === $num ? 'border-blue-600 text-blue-600' : 'border-gray-300')); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step > $num): ?> ✓ <?php else: ?> <?php echo e($num); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span class="font-medium hidden sm:inline"><?php echo e($label); ?></span>
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < 3): ?>
                <div class="step-line flex-1 h-0.5 mx-2 <?php echo e($step > $num ? 'bg-blue-500' : 'bg-gray-200'); ?>"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 1): ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php else: ?> <span></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="text-xs text-gray-400"><?php echo e(strlen($title)); ?>/200</span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea wire:model="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional description"></textarea>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formId): ?>
                <p class="text-sm text-gray-500 mb-6">Public URL: <a href="<?php echo e(url('/submit/'.(\App\Models\Form::find($formId)?->slug ?? ''))); ?>" class="text-blue-600" target="_blank"><?php echo e(url('/submit/'.(\App\Models\Form::find($formId)?->slug ?? ''))); ?></a></p>
            <?php else: ?>
                <p class="text-sm text-gray-500 mb-6">Public URL will be generated on save.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="border border-dashed border-purple-300 rounded-lg p-4 mb-6 bg-purple-50">
                <h3 class="font-medium text-purple-800 mb-2">✨ Generate with AI</h3>
                <textarea wire:model="aiPrompt" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mb-2"
                    placeholder="e.g., Internship application with education history, skills and resume upload"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['aiPrompt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mb-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex gap-2">
                    <button wire:click="startAiGeneration('create')" class="text-sm bg-purple-600 text-white px-4 py-1.5 rounded-lg hover:bg-purple-700">Generate Form</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formId): ?>
                        <button wire:click="startAiGeneration('edit')" class="text-sm bg-purple-100 text-purple-700 px-4 py-1.5 rounded-lg hover:bg-purple-200">AI Edit Existing</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aiJobUuid): ?>
                    <div wire:poll.2s="pollAiStatus" class="mt-2 text-sm text-purple-700">
                        Status: <?php echo e($aiStatus); ?>...
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="border border-dashed border-green-300 rounded-lg p-4 bg-green-50">
                <h3 class="font-medium text-green-800 mb-2">📄 Import from Word / Excel</h3>
                <input type="file" wire:model="importFile" accept=".docx,.xlsx" class="text-sm mb-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['importFile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mb-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="startImport" class="text-sm bg-green-600 text-white px-4 py-1.5 rounded-lg hover:bg-green-700">Upload & Parse</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($importUuid): ?>
                    <div wire:poll.2s="pollImportStatus" class="mt-2 text-sm text-green-700">
                        Import status: <?php echo e($importStatus); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $importWarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <p class="text-yellow-700">⚠ <?php echo e($w); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 2): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['schema'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="bg-red-50 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="flex gap-4">
            
            <div class="flex-1">
                <div class="flex gap-2 mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $schema['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button wire:click="$set('selectedSectionId', '<?php echo e($section['id']); ?>')"
                            class="px-3 py-1 rounded-lg text-sm <?php echo e($selectedSectionId === $section['id'] ? 'bg-blue-600 text-white' : 'bg-white border text-gray-600'); ?>">
                            <?php echo e($section['title']); ?>

                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button wire:click="addSection" class="px-3 py-1 rounded-lg text-sm border border-dashed text-gray-500 hover:border-blue-400">+ Section</button>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $schema['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($section['id'] === $selectedSectionId): ?>
                        <div class="canvas-area border-2 border-dashed border-gray-300 rounded-xl p-6 min-h-96 bg-white"
                            x-data="{ initSortable() {
                                const el = this.$refs.sortable;
                                if (el && !el._sortable) {
                                    el._sortable = Sortable.create(el, {
                                        animation: 150,
                                        handle: '.drag-handle',
                                        onEnd: (evt) => {
                                            const ids = [...el.children].map(c => c.dataset.fieldId);
                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').reorderFields('<?php echo e($section['id']); ?>', ids);
                                        }
                                    });
                                }
                            }}"
                            x-init="initSortable()">
                            <div x-ref="sortable">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $section['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div data-field-id="<?php echo e($field['id']); ?>"
                                        wire:click="selectField('<?php echo e($field['id']); ?>', '<?php echo e($section['id']); ?>')"
                                        class="field-card border rounded-lg p-4 mb-3 bg-white cursor-pointer hover:shadow-sm <?php echo e($selectedFieldId === $field['id'] ? 'selected ring-2 ring-blue-500' : ''); ?>">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center gap-2">
                                                <span class="drag-handle cursor-grab text-gray-400">⠿</span>
                                                <div>
                                                    <span class="font-medium"><?php echo e($field['label']); ?></span>
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded ml-2"><?php echo e($field['type']); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field['required']): ?> <span class="text-red-500 text-xs">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="flex gap-1">
                                                <button wire:click.stop="duplicateField('<?php echo e($section['id']); ?>', '<?php echo e($field['id']); ?>')" class="p-1 text-gray-400 hover:text-blue-600" title="Duplicate">⧉</button>
                                                <button wire:click.stop="deleteField('<?php echo e($section['id']); ?>', '<?php echo e($field['id']); ?>')" class="p-1 text-gray-400 hover:text-red-600" title="Delete">🗑</button>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($field['type'], ['text','email','number','phone','date','textarea','dropdown'])): ?>
                                            <div class="mt-2">
                                                <input disabled class="w-full border rounded px-3 py-1.5 text-sm bg-gray-50" placeholder="<?php echo e($field['placeholder'] ?: $field['label']); ?>">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <p class="text-gray-400 text-center py-12">Click a field type on the right to add fields, or drag them here.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <div class="mt-4 flex gap-2">
                    <button wire:click="toggleJsonEditor" class="text-sm text-gray-600 border px-3 py-1.5 rounded-lg hover:bg-gray-50">
                        <?php echo e($showJsonEditor ? 'Hide' : 'Show'); ?> JSON Editor
                    </button>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showJsonEditor): ?>
                    <div class="mt-4">
                        <textarea wire:model="schemaJson" rows="15" class="w-full font-mono text-xs border rounded-lg p-3"></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['schemaJson'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button wire:click="applyJsonSchema" class="mt-2 text-sm bg-gray-800 text-white px-4 py-1.5 rounded-lg">Apply JSON</button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="w-72 bg-white border rounded-xl p-4 h-fit sticky top-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedField): ?>
                    <h3 class="font-semibold text-gray-800 mb-3">Field options</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="block text-gray-600 mb-1">Label</label>
                            <input type="text" value="<?php echo e($selectedField['label']); ?>"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'label', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Key</label>
                            <input type="text" value="<?php echo e($selectedField['key']); ?>"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'key', $event.target.value)"
                                class="w-full border rounded px-2 py-1 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Placeholder</label>
                            <input type="text" value="<?php echo e($selectedField['placeholder'] ?? ''); ?>"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'placeholder', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Help text</label>
                            <input type="text" value="<?php echo e($selectedField['help_text'] ?? ''); ?>"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'help_text', $event.target.value)"
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" <?php echo e($selectedField['required'] ? 'checked' : ''); ?>

                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'required', $event.target.checked)">
                            Required
                        </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($selectedField) && \App\Services\FieldTypeRegistry::needsOptions($selectedField['type'])): ?>
                            <div>
                                <label class="block text-gray-600 mb-1">Options</label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($selectedField['options'] ?? null)): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedField['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="flex gap-1 mb-1">
                                            <input type="text" value="<?php echo e(is_array($opt) ? ($opt['label'] ?? '') : $opt); ?>" class="flex-1 border rounded px-2 py-1 text-xs"
                                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'options.<?php echo e($oi); ?>.label', $event.target.value)">
                                            <button wire:click="removeFieldOption('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', <?php echo e($oi); ?>)" class="text-red-500 text-xs">×</button>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500">No options available.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button wire:click="addFieldOption('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>')" class="text-xs text-blue-600">+ Add option</button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="border-t pt-3">
                            <p class="text-gray-600 font-medium mb-2">Validation</p>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" placeholder="Min length" value="<?php echo e($selectedField['validation']['min_length'] ?? ''); ?>"
                                    wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'validation.min_length', $event.target.value)"
                                    class="border rounded px-2 py-1 text-xs">
                                <input type="number" placeholder="Max length" value="<?php echo e($selectedField['validation']['max_length'] ?? ''); ?>"
                                    wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'validation.max_length', $event.target.value)"
                                    class="border rounded px-2 py-1 text-xs">
                            </div>
                        </div>
                        <div class="border-t pt-3">
                            <p class="text-gray-600 font-medium mb-2">Conditional logic</p>
                            <input type="text" placeholder="Show when field key" value="<?php echo e($selectedField['conditions']['show_when']['field'] ?? ''); ?>"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'conditions.show_when.field', $event.target.value)"
                                class="w-full border rounded px-2 py-1 text-xs mb-1">
                            <select class="w-full border rounded px-2 py-1 text-xs"
                                wire:change="updateFieldProperty('<?php echo e($selectedSectionId); ?>', '<?php echo e($selectedField['id']); ?>', 'conditions.show_when.operator', $event.target.value)">
                                <option value="equals" <?php echo e(($selectedField['conditions']['show_when']['operator'] ?? '') === 'equals' ? 'selected' : ''); ?>>Equals</option>
                                <option value="not_equals" <?php echo e(($selectedField['conditions']['show_when']['operator'] ?? '') === 'not_equals' ? 'selected' : ''); ?>>Not equals</option>
                                <option value="not_empty">Not empty</option>
                            </select>
                        </div>
                    </div>
                <?php else: ?>
                    <h3 class="font-semibold text-gray-500 text-xs uppercase tracking-wide mb-3">Standard Fields</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->fieldTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button wire:click="addField('<?php echo e($type); ?>')"
                                class="text-left text-xs border rounded-lg px-2 py-2 hover:bg-blue-50 hover:border-blue-300 transition">
                                <?php echo e($label); ?>

                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 3): ?>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($versions->count()): ?>
                <div class="border-t pt-4">
                    <h3 class="font-medium mb-2">Version History</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex justify-between items-center text-sm py-1">
                            <span>v<?php echo e($version->version_number); ?> — <?php echo e($version->change_note ?? 'Saved'); ?> (<?php echo e($version->created_at->diffForHumans()); ?>)</span>
                            <button wire:click="rollbackVersion(<?php echo e($version->version_number); ?>)" class="text-blue-600 text-xs">Rollback</button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 4): ?>
        <div class="bg-white rounded-xl border p-8 max-w-2xl mx-auto text-center">
            <div class="text-5xl mb-4">🎉</div>
            <h2 class="text-xl font-bold mb-2">Ready to launch!</h2>
            <p class="text-gray-500 mb-6">Your form "<?php echo e($title); ?>" has <?php echo e(collect($schema['sections'])->sum(fn($s) => count($s['fields']))); ?> fields across <?php echo e(count($schema['sections'])); ?> sections.</p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formId): ?>
                <p class="text-sm mb-4">Public URL: <a href="<?php echo e(route('forms.public', \App\Models\Form::find($formId)?->slug)); ?>" class="text-blue-600" target="_blank"><?php echo e(route('forms.public', \App\Models\Form::find($formId)?->slug)); ?></a></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex justify-center gap-3">
                <button wire:click="save(false)" class="border border-gray-300 px-6 py-2 rounded-lg hover:bg-gray-50">Save Draft</button>
                <button wire:click="save(true)" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Publish Form</button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="flex justify-between mt-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step > 1): ?>
            <button wire:click="prevStep" class="border border-gray-300 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-50">← Back</button>
        <?php else: ?>
            <a href="<?php echo e(route('forms.index')); ?>" class="border border-red-300 text-red-600 px-6 py-2 rounded-lg hover:bg-red-50">Cancel</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step < 4): ?>
            <button wire:click="nextStep" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Next: <?php echo e(['', 'Builder', 'Settings', 'Finish'][$step]); ?> →
            </button>
        <?php elseif($step === 4): ?>
            <span></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH D:\demo-pro\form-builder-laravel11\resources\views/livewire/form-wizard.blade.php ENDPATH**/ ?>