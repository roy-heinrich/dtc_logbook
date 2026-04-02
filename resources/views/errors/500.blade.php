<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-900">
    @php
        $error = $exception ?? null;
    @endphp

    <div class="min-h-screen px-4 py-6 sm:py-10">
        <div class="mx-auto max-w-5xl rounded-lg border border-red-200 bg-white p-4 sm:p-6 shadow">
            <h1 class="text-2xl font-bold text-red-700">Application Error</h1>

            @if ($error)
                <div class="mt-4 space-y-3 text-sm">
                    <div><span class="font-semibold">Exception:</span> {{ get_class($error) }}</div>
                    <div><span class="font-semibold">Message:</span> {{ $error->getMessage() }}</div>
                    <div><span class="font-semibold">File:</span> {{ $error->getFile() }}</div>
                    <div><span class="font-semibold">Line:</span> {{ $error->getLine() }}</div>
                </div>

                <h2 class="mt-6 text-lg font-semibold">Stack Trace</h2>
                <pre class="mt-2 overflow-x-auto rounded bg-gray-900 p-4 text-xs text-gray-100">{{ $error->getTraceAsString() }}</pre>
            @else
                <p class="mt-4 text-sm text-gray-700">No exception details were provided to the error view.</p>
            @endif
        </div>
    </div>
</body>
</html>
