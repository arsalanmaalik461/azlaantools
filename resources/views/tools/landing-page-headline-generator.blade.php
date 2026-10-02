@extends('layouts.app')

@section('title', 'Landing Page Headline Generator - Azlaan Tools')
@section('meta_description', 'Generate high-converting landing page and sales headlines from proven templates, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Landing Page Headline Generator</h1>
            <p class="lead text-muted">Create high-converting headlines for your sales pages — easy with proven templates.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="lhProduct" class="form-label fw-semibold">Product / service name</label>
                            <input type="text" class="form-control" id="lhProduct" placeholder="e.g. Solar Installation">
                        </div>
                        <div class="col-md-6">
                            <label for="lhAudience" class="form-label fw-semibold">Target audience</label>
                            <input type="text" class="form-control" id="lhAudience" placeholder="e.g. homeowners in Pakistan">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="lhBenefit" class="form-label fw-semibold">Main benefit</label>
                            <input type="text" class="form-control" id="lhBenefit" placeholder="e.g. 80% lower electricity bill">
                        </div>
                        <div class="col-md-6">
                            <label for="lhPain" class="form-label fw-semibold">Pain point</label>
                            <input type="text" class="form-control" id="lhPain" placeholder="e.g. rising electricity bills">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="lhTone" class="form-label fw-semibold">Tone</label>
                        <select class="form-select" id="lhTone">
                            <option value="direct">Direct / bold</option>
                            <option value="friendly">Friendly / warm</option>
                            <option value="curious">Curiosity / question</option>
                            <option value="urgent">Urgent / offer</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Headlines</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">10 headline ideas</h2>
                        <div class="list-group mb-3" id="lhList"></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success" id="lhCopy">Copy All</button>
                            <button type="button" class="btn btn-outline-secondary" id="lhDownload">TXT Download</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your product, audience, main benefit and pain point.</li>
                <li>Choose a tone and press <strong>Generate Headlines</strong>.</li>
                <li>Click a headline you like to copy it, or copy them all at once.</li>
            </ol>
            <p class="small text-muted">A/B test your headlines — a different headline works better for each audience.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var listEl = document.getElementById('lhList');
    var lastHeadlines = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }

    var TEMPLATES = {
        direct: [
            '{B} — With {P}',
            'Get {B} Without {P2}',
            '{P} for {A}: {B} in Days, Not Months',
            'Stop Suffering From {P2}. Start Getting {B}.',
            'The #1 {P} Service for {A}',
            '{B}, Guaranteed — Or You Pay Nothing'
        ],
        friendly: [
            'Finally, {B} Made Simple for {A}',
            'Your Shortcut to {B} (Without the Headache)',
            'We Help {A} Achieve {B} — Here is How',
            '{P} Should Feel Easy. Now It Is.',
            'Join Hundreds of {A} Already Getting {B}'
        ],
        curious: [
            'What If {B} Was Actually Easy?',
            'Why Are {A} Switching to {P}?',
            'The Secret Behind {B} (Nobody Tells You This)',
            'Still Struggling With {P2}? Read This First.',
            'How Do {A} Get {B}? The Answer Might Surprise You.'
        ],
        urgent: [
            'Limited Offer: {B} With {P} — Claim Yours Today',
            'Only a Few Spots Left for {A} Who Want {B}',
            'End {P2} This Month — Start {B} Now',
            'Act Fast: {P} at Its Best Price Ever',
            'Do not Wait Another Month of {P2}. Get {B} Today.'
        ]
    };

    goBtn.addEventListener('click', function () {
        hideError();
        var p = document.getElementById('lhProduct').value.trim();
        var a = document.getElementById('lhAudience').value.trim();
        var b = document.getElementById('lhBenefit').value.trim();
        var pain = document.getElementById('lhPain').value.trim();
        var tone = document.getElementById('lhTone').value;
        if (!p) { showError('Please enter your product or service name.'); return; }
        if (!b) { showError('Please enter the main benefit — it is the most important part of the headline.'); return; }
        a = a || 'people like you';
        pain = pain || 'the same old problems';

        var temps = TEMPLATES[tone] || TEMPLATES.direct;
        var headlines = temps.map(function (t) {
            return t.split('{P}').join(p).split('{A}').join(cap(a)).split('{B}').join(b).split('{P2}').join(pain);
        });
        // Add generic fillers to reach 10
        var extra = [
            cap(p) + ': ' + cap(b) + ' for ' + cap(a),
            'Discover How ' + cap(a) + ' Get ' + cap(b),
            'Say Goodbye to ' + cap(pain) + ' With ' + cap(p),
            cap(b) + ' — Trusted by ' + cap(a),
            'Try ' + cap(p) + ' Risk-Free and Experience ' + cap(b)
        ];
        headlines = headlines.concat(extra).slice(0, 10);
        lastHeadlines = headlines;

        listEl.innerHTML = '';
        headlines.forEach(function (h, i) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + (i + 1) + '.</strong> ' + escapeHtml(h);
            item.appendChild(span);
            var badge = document.createElement('span');
            badge.className = 'badge bg-primary rounded-pill';
            badge.textContent = 'Copy';
            item.appendChild(badge);
            item.addEventListener('click', function () {
                copyText(h, function () {
                    badge.textContent = 'Copied!';
                    setTimeout(function () { badge.textContent = 'Copy'; }, 1200);
                });
            });
            listEl.appendChild(item);
        });
        results.classList.remove('d-none');
    });

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function copyText(text, done) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    }

    document.getElementById('lhCopy').addEventListener('click', function () {
        hideError();
        if (!lastHeadlines.length) { showError('Generate headlines first.'); return; }
        copyText(lastHeadlines.join('\n'), function () {
            document.getElementById('lhCopy').textContent = 'Copied!';
            setTimeout(function () { document.getElementById('lhCopy').textContent = 'Copy All'; }, 1500);
        });
    });
    document.getElementById('lhDownload').addEventListener('click', function () {
        hideError();
        if (!lastHeadlines.length) { showError('Generate headlines first.'); return; }
        var blob = new Blob([lastHeadlines.join('\n')], { type: 'text/plain' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'headlines.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
