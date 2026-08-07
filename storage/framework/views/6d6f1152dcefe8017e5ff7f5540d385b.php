<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Forms</h1>
        <a href="<?php echo e(route('forms.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ New Form</a>
    </div>

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search forms..."
        class="w-full max-w-md border rounded-lg px-4 py-2 mb-6">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templates->count()): ?>
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-3">Template Library</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(route('forms.create', ['templateId' => $template->id])); ?>"
                        class="bg-white border rounded-xl p-4 hover:shadow-md transition">
                        <h3 class="font-medium"><?php echo e($template->title); ?></h3>
                        <p class="text-sm text-gray-500 mt-1"><?php echo e($template->template_category); ?></p>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Title</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Submissions</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Updated</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"><?php echo e($form->title); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                <?php echo e($form->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e(ucfirst($form->status)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3"><?php echo e($form->submissions_count ?? $form->submissions()->count()); ?></td>
                        <td class="px-4 py-3 text-gray-500"><?php echo e($form->updated_at->diffForHumans()); ?></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="<?php echo e(route('forms.edit', $form)); ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="<?php echo e(route('forms.submissions', $form)); ?>" class="text-gray-600 hover:underline">Submissions</a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->isPublished()): ?>
                                <a href="<?php echo e(route('forms.public', $form->slug)); ?>" target="_blank" class="text-green-600 hover:underline">Public</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <button wire:click="deleteForm(<?php echo e($form->id); ?>)" wire:confirm="Delete this form?"
                                class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No forms yet. Create your first form!</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($forms->links()); ?></div>
</div>
<?php /**PATH D:\demo-pro\form-builder-laravel11\resources\views/livewire/form-index.blade.php ENDPATH**/ ?>