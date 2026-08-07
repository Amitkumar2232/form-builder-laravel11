<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Form Builder'); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        [x-cloak] { display: none !important; }
        .step-line { @apply flex-1 h-0.5 bg-gray-200 mx-2; }
        .step-line.active { @apply bg-blue-500; }
        .field-card.selected { @apply ring-2 ring-blue-500 border-blue-500; }
        .canvas-area { background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px); background-size: 20px 20px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <a href="<?php echo e(route('forms.index')); ?>" class="text-lg font-bold text-gray-900">Form Builder</a>
        <div class="flex gap-4 text-sm">
            <a href="<?php echo e(route('forms.index')); ?>" class="text-gray-600 hover:text-blue-600">My Forms</a>
            <a href="<?php echo e(route('forms.create')); ?>" class="text-blue-600 font-medium">+ New Form</a>
        </div>
    </nav>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('message')): ?>
        <div class="max-w-6xl mx-auto mt-4 px-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <?php echo e(session('message')); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <main class="max-w-6xl mx-auto px-4 py-6">
        <?php echo e($slot); ?>

    </main>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH D:\demo-pro\form-builder-laravel11\resources\views/layouts/app.blade.php ENDPATH**/ ?>