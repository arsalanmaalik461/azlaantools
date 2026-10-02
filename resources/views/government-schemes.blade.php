@extends('layouts.app')

@section('title', 'Government Schemes Pakistan - BISP, Ehsaas, CM Punjab Schemes | Azlaan Tools')
@section('meta_description', 'All major government schemes of Pakistan in one place: BISP, Ehsaas, Sehat Card, CM Punjab schemes, youth loans and scholarships - benefits, eligibility, how to apply and official links. Information verified only from official sources.')

@section('content')
@php
    $schemesPath = resource_path('data/gov-schemes.json');
    $schemes = file_exists($schemesPath) ? json_decode(file_get_contents($schemesPath), true) : null;
    $catNames = [
        'cash-support' => 'Cash Support',
        'education' => 'Education',
        'health' => 'Health',
        'jobs-loans' => 'Jobs & Loans',
        'youth' => 'Youth',
        'housing' => 'Housing',
        'energy' => 'Energy',
        'agriculture' => 'Agriculture',
        'transport' => 'Transport',
        'other' => 'Other',
    ];
    $statusMap = [
        'open' => ['Open', 'bg-success'],
        'closed' => ['Closed', 'bg-secondary'],
        'unclear-from-source' => ['Source-unverified', 'bg-warning text-dark'],
    ];
    $total = is_array($schemes) ? count($schemes) : 0;
    $openCount = 0;
    $lastVerified = null;
    if (is_array($schemes)) {
        foreach ($schemes as $sc) {
            if (($sc['status'] ?? '') === 'open') { $openCount++; }
            if (!empty($sc['last_verified']) && ($lastVerified === null || $sc['last_verified'] > $lastVerified)) { $lastVerified = $sc['last_verified']; }
        }
    }
@endphp
<div class="container py-4">
    @if(!is_array($schemes))
        <div class="alert alert-warning" role="alert">
            Scheme information is being updated — please check again in a while.
        </div>
    @else
        <div class="text-center mb-4">
            <h1 class="mb-3">Government Schemes - Pakistan</h1>
            <p class="lead">All major schemes of the federal and provincial governments in one place — BISP, Ehsaas, Sehat Card, CM Punjab schemes, youth loans and scholarships.</p>
            <span class="badge bg-success fs-6 px-3 py-2">{{ $total }} schemes</span>
            <span class="badge bg-primary fs-6 px-3 py-2">{{ $openCount }} open now</span>
            @if($lastVerified)
                <span class="badge bg-dark fs-6 px-3 py-2">Last verified: {{ $lastVerified }}</span>
            @endif
        </div>

        <div class="alert alert-warning" role="alert">
            <strong>Note:</strong> This information is taken from official government sources only. Always confirm on the official website before applying. Never pay any fee to an agent — applying to real schemes is usually completely free.
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label for="schemeSearch" class="form-label fw-bold">Search schemes</label>
                <input type="text" class="form-control form-control-lg" id="schemeSearch" placeholder="e.g. BISP, Sehat Card, Laptop, Kisan, Loan...">
                <div class="mt-3 d-flex flex-wrap gap-2" id="schemeChips">
                    <button type="button" class="btn btn-success btn-sm scheme-chip active" data-province="all">All</button>
                    <button type="button" class="btn btn-outline-success btn-sm scheme-chip" data-province="federal">Federal</button>
                    <button type="button" class="btn btn-outline-success btn-sm scheme-chip" data-province="Punjab">Punjab</button>
                    <button type="button" class="btn btn-outline-success btn-sm scheme-chip" data-province="Sindh">Sindh</button>
                    <button type="button" class="btn btn-outline-success btn-sm scheme-chip" data-province="Khyber Pakhtunkhwa">KP</button>
                    <button type="button" class="btn btn-outline-success btn-sm scheme-chip" data-province="Balochistan">Balochistan</button>
                </div>
                <div class="form-text mt-2"><span id="schemeCount">{{ $total }}</span> schemes showing.</div>
            </div>
        </div>

        <div class="row g-3" id="schemeGrid">
            @foreach($schemes as $s)
                @php
                    $provKey = ($s['level'] ?? '') === 'federal' ? 'federal' : ($s['province'] ?? '');
                    $provLabel = ($s['level'] ?? '') === 'federal' ? 'Federal' : ($s['province'] ?? '');
                    $st = $statusMap[$s['status'] ?? ''] ?? ['Unknown', 'bg-secondary'];
                    $searchText = strtolower(($s['name'] ?? '') . ' ' . ($catNames[$s['category'] ?? ''] ?? '') . ' ' . $provLabel);
                @endphp
                <div class="col-md-6 col-lg-4 scheme-card" data-province="{{ $provKey }}" data-search="{{ $searchText }}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h3 class="h5 fw-bold">{{ $s['name'] }}</h3>
                            <div class="mb-2">
                                <span class="badge bg-primary">{{ $provLabel }}</span>
                                <span class="badge bg-info text-dark">{{ $catNames[$s['category'] ?? ''] ?? 'Other' }}</span>
                                <span class="badge {{ $st[1] }}">{{ $st[0] }}</span>
                            </div>
                            @if(!empty($s['benefits']))
                                <p class="small mb-2"><strong>Benefit:</strong> {{ $s['benefits'] }}</p>
                            @endif
                            @if(!empty($s['eligibility']))
                                <p class="small mb-2"><strong>Who can apply:</strong> {{ $s['eligibility'] }}</p>
                            @endif
                            @if(!empty($s['how_to_apply']))
                                <p class="small mb-2"><strong>How to apply:</strong> {{ $s['how_to_apply'] }}</p>
                            @endif
                            @if(!empty($s['deadline']))
                                <p class="small mb-2 text-warning"><strong>Last date:</strong> {{ $s['deadline'] }}</p>
                            @endif
                            <div class="mt-auto pt-2">
                                @if(!empty($s['official_url']))
                                    <a href="{{ $s['official_url'] }}" target="_blank" rel="noopener" class="btn btn-success btn-sm">Official Website</a>
                                @endif
                                @if(!empty($s['source_url']))
                                    <a href="{{ $s['source_url'] }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">Source</a>
                                @endif
                                @if(!empty($s['last_verified']))
                                    <div class="small text-muted mt-2">Verified: {{ $s['last_verified'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="alert alert-secondary mt-4 d-none" id="schemeEmpty" role="alert">
            No scheme found for this search or filter. Try something else.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
    var searchInput = document.getElementById('schemeSearch');
    var chips = document.querySelectorAll('.scheme-chip');
    var cards = document.querySelectorAll('.scheme-card');
    var countEl = document.getElementById('schemeCount');
    var emptyEl = document.getElementById('schemeEmpty');
    if (!searchInput || !cards.length) { return; }
    var activeProvince = 'all';
    function applyFilters() {
        var q = searchInput.value.trim().toLowerCase();
        var shown = 0;
        cards.forEach(function (card) {
            var provOk = activeProvince === 'all' || card.getAttribute('data-province') === activeProvince;
            var searchOk = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            var show = provOk && searchOk;
            card.classList.toggle('d-none', !show);
            if (show) { shown++; }
        });
        if (countEl) { countEl.textContent = shown; }
        if (emptyEl) { emptyEl.classList.toggle('d-none', shown !== 0); }
    }
    searchInput.addEventListener('input', applyFilters);
    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) { c.classList.remove('active', 'btn-success'); c.classList.add('btn-outline-success'); });
            chip.classList.add('active', 'btn-success');
            chip.classList.remove('btn-outline-success');
            activeProvince = chip.getAttribute('data-province');
            applyFilters();
        });
    });
})();
</script>
@endsection
