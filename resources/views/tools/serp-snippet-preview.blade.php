@extends('layouts.app')

@section('title', 'SERP Snippet Preview - Azlaan Tools')
@section('meta_description', 'See how your title and description look in Google search results — desktop and mobile preview, title/description length checks, free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">SERP Snippet Preview</h1>
            <p class="lead text-muted">See how your result will look on Google before you publish — with both desktop and mobile previews and SEO length checks.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="serpUrl" class="form-label fw-semibold">Page URL</label>
                        <input type="text" class="form-control" id="serpUrl" placeholder="https://example.com/my-page">
                    </div>
                    <div class="mb-3">
                        <label for="serpTitle" class="form-label fw-semibold">Meta Title <span class="text-muted small">(<span id="titleLen">0</span> chars)</span></label>
                        <input type="text" class="form-control" id="serpTitle" placeholder="Your page title…">
                    </div>
                    <div class="mb-3">
                        <label for="serpDesc" class="form-label fw-semibold">Meta Description <span class="text-muted small">(<span id="descLen">0</span> chars)</span></label>
                        <textarea class="form-control" id="serpDesc" rows="3" placeholder="1-2 sentence summary of the page…"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">See Preview</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h6 class="text-muted">Desktop preview</h6>
                        <div class="border rounded p-3 mb-4" style="max-width: 640px; background: #fff;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:28px;height:28px;background:#6d28d9;font-size:14px;" id="favIcon">A</div>
                                <div>
                                    <div style="font-size:14px;color:#202124;" id="pvSite">example.com</div>
                                    <div style="font-size:12px;color:#4d5156;" id="pvUrl">https://example.com › my-page</div>
                                </div>
                            </div>
                            <div style="font-size:20px;color:#1a0dab;line-height:1.3;cursor:pointer;" id="pvTitle">Title</div>
                            <div style="font-size:14px;color:#4d5156;line-height:1.58;" id="pvDesc">Description</div>
                        </div>

                        <h6 class="text-muted">Mobile preview</h6>
                        <div class="border rounded p-3 mb-4" style="max-width: 360px; background: #fff;">
                            <div style="font-size:12px;color:#202124;" id="pvSiteM">example.com</div>
                            <div style="font-size:16px;color:#1a0dab;line-height:1.3;" id="pvTitleM">Title</div>
                            <div style="font-size:14px;color:#4d5156;line-height:1.5;" id="pvDescM">Description</div>
                        </div>

                        <h6>Length checks</h6>
                        <ul class="list-group" id="checkList"></ul>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the page URL, meta title and meta description.</li>
                <li>Click <strong>See Preview</strong> — you will see the Google-style desktop and mobile result.</li>
                <li>Keep the title around ~60 characters and the description around ~155 characters in the checks, otherwise Google cuts them (adds …).</li>
            </ol>
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
    var titleInput = document.getElementById('serpTitle');
    var descInput = document.getElementById('serpDesc');
    var urlInput = document.getElementById('serpUrl');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function updateCounts() {
        document.getElementById('titleLen').textContent = titleInput.value.length;
        document.getElementById('descLen').textContent = descInput.value.length;
    }
    titleInput.addEventListener('input', updateCounts);
    descInput.addEventListener('input', updateCounts);

    function hostOf(u) {
        try {
            var h = u;
            if (!/^https?:\/\//i.test(h)) h = 'https://' + h;
            return new URL(h).hostname.replace(/^www\./, '');
        } catch (e) { return u; }
    }
    function shortUrl(u) {
        var h = hostOf(u);
        var path = u.replace(/^https?:\/\//i, '').replace(/^www\./i, '').replace(/^[^\/]*/, '');
        path = path.replace(/\//g, ' › ').replace(/^ › /, '');
        return (path.length > 45 ? path.slice(0, 45) + '…' : path) || h;
    }

    function checkItem(label, ok, hint) {
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-start';
        var badge = ok
            ? '<span class="badge bg-success rounded-pill">OK</span>'
            : '<span class="badge bg-warning text-dark rounded-pill">Check</span>';
        li.innerHTML = '<div><strong>' + label + '</strong><br><small class="text-muted">' + hint + '</small></div>' + badge;
        return li;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var url = urlInput.value.trim();
        var title = titleInput.value.trim();
        var desc = descInput.value.trim();
        if (!title) { showError('Please enter a value. First write the meta title.'); return; }
        if (!desc) { showError('Also write the meta description so the full preview can be made.'); return; }

        var host = url ? hostOf(url) : 'your-site.com';
        var urlLine = url ? host + ' › ' + shortUrl(url).split(' › ').slice(1).join(' › ') : host;
        var fav = host.charAt(0).toUpperCase();

        ['pvSite', 'pvSiteM'].forEach(function (id) { document.getElementById(id).textContent = host; });
        document.getElementById('pvUrl').textContent = url ? 'https://' + urlLine : '';
        document.getElementById('favIcon').textContent = fav;
        document.getElementById('pvTitle').textContent = title;
        document.getElementById('pvTitleM').textContent = title;
        document.getElementById('pvDesc').textContent = desc;
        document.getElementById('pvDescM').textContent = desc;

        var list = document.getElementById('checkList');
        list.innerHTML = '';
        var tLen = title.length, dLen = desc.length;
        list.appendChild(checkItem('Title length: ' + tLen + ' chars', tLen >= 30 && tLen <= 60, 'Google cuts the title after ~600 pixels (~60 chars).'));
        list.appendChild(checkItem('Description length: ' + dLen + ' chars', dLen >= 70 && dLen <= 160, 'After ~155 chars, "…" is added to the description.'));
        list.appendChild(checkItem('Keyword in title', title.length > 0, 'Putting the main keyword at the start of the title is better SEO.'));
        list.appendChild(checkItem('Call-to-action in description', /(free|download|learn|buy|guide|best|check|online)/i.test(desc), 'A small action word (e.g. "free", "guide") increases clicks.'));

        results.classList.remove('d-none');
    });
})();
</script>
@endsection
