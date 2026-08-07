<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="<?php echo e(route('forms.index')); ?>" class="text-sm text-gray-500 hover:text-blue-600">← Back to forms</a>
            <h1 class="text-2xl font-bold mt-1"><?php echo e($form->title); ?> — Submissions</h1>
        </div>
        <a href="<?php echo e(route('forms.submissions.export', $form)); ?>?search=<?php echo e($search); ?>"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">Export CSV</a>
    </div>

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search submissions..."
        class="w-full max-w-md border rounded-lg px-4 py-2 mb-4">

    <div class="bg-white border rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3">ID</th>
                    <th class="text-left px-4 py-3">Submitted</th>
                    <th class="text-left px-4 py-3">IP</th>
                    <th class="text-left px-4 py-3">Data Preview</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="border-b">
                        <td class="px-4 py-3">#<?php echo e($submission->id); ?></td>
                        <td class="px-4 py-3"><?php echo e($submission->submitted_at?->format('M d, Y H:i')); ?></td>
                        <td class="px-4 py-3 text-gray-500"><?php echo e($submission->ip_address); ?></td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-gray-50 px-2 py-1 rounded"><?php echo e(\Illuminate\Support\Str::limit(json_encode($submission->data), 80)); ?></code>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No submissions yet.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($submissions->links()); ?></div>
</div>
<?php /**PATH D:\demo-pro\form-builder-laravel11\resources\views/livewire/form-submissions.blade.php ENDPATH**/ ?>