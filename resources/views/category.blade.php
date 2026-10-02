@extends('layouts.app')

@section('title', $catalog['categories'][$catKey]['name'] . ' – Free Online Tools | Azlaan Tools')
@section('meta_description'){{ $catalog['categories'][$catKey]['tagline'] }} {{ count(array_filter($catalog['tools'], function ($tool) use ($catKey) { return $tool['category'] === $catKey; })) }} free tools. All free, no signup, runs in your browser. Azlaan Tools, Pakistan.@endsection

@section('content')
@php
    $cat = $catalog['categories'][$catKey];
    $catTools = array_filter($catalog['tools'], function ($tool) use ($catKey) { return $tool['category'] === $catKey; });
    $catCount = count($catTools);
    // Phase 8: per-tool icons (fallback to category icon)
    $iconSvgs = json_decode(@file_get_contents(resource_path('data/tool-icon-svgs.json')), true) ?: [];
    $iconMap = json_decode(@file_get_contents(resource_path('data/tool-icons.json')), true) ?: [];
@endphp
<nav aria-label="breadcrumb" class="mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $cat['name'] }}</li>
    </ol>
</nav>

<h1>{{ $cat['name'] }}</h1>
<p class="lead">{{ $cat['tagline'] }} {{ $catCount }} tools. All free, no signup.</p>

<div class="row g-3 mt-1 tool-grid">
    @foreach($catTools as $slug => $tool)
    <div class="col-6 col-md-4 col-lg-3" data-name="{{ $slug }} {{ $tool['keywords'] }}">
        <div class="tool-card p-3"><a href="{{ route('tools.' . $slug) }}"><div class="icon">{!! $iconSvgs[$iconMap[$slug] ?? ''] ?? $cat['icon'] !!}</div><h3 class="h6 fw-bold mt-2">{{ $tool['name'] }}</h3><p class="small text-muted mb-0">{{ $tool['desc'] }}</p></a></div>
    </div>
    @endforeach
</div>

<section class="mt-5 mb-4">
    <h2 class="h5 fw-bold">Other categories</h2>
    <div class="mt-3">
        @foreach($catalog['categories'] as $otherKey => $otherCat)
            @php $otherCount = count(array_filter($catalog['tools'], function ($tool) use ($otherKey) { return $tool['category'] === $otherKey; })); @endphp
            @if($otherKey !== $catKey && $otherCount > 0)
            <a class="btn btn-outline-secondary btn-sm m-1" href="{{ route('category.show', $otherKey) }}">{{ $otherCat['name'] }} ({{ $otherCount }})</a>
            @endif
        @endforeach
    </div>
</section>
@endsection
