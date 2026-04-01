@php
    $socialTitle = trim($__env->yieldContent('meta_title')) ?: ($pageTitle ?? config('app.name', 'DTC Logbook'));
    $socialDescription = trim($__env->yieldContent('meta_description')) ?: 'DTC Logbook platform for managing daily time records, attendance, and activity logs.';
    $socialImage = trim($__env->yieldContent('meta_image')) ?: asset('images/header-banner.png');
    $socialUrl = trim($__env->yieldContent('meta_url')) ?: url()->current();
@endphp

<meta name="description" content="{{ $socialDescription }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name', 'DTC Logbook') }}">
<meta property="og:title" content="{{ $socialTitle }}">
<meta property="og:description" content="{{ $socialDescription }}">
<meta property="og:url" content="{{ $socialUrl }}">
<meta property="og:image" content="{{ $socialImage }}">
<meta property="og:image:alt" content="{{ $socialTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $socialTitle }}">
<meta name="twitter:description" content="{{ $socialDescription }}">
<meta name="twitter:image" content="{{ $socialImage }}">