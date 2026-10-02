<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XJZ5WSTH4S"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XJZ5WSTH4S');
      gtag('config', 'GT-WF83PS2Z');
    </script>
    <meta name="keywords" content="online tools pakistan, electricity bill check, lesco bill, gepco bill, fesco bill, mepco bill, iesco bill, solar calculator pakistan, free pdf tools, compress pdf, merge pdf, image compressor, image to text ocr, salary tax calculator, zakat calculator, azlaan tools, arslanmalik.tech">
    <meta name="msvalidate.01" content="AA7769956FE2677995F020CB657B667F">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $seoTitle = trim($__env->yieldContent('title', 'Azlaan Tools – Free Online Tools for Pakistan'));
        $seoDesc = trim($__env->yieldContent('meta_description', 'Free online tools for Pakistan: check electricity, gas and PTCL bills, solar calculators, PDF tools, image tools and daily utilities. No signup, 100% free.'));
        $seoUrl = url()->current();
        $seoImage = asset('images/og-image.png');
        $isHome = request()->routeIs('home');
        $org = [
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => 'Azlaan Tools',
            'url' => url('/'),
            'logo' => $seoImage,
            'description' => 'Free online tools for Pakistan — bill check, solar calculators, PDF, image and daily utility tools.',
            'telephone' => '+92-300-8987448',
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Faisalabad', 'addressRegion' => 'Punjab', 'addressCountry' => 'PK'],
            'contactPoint' => ['@type' => 'ContactPoint', 'telephone' => '+92-300-8987448', 'contactType' => 'customer service', 'areaServed' => 'PK', 'availableLanguage' => ['en', 'ur']],
        ];
        $graph = [$org, ['@type' => 'WebSite', '@id' => url('/') . '#website', 'name' => 'Azlaan Tools', 'url' => url('/'), 'publisher' => ['@id' => url('/') . '#organization'], 'inLanguage' => 'en-PK']];
        if (!$isHome) {
            $toolName = trim(str_replace(['| Azlaan Tools', '– Azlaan Tools', '- Azlaan Tools'], '', $seoTitle));
            $graph[] = ['@type' => 'WebApplication', 'name' => $toolName, 'url' => $seoUrl, 'description' => $seoDesc, 'applicationCategory' => 'UtilitiesApplication', 'operatingSystem' => 'Any', 'browserRequirements' => 'Requires JavaScript', 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'PKR'], 'provider' => ['@id' => url('/') . '#organization'], 'inLanguage' => 'en-PK'];
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $toolName, 'item' => $seoUrl],
            ]];
        }
        $schemaJson = json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="author" content="Azlaan Tools">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Azlaan Tools">
    <meta property="og:locale" content="en_PK">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Azlaan Tools — Free Online Tools for Pakistan">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <script type="application/ld+json">{!! $schemaJson !!}</script>
    @yield('schema')
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%230b3d2e'/%3E%3Cpath d='M35 8 L18 36 h11 L26 56 L46 27 H34 Z' fill='%23f5b301'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=9" rel="stylesheet">
    @yield('styles')
</head>
<body>
<div class="topbar">
    <div class="container">
        <span class="topbar-msg">Azlaan Electric AC Solar Center — Solar, CCTV, AC &amp; electrical work, Faisalabad</span>
        <span>Call: <a href="tel:+923008987448">0300-8987448</a></span>
    </div>
</div>
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="brand" href="{{ route('home') }}" aria-label="Azlaan Tools — home">
                <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 2 4.8 13.6h5.7L9.2 22 19.5 10.2h-6L13 2Z" fill="#f5b301"/></svg></span>
                <span class="brand-name">Azlaan <em>Tools</em><span class="brand-tag">Free tools for Pakistan</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'bills') }}">Bill Check</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'solar') }}">Solar</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'pdf') }}">PDF</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'image') }}">Image</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'calculators') }}">Calculators</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('category.show', 'finance') }}">Finance</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#categories">All Tools</a></li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="nav-link btn-whatsapp" href="https://wa.me/923008987448" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.9-4.45 9.9-9.91C21.94 6.45 17.5 2 12.04 2Zm0 18.03a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-3.12.82.83-3.04-.2-.31a8.08 8.08 0 0 1-1.24-4.28c0-4.47 3.64-8.11 8.16-8.11 4.47 0 8.11 3.64 8.11 8.11 0 4.47-3.64 8.12-8.11 8.12Zm4.45-6.08c-.24-.12-1.44-.71-1.66-.79-.22-.08-.39-.12-.55.12-.16.25-.63.79-.77.97-.14.18-.29.2-.53.08-.24-.12-1.03-.38-1.96-1.21-.72-.65-1.21-1.44-1.35-1.68-.14-.25-.02-.38.11-.51.11-.11.24-.29.37-.43.12-.14.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.43.06-.67.31-.24.25-.93.9-.93 2.2 0 1.29.94 2.53 1.07 2.71.12.18 1.85 2.82 4.48 3.96.63.27 1.12.43 1.5.56.63.2 1.2.17 1.66.1.5-.08 1.55-.63 1.89-1.29.35-.65.35-1.21.24-1.32-.1-.12-.22-.18-.47-.3Z"/></svg>WhatsApp</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="footer-brand">
                    <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 2 4.8 13.6h5.7L9.2 22 19.5 10.2h-6L13 2Z" fill="#f5b301"/></svg></span>
                    <h5 class="mb-0">Azlaan Tools</h5>
                </div>
                <p>Free online tools for Pakistan — bill check, solar calculators, PDF and image tools, and daily-use utilities. No signup, no fees.</p>
                <p class="mb-0">Every tool runs in your browser. Your files and data never leave your device.</p>
            </div>
            <div class="col-md-3">
                <h6>Popular Tools</h6>
                <ul class="footer-links mt-3">
                    <li><a href="{{ route('tools.electricity-bill-check') }}">Electricity Bill Check</a></li>
                    <li><a href="{{ route('tools.gas-bill-check') }}">Gas Bill Check</a></li>
                    <li><a href="{{ route('tools.solar-rates-today') }}">Solar Rates Today</a></li>
                    <li><a href="{{ route('tools.solar-load-calculator') }}">Solar Load Calculator</a></li>
                    <li><a href="{{ route('tools.merge-pdf') }}">Merge PDF</a></li>
                    <li><a href="{{ route('tools.image-compressor') }}">Image Compressor</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact — Azlaan Electric AC Solar Center</h6>
                <div class="mt-3">
                    <p class="contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-5.6-7-11a7 7 0 0 1 14 0c0 5.4-7 11-7 11Z"/><circle cx="12" cy="10" r="2.6"/></svg><span>Faisalabad, Punjab, Pakistan</span></p>
                    <p class="contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a12 12 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg><span>Malik Arslan: <a href="tel:+923008987448" class="footer-links-a">0300-8987448</a></span></p>
                    <p class="contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a12 12 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg><span>Malik Rehan: <a href="tel:+923146332385" class="footer-links-a">0314-6332385</a></span></p>
                    <p class="contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 12a9 9 0 1 1-4.4-7.7L21 3l-1.2 4.3c.8 1.4 1.2 3 1.2 4.7Z"/><path d="M8.5 10.8c.6 2.5 3.2 5.1 5.7 5.7l1.8-1.8 3.2 1.6c-.3 1.9-1 2.7-2.9 2.7C10.9 19 5 13.1 5 7.7c0-1.9.8-2.6 2.7-2.9l1.6 3.2-1.8 1.8Z" fill="currentColor" stroke="none" opacity=".0"/></svg><span>WhatsApp: <a href="https://wa.me/923008987448" target="_blank" rel="noopener" class="footer-links-a">0300-8987448</a></span></p>
                </div>
                <p class="mt-3 mb-0">Solar installation &amp; maintenance, CCTV, AC / fridge repair and all electrical work.</p>
            </div>
        </div>
        <hr>
        <p class="footer-bottom text-center mb-0">© {{ date('Y') }} Azlaan Tools · Azlaan Electric AC Solar Center, Faisalabad · Made in Pakistan</p>
    </div>
</footer>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/tool-usage.js') }}"></script>
@yield('scripts')
</body>
</html>
