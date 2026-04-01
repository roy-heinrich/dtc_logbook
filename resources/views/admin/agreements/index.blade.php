@extends('admin.layouts.app')

@php
    $pageTitle = 'Privacy Policy & Terms of Service';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Privacy Policy & Terms of Service</h1>
    </div>

    <div class="rounded-xl glass-card p-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Manage Legal Information</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update the privacy policy and terms of service that users will see.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700/60 dark:bg-red-900/30 dark:text-red-100">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.agreements.update') }}" method="POST" class="mt-6 space-y-6" id="agreements-form">
            @csrf

            <!-- Privacy Policy Section -->
            <div>
                <label for="privacy_info" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                    Privacy Policy
                </label>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Use the editor below to apply formatting. Content is sanitized automatically when saved.
                </p>
                <input
                    id="privacy_info"
                    type="hidden"
                    name="privacy_info"
                    value="{{ old('privacy_info', $agreement->privacy_info) }}"
                >
                <trix-editor
                    input="privacy_info"
                    class="mt-3 trix-content min-h-64 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                ></trix-editor>
            </div>

            <!-- Terms of Service Section -->
            <div>
                <label for="tos_info" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                    Terms of Service
                </label>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Use the editor below to apply formatting. Content is sanitized automatically when saved.
                </p>
                <input
                    id="tos_info"
                    type="hidden"
                    name="tos_info"
                    value="{{ old('tos_info', $agreement->tos_info) }}"
                >
                <trix-editor
                    input="tos_info"
                    class="mt-3 trix-content min-h-64 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                ></trix-editor>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    @if($agreement->exists)
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Privacy Policy Preview -->
            @if($agreement->privacy_info)
                <div class="rounded-xl glass-card p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Privacy Policy Preview</h3>
                    <div class="legal-doc-content prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
                        {!! $agreement->privacy_info !!}
                    </div>
                </div>
            @endif

            <!-- Terms of Service Preview -->
            @if($agreement->tos_info)
                <div class="rounded-xl glass-card p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Terms of Service Preview</h3>
                    <div class="legal-doc-content prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
                        {!! $agreement->tos_info !!}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="preconnect" href="https://unpkg.com" crossorigin>
<link rel="preload" as="style" href="https://unpkg.com/trix@2.1.15/dist/trix.css" onload="this.onload=null;this.rel='stylesheet'">
<noscript>
    <link rel="stylesheet" href="https://unpkg.com/trix@2.1.15/dist/trix.css">
</noscript>
<style>
    trix-toolbar .trix-button-group--file-tools {
        display: none;
    }

    trix-editor {
        min-height: 14rem;
    }

    trix-editor p,
    .legal-doc-content p {
        text-align: justify;
        text-justify: inter-word;
    }

    trix-toolbar .trix-button-group {
        border-color: rgb(226 232 240);
        background-color: rgb(248 250 252);
    }

    trix-toolbar .trix-button {
        background: transparent;
        border-bottom: 0;
        color: rgb(51 65 85);
    }

    trix-toolbar .trix-button:hover {
        background-color: rgb(241 245 249);
    }

    .dark trix-toolbar .trix-button-group {
        border-color: rgb(51 65 85);
        background-color: rgb(15 23 42);
    }

    .dark trix-toolbar .trix-button {
        color: rgb(226 232 240);
    }

    .dark trix-toolbar .trix-button:hover,
    .dark trix-toolbar .trix-button.trix-active {
        background-color: rgb(30 41 59);
    }

    .dark trix-toolbar .trix-button::before {
        filter: brightness(0) invert(1);
    }

    .dark trix-toolbar .trix-dialog {
        background-color: rgb(15 23 42);
        border-color: rgb(51 65 85);
        color: rgb(226 232 240);
    }

    .dark trix-toolbar .trix-dialog input {
        background-color: rgb(2 6 23);
        border-color: rgb(51 65 85);
        color: rgb(226 232 240);
    }
</style>
@endpush

@push('scripts')
<script defer src="https://unpkg.com/trix@2.1.15/dist/trix.umd.min.js"></script>
<script>
    document.addEventListener('trix-file-accept', (event) => {
        event.preventDefault();
    });

    document.addEventListener('trix-attachment-add', (event) => {
        if (event.attachment) {
            event.attachment.remove();
        }
    });

    // Handle Tab key in Trix editors - use capture phase to intercept before Trix
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }

        // Check if we're inside a trix-editor
        const trixEditor = event.target.closest('trix-editor');
        if (!trixEditor) {
            return;
        }

        event.preventDefault();
        const editor = trixEditor.editor;

        if (!editor) {
            return;
        }

        if (event.shiftKey) {
            editor.decreaseNestingLevel();
        } else {
            const didNest = editor.increaseNestingLevel();
            if (!didNest) {
                editor.insertString('    ');
            }
        }
    }, true); // Capture phase to intercept Tab before Trix
</script>
@endpush
