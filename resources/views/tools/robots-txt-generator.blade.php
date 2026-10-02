@extends('layouts.app')

@section('title', 'Robots.txt Generator Online Free | Azlaan Tools')
@section('meta_description', 'Free robots.txt generator: allow or block search bots, add per-bot rules and paths, include your sitemap and download a ready robots.txt file.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Robots.txt Generator</h1>
            <p class="lead text-muted">Build a correct robots.txt for your website with a friendly form. Runs 100% in your browser.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="allowAll" checked><label class="form-check-label fw-semibold" for="allowAll">Allow all bots to crawl everything (simple mode)</label></div>
                    <div id="rulesBox">
                        <label class="form-label fw-semibold">Per-Bot Rules</label>
                        <div id="rulesList"></div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-4"><select id="newAgent" class="form-select"><option>Googlebot</option><option>Bingbot</option><option>*</option><option>DuckDuckBot</option><option>GPTBot</option><option>CCBot</option></select></div>
                            <div class="col-md-3"><select id="newAction" class="form-select"><option value="Disallow">Disallow</option><option value="Allow">Allow</option></select></div>
                            <div class="col-md-3"><input id="newPath" class="form-control" placeholder="/admin/"></div>
                            <div class="col-md-2"><button type="button" class="btn btn-primary w-100" id="addRuleBtn">Add</button></div>
                        </div>
                        <div class="form-text">Tip: add one rule per bot + path, for example Disallow /admin/ for all bots (*).</div>
                    </div>
                    <label class="form-label fw-semibold mt-3" for="sitemapInput">Sitemap URL</label>
                    <input id="sitemapInput" class="form-control" placeholder="https://example.com/sitemap.xml">
                    <label class="form-label fw-semibold mt-3" for="robotsOut">Generated robots.txt</label>
                    <textarea id="robotsOut" class="form-control font-monospace" rows="10" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy</button><button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download robots.txt</button></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>For a simple open site, keep <strong>Allow all</strong> on and just add your sitemap URL.</li>
                <li>To block areas, turn it off and add rules: pick a bot, choose Disallow or Allow, and type a path like /admin/.</li>
                <li>Copy the generated file or download it, then upload it to the root of your website (https://example.com/robots.txt).</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var rules = [{ agent: '*', action: 'Disallow', path: '/admin/' }];
    var listEl = document.getElementById('rulesList'); var out = document.getElementById('robotsOut');
    function renderRules() {
        listEl.innerHTML = '';
        rules.forEach(function (r, i) {
            var row = document.createElement('div'); row.className = 'd-flex justify-content-between align-items-center border rounded px-2 py-1 mb-1 small font-monospace';
            var span = document.createElement('span'); span.textContent = r.agent + ' — ' + r.action + ': ' + r.path;
            var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-sm btn-outline-danger'; btn.textContent = 'Remove';
            btn.addEventListener('click', function () { rules.splice(i, 1); renderRules(); generate(); });
            row.appendChild(span); row.appendChild(btn); listEl.appendChild(row);
        });
    }
    function generate() {
        var lines = [];
        if (document.getElementById('allowAll').checked && rules.length === 0) { lines.push('User-agent: *', 'Allow: /'); }
        else {
            var agents = {};
            rules.forEach(function (r) { if (!agents[r.agent]) agents[r.agent] = []; agents[r.agent].push(r); });
            Object.keys(agents).forEach(function (agent) { lines.push('User-agent: ' + agent); agents[agent].forEach(function (r) { lines.push(r.action + ': ' + r.path); }); lines.push(''); });
        }
        var sitemap = document.getElementById('sitemapInput').value.trim();
        if (sitemap) lines.push('Sitemap: ' + sitemap);
        out.value = lines.join('\n').trim() + '\n';
        document.getElementById('rulesBox').style.opacity = document.getElementById('allowAll').checked ? '0.85' : '1';
    }
    document.getElementById('allowAll').addEventListener('change', generate);
    document.getElementById('sitemapInput').addEventListener('input', generate);
    document.getElementById('addRuleBtn').addEventListener('click', function () {
        var path = document.getElementById('newPath').value.trim() || '/';
        if (path.charAt(0) !== '/') path = '/' + path;
        rules.push({ agent: document.getElementById('newAgent').value, action: document.getElementById('newAction').value, path: path });
        document.getElementById('allowAll').checked = false;
        renderRules(); generate();
    });
    document.getElementById('copyBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(out.value); });
    document.getElementById('downloadBtn').addEventListener('click', function () { var blob = new Blob([out.value], { type: 'text/plain' }); var url = URL.createObjectURL(blob); var a = document.createElement('a'); a.href = url; a.download = 'robots.txt'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 2000); });
    renderRules(); generate();
})();
</script>
@endsection
