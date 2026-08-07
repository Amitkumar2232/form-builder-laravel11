<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($form->title ?? 'Form'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
        <?php echo e($slot); ?>

    </div>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH D:\demo-pro\form-builder-laravel11\resources\views/layouts/public.blade.php ENDPATH**/ ?>