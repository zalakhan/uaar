<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview — {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container" style="max-width: 640px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small mb-1">Form preview ({{ $category?->name ?? 'No category' }})</p>
                <h1 class="h4">{{ $event->title }}</h1>
                @include('partials.event-registration-fields', ['resolvedFields' => $resolvedFields, 'readOnly' => true])
            </div>
        </div>
    </div>
</body>
</html>
