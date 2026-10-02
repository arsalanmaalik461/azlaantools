@extends('layouts.app')

@section('title', 'Azlaan Tools – Free Online Tools for Pakistan | Bill Check, Solar, PDF, Image')
@section('meta_description', 'Free online tools for Pakistan: electricity, gas & PTCL bill check, solar calculators & daily solar rates, PDF tools, image & photo tools, video & audio tools, OCR, finance & health calculators, salary tax, prayer times, resume builder & more. No signup, 100% free.')

@section('content')
@php
    $catalog = require resource_path('catalog.php');
    $total = count($catalog['tools']);
    $catCounts = [];
    foreach ($catalog['tools'] as $toolItem) {
        $catCounts[$toolItem['category']] = ($catCounts[$toolItem['category']] ?? 0) + 1;
    }
    // Phase 8: per-tool icons (fallback to category icon)
    $iconSvgs = json_decode(@file_get_contents(resource_path('data/tool-icon-svgs.json')), true) ?: [];
    $iconMap = json_decode(@file_get_contents(resource_path('data/tool-icons.json')), true) ?: [];
    $popularSlugs = ['electricity-bill-check', 'gas-bill-check', 'ptcl-bill-check', 'solar-rates-today', 'solar-load-calculator', 'solar-price-estimator', 'electricity-bill-estimator', 'merge-pdf', 'split-pdf', 'compress-pdf', 'pdf-to-jpg', 'jpg-to-pdf', 'pdf-to-word', 'word-to-pdf', 'image-compressor', 'image-resizer', 'remove-background', 'passport-photo-maker', 'scientific-calculator', 'age-calculator', 'percentage-calculator', 'bmi-calculator', 'currency-converter', 'unit-converter', 'loan-calculator', 'emi-calculator', 'compound-interest-calculator', 'salary-tax-calculator', 'zakat-calculator', 'gold-price-calculator', 'qr-code-generator', 'word-counter', 'gpa-calculator', 'prayer-times', 'date-to-hijri-converter', 'discount-calculator', 'fuel-cost-calculator', 'resume-builder', 'video-to-gif', 'image-to-text-ocr', 'password-generator', 'typing-speed-test', 'e-challan-check', 'whatsapp-link-generator', 'youtube-thumbnail-downloader', 'electricity-units-converter', 'plot-size-calculator', 'cash-counter'];
@endphp
<section class="hero">
    <div class="hero-inner">
        <p class="hero-eyebrow">Azlaan Tools — Faisalabad, Pakistan</p>
        <h1>Everyday online work,<br><em>in one place.</em></h1>
        <p class="hero-sub">Check an electricity bill, estimate a solar setup or merge a PDF — {{ count($catalog['categories']) }} categories with {{ $total }} free tools are here. No signup, no fees, no hassle. Every tool runs in your own browser, so your files stay with you.</p>
        <div class="hero-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20.5 20.5-4.6-4.6"/></svg>
            <input type="text" id="toolSearch" class="form-control" placeholder="Search a tool… e.g. bill, pdf, solar, age" autocomplete="off">
        </div>
        <div class="hero-meta">
            <div><strong>{{ $total }}</strong><span>Free tools</span></div>
            <div><strong>{{ count($catalog['categories']) }}</strong><span>Categories</span></div>
            <div><strong>Rs 0</strong><span>No fees</span></div>
            <div><strong>100%</strong><span>Mobile friendly</span></div>
        </div>
        <div class="hero-popular">
            <span>Popular:</span>
            <a href="{{ route('tools.electricity-bill-check') }}">Electricity Bill Check</a>
            <a href="{{ route('tools.solar-rates-today') }}">Solar Rates Today</a>
            <a href="{{ route('tools.merge-pdf') }}">Merge PDF</a>
            <a href="{{ route('tools.age-calculator') }}">Age Calculator</a>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hv-card">
            <div class="hv-head"><strong>Today's Solar Rates</strong><span>Live list</span></div>
            <div class="hv-row"><span>Longi Hi-MO 7 Bifacial 610W</span><b>Rs 41.5–44.5/W</b></div>
            <div class="hv-row"><span>Longi Hi-MO X10 640W</span><b>Rs 42.2–44.5/W</b></div>
            <div class="hv-row"><span>Longi Hi-MO 7 585W</span><b>Rs 41–46/W</b></div>
            <div class="hv-foot">Full list of panels, inverters and batteries — <a href="{{ route('tools.solar-rates-today') }}">view rates</a></div>
        </div>
        <div class="hv-chips">
            <a class="hv-chip" href="{{ route('tools.electricity-bill-check') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2.5 4.8 13.5h5L9.6 21.5l8.4-11h-5.2l.2-8z"/></svg></span><span><strong>Electricity Bill Check</strong><span>All DISCOs, official</span></span></a>
            <a class="hv-chip" href="{{ route('tools.merge-pdf') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a1.5 1.5 0 0 0-1.5 1.5v15A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5V7.5L14 3z"/><path d="M14 3v4.5h4.5"/></svg></span><span><strong>Merge PDF</strong><span>iLovePDF style, free</span></span></a>
        </div>
    </div>
</section>

<section id="topToolsSection" class="mt-4">
    <h2 class="category-title">Your Top 10 Tools</h2>
    <p class="cat-sub">The tools you open most will show at the top here. <span class="text-muted small">This list is saved only in your browser. It is never sent anywhere.</span></p>
    <div class="row g-3 mt-1 tool-grid" id="topToolsGrid"></div>
    <p id="topToolsHint" class="text-muted d-none">The tool you use most will appear at the top here.</p>
    <button type="button" id="clearTopTools" class="btn btn-sm btn-outline-secondary mt-2 d-none">Clear list</button>
</section>

<p id="noResults" class="alert alert-warning d-none mt-4">No tool found. Try "bill", "pdf", "solar" or "calculator".</p>

<section id="searchResults" class="d-none mt-4">
    <h2 id="searchResultsTitle"></h2>
    <div class="row g-3 mt-1 tool-grid" id="searchGrid"></div>
</section>

<section class="featured" id="featuredStrip">
    <div class="featured-head">
        <h2>Most Used Tools</h2>
        <p>These tools get opened first every day.</p>
    </div>
    <div class="feature-grid">
        <a class="feature-tile" href="{{ route('tools.electricity-bill-check') }}">
            <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12v17.2l-2-1.4-2 1.4-2-1.4-2 1.4-2-1.4L6 20.2Z"/><path d="M9.2 8h5.6M9.2 12h5.6"/></svg></div>
            <h3>Electricity Bill Check</h3>
            <p>LESCO, IESCO, FESCO, MEPCO &amp; all DISCOs — check your electricity bill online.</p>
            <span class="go">Use tool &rarr;</span>
        </a>
        <a class="feature-tile" href="{{ route('tools.solar-rates-today') }}">
            <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3.6"/><path d="M12 3v2.2M12 18.8V21M3 12h2.2M18.8 12H21M5.6 5.6l1.6 1.6M16.8 16.8l1.6 1.6M18.4 5.6l-1.6 1.6M7.2 16.8l-1.6 1.6"/></svg></div>
            <h3>Solar Rates Today</h3>
            <p>Today's panel, inverter and battery rates — updated daily.</p>
            <span class="go">Use tool &rarr;</span>
        </a>
        <a class="feature-tile" href="{{ route('tools.merge-pdf') }}">
            <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/></svg></div>
            <h3>Merge PDF</h3>
            <p>Combine many PDFs into one file.</p>
            <span class="go">Use tool &rarr;</span>
        </a>
        <a class="feature-tile" href="{{ route('tools.scientific-calculator') }}">
            <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5.5" y="3" width="13" height="18" rx="2"/><path d="M8.7 6.8h6.6"/><circle cx="9" cy="11.5" r=".9" fill="currentColor" stroke="none"/><circle cx="12" cy="11.5" r=".9" fill="currentColor" stroke="none"/><circle cx="15" cy="11.5" r=".9" fill="currentColor" stroke="none"/><circle cx="9" cy="15" r=".9" fill="currentColor" stroke="none"/><circle cx="12" cy="15" r=".9" fill="currentColor" stroke="none"/><circle cx="15" cy="15" r=".9" fill="currentColor" stroke="none"/></svg></div>
            <h3>Scientific Calculator</h3>
            <p>Full scientific calculator with trigonometry, log, powers and brackets.</p>
            <span class="go">Use tool &rarr;</span>
        </a>
        <a class="feature-tile" href="{{ route('government-schemes') }}">
            <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21v-8M19 21v-8"/><path d="M3 13l9-8 9 8Z"/><path d="M9.5 21v-5M12 21v-5M14.5 21v-5"/></svg></div>
            <h3>Government Schemes</h3>
            <p>BISP, Ehsaas, Sehat Card and CM Punjab schemes — benefits, eligibility and how to apply, verified from official sources only.</p>
            <span class="go">View schemes &rarr;</span>
        </a>
    </div>
</section>

{{-- ================= CATEGORIES ================= --}}
<section id="categories">
    <h2 class="category-title">All Categories</h2>
    <p class="cat-sub">Pick the category you need — all free tools of every category on one page.</p>
    <div class="cat-grid">
        @foreach($catalog['categories'] as $catKey => $cat)
            @if(($catCounts[$catKey] ?? 0) > 0)
            <a class="cat-tile" href="{{ route('category.show', $catKey) }}">
                <div class="icon">{!! $cat['icon'] !!}</div>
                <h3>{{ $cat['name'] }}</h3>
                <span class="cat-tile-count">{{ $catCounts[$catKey] }} tools</span>
                <p>{{ $cat['tagline'] }}</p>
            </a>
            @endif
        @endforeach
    </div>
</section>

{{-- ================= POPULAR ================= --}}
<section id="popularSection">
    <h2 class="category-title">Popular Tools</h2>
    <p class="cat-sub">The most used tools — find all other tools in the categories above.</p>
    <div class="row g-3 mt-1 tool-grid">
        @foreach($popularSlugs as $popularSlug)
            @if(isset($catalog['tools'][$popularSlug]))
                @php $popularTool = $catalog['tools'][$popularSlug]; @endphp
                <div class="col-6 col-md-4 col-lg-3" data-name="{{ $popularSlug }} {{ $popularTool['keywords'] }}">
                    <div class="tool-card p-3"><a href="{{ route('tools.' . $popularSlug) }}"><div class="icon">{!! $iconSvgs[$iconMap[$popularSlug] ?? ''] ?? $catalog['categories'][$popularTool['category']]['icon'] !!}</div><h3 class="h6 fw-bold mt-2">{{ $popularTool['name'] }}</h3><p class="small text-muted mb-0">{{ $popularTool['desc'] }}</p></a></div>
                </div>
            @endif
        @endforeach
    </div>
</section>

<div class="card mt-5 border-0 shadow-sm">
    <div class="card-body p-4">
        <h2 class="h4 fw-bold">Why Azlaan Tools?</h2>
        <p class="mb-2">All these tools are <strong>completely free</strong> — no signup, no file uploads. Every tool runs in your own phone or computer's browser, so your files and data stay fully private. For bill checks we send you straight to the company's official website, so you get your real, verified bill.</p>
        <p class="mb-0">Need solar installed, or CCTV, AC or fridge repair — <strong>Azlaan Electric AC Solar Center, Faisalabad</strong> is one call away: <a href="tel:+923008987448">0300-8987448</a> (Malik Arslan) · <a href="tel:+923146332385">0314-6332385</a> (Malik Rehan).</p>
    </div>
</div>
{{-- ================= ABOUT ================= --}}
<section class="about-strip">
    <div>
        <h2>Who owns this website?</h2>
        <p>Azlaan Tools is from the <strong>Azlaan Electric AC Solar Center</strong> of Faisalabad — the same team that installs solar systems, CCTV and AC units in your homes and shops. We made these {{ $total }} tools for the everyday work of our customers and the public. If any tool does not work or you spot a mistake, tell us on WhatsApp — we will fix it.</p>
    </div>
    <div class="about-facts">
        <div><strong>Need solar installed?</strong><span>Free site survey and advice — call or WhatsApp 0300-8987448.</span></div>
        <div><strong>CCTV, AC and electrical work</strong><span>Installation and repair service available in Faisalabad.</span></div>
        <div><strong>Want a new tool?</strong><span>Tell us on WhatsApp which tool you need — we will try to build it.</span></div>
    </div>
</section>

{{-- ================= FAQ ================= --}}
<h2 class="category-title mt-5" id="faq">Frequently Asked Questions (FAQ)</h2>
<div class="row g-3 mt-1 mb-2">
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">How do I check my electricity bill online?</h3>
            <p class="small text-muted mb-0">In our <a href="{{ route('tools.electricity-bill-check') }}">Electricity Bill Check</a> tool, select your company (LESCO, IESCO, FESCO, MEPCO, GEPCO, PESCO, HESCO, SEPCO, QESCO or TESCO) and enter the 14-digit reference number printed on your bill — your duplicate bill will open on the official portal. Completely free, no signup.</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">How do I check my gas bill (SNGPL / SSGC) online?</h3>
            <p class="small text-muted mb-0">Use the <a href="{{ route('tools.gas-bill-check') }}">Gas Bill Check</a> tool to check your SNGPL or SSGC duplicate bill instantly with your consumer number. For the PTCL bill, use the <a href="{{ route('tools.ptcl-bill-check') }}">PTCL Bill Check</a> tool.</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">How do I estimate the cost of a solar system?</h3>
            <p class="small text-muted mb-0">First use the <a href="{{ route('tools.solar-load-calculator') }}">Solar Load Calculator</a> to find the right system size (kW) for your home load, then use the <a href="{{ route('tools.solar-price-estimator') }}">Solar Price Estimator</a> for the total cost. Today's panel, inverter and battery rates update daily on <a href="{{ route('tools.solar-rates-today') }}">Solar Rates Today</a>.</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">Is Azlaan Tools really free?</h3>
            <p class="small text-muted mb-0">Yes — all {{ $total }} tools are 100% free. No signup, no account, no hidden charges. Just open a tool and use it.</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">Are my files or data uploaded to a server?</h3>
            <p class="small text-muted mb-0">No. All tools run in your own browser — PDFs, photos and other files never leave your device. Your privacy stays safe.</p>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <h3 class="h6 fw-bold">How do I merge, compress or convert a PDF?</h3>
            <p class="small text-muted mb-0">Use <a href="{{ route('tools.merge-pdf') }}">Merge PDF</a> to combine many files into one, <a href="{{ route('tools.compress-pdf') }}">Compress PDF</a> to reduce size, and converters like PDF to JPG and JPG to PDF are also available — all free and with no watermark.</p>
        </div></div>
    </div>
</div>
@endsection

@section('schema')
@php $schemaCatalog = require resource_path('catalog.php'); $schemaTotal = count($schemaCatalog['tools']); @endphp
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[
{"@type":"Question","name":"How can I check my electricity bill online in Pakistan?","acceptedAnswer":{"@type":"Answer","text":"Open the Electricity Bill Check tool on Azlaan Tools, select your company (LESCO, IESCO, FESCO, MEPCO, GEPCO, PESCO, HESCO, SEPCO, QESCO or TESCO) and enter the 14-digit reference number printed on your bill. Your duplicate bill opens on the official company portal. It is free and needs no signup."}},
{"@type":"Question","name":"How can I check my SNGPL or SSGC gas bill online?","acceptedAnswer":{"@type":"Answer","text":"Use the Gas Bill Check tool on Azlaan Tools and enter your consumer number to view your SNGPL or SSGC duplicate gas bill online for free."}},
{"@type":"Question","name":"How can I estimate the cost of a solar system for my home?","acceptedAnswer":{"@type":"Answer","text":"Use the Solar Load Calculator to find the right system size in kW for your home load, then the Solar Price Estimator for the total cost. Daily updated panel, inverter and battery prices are listed on the Solar Rates Today page."}},
{"@type":"Question","name":"Is Azlaan Tools really free?","acceptedAnswer":{"@type":"Answer","text":"Yes. All {{ $schemaTotal }} tools on Azlaan Tools are 100% free with no signup, no account and no hidden charges."}},
{"@type":"Question","name":"Are my files uploaded to a server?","acceptedAnswer":{"@type":"Answer","text":"No. All tools run locally in your browser, so your PDF files, photos and data never leave your device."}},
{"@type":"Question","name":"How can I merge or compress a PDF for free?","acceptedAnswer":{"@type":"Answer","text":"Use the Merge PDF tool to combine files into one PDF and the Compress PDF tool to reduce file size. Both are free and add no watermark."}}
]}
</script>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('toolSearch');
    if (!input) { return; }
    var searchResults = document.getElementById('searchResults');
    var searchGrid = document.getElementById('searchGrid');
    var searchTitle = document.getElementById('searchResultsTitle');
    var noResults = document.getElementById('noResults');
    var indexPromise = null;
    var debounceTimer = null;

    function loadIndex() {
        if (!indexPromise) {
            indexPromise = fetch('{{ asset('tools-index.json') }}')
                .then(function (r) { return r.json(); })
                .catch(function () { return []; });
        }
        return indexPromise;
    }

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function toggleBrowsingSections(hide) {
        ['categories', 'categoriesSection', 'popularSection', 'featuredStrip', 'topToolsSection'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.classList.toggle('d-none', hide); }
        });
    }

    function render(q) {
        if (!q) {
            searchResults.classList.add('d-none');
            searchGrid.innerHTML = '';
            noResults.classList.add('d-none');
            toggleBrowsingSections(false);
            return;
        }
        loadIndex().then(function (index) {
            if (input.value.trim().toLowerCase() !== q) { return; }
            var tokens = q.split(/\s+/).filter(Boolean);
            var results = index.filter(function (tool) {
                var hay = (tool.name + ' ' + tool.keywords + ' ' + tool.category + ' ' + tool.slug + ' ' + tool.slug.replace(/-/g, ' ')).toLowerCase();
                return tokens.every(function (token) { return hay.indexOf(token) !== -1; });
            }).slice(0, 60);

            toggleBrowsingSections(true);
            if (results.length === 0) {
                searchResults.classList.add('d-none');
                searchGrid.innerHTML = '';
                noResults.classList.remove('d-none');
                return;
            }
            noResults.classList.add('d-none');
            searchTitle.textContent = results.length + ' results for "' + q + '"';
            searchGrid.innerHTML = results.map(function (tool) {
                return '<div class="col-6 col-md-4 col-lg-3" data-name="' + esc(tool.slug) + '">'
                    + '<div class="tool-card p-3"><a href="/tools/' + esc(tool.slug) + '">'
                    + '<div class="icon">' + tool.icon + '</div>'
                    + '<h3 class="h6 fw-bold mt-2">' + esc(tool.name) + '</h3>'
                    + '<p class="small text-muted mb-0">' + esc(tool.desc) + '</p>'
                    + '</a></div></div>';
            }).join('');
            searchResults.classList.remove('d-none');
        });
    }

    input.addEventListener('focus', function () { loadIndex(); });
    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        if (q) { loadIndex(); }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { render(q); }, 120);
    });
})();
</script>
@endsection
