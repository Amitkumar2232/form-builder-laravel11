<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <style>body{margin:0;font-family:system-ui,sans-serif}</style>
</head>
<body>
    <iframe src="{{ route('forms.public', $form->slug) }}" width="100%" height="600" frameborder="0" style="border:none;"></iframe>
</body>
</html>
