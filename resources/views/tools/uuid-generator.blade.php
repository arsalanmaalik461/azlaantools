@extends('layouts.app')

@section('title', 'UUID Generator Online Free - Bulk v4 UUIDs | Azlaan Tools')
@section('meta_description', 'Free UUID generator: create 1 to 1000 random UUID v4 values instantly with uppercase, no-hyphen and braces options. Copy all or download as text. Runs in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">UUID Generator</h1>
            <p class="lead text-muted">Generate random UUID v4 values in bulk. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><label class="form-label fw-semibold" for="countInput">How many? (1–1000)</label><input type="number" id="countInput" class="form-control" value="10" min="1" max="1000"></div>
                        <div class="col-md-8"><label class="form-label fw-semibold">Options</label><div class="d-flex gap-3 flex-wrap pt-2">
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="optUpper"><label class="form-check-label" for="optUpper">Uppercase</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="optNoHyphen"><label class="form-check-label" for="optNoHyphen">No hyphens</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="optBraces"><label class="form-check-label" for="optBraces">Braces</label></div>
                        </div></div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="genBtn">Generate UUIDs</button>
                    <label class="form-label fw-semibold mt-3" for="uuidOut">Result</label>
                    <textarea id="uuidOut" class="form-control font-monospace" rows="12" readonly placeholder="Your UUIDs will appear here..."></textarea>
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy All</button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="downloadBtn">Download .txt</button>
                        <span id="copyMsg" class="text-success small d-none align-self-center">Copied!</span>
                    </div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Choose how many UUIDs you need (1 to 1000) and tick any formatting options.</li>
                <li>Click <strong>Generate UUIDs</strong>.</li>
                <li>Click <strong>Copy All</strong> to copy the whole list, or <strong>Download .txt</strong> to save it as a file.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var out = document.getElementById('uuidOut');
    function makeOne() {
        var id;
        if (crypto.randomUUID) { id = crypto.randomUUID(); }
        else {
            var b = new Uint8Array(16); crypto.getRandomValues(b);
            b[6] = (b[6] & 15) | 64; b[8] = (b[8] & 63) | 128;
            var h = Array.from(b).map(function (x) { return x.toString(16).padStart(2, '0'); }).join('');
            id = h.slice(0, 8) + '-' + h.slice(8, 12) + '-' + h.slice(12, 16) + '-' + h.slice(16, 20) + '-' + h.slice(20);
        }
        if (document.getElementById('optNoHyphen').checked) id = id.replace(/-/g, '');
        if (document.getElementById('optUpper').checked) id = id.toUpperCase();
        if (document.getElementById('optBraces').checked) id = '{' + id + '}';
        return id;
    }
    function generate() {
        var n = parseInt(document.getElementById('countInput').value, 10);
        if (isNaN(n) || n < 1) n = 1; if (n > 1000) n = 1000;
        document.getElementById('countInput').value = n;
        var list = []; for (var i = 0; i < n; i++) list.push(makeOne());
        out.value = list.join('\n');
        document.getElementById('copyMsg').classList.add('d-none');
    }
    document.getElementById('genBtn').addEventListener('click', generate);
    ['optUpper', 'optNoHyphen', 'optBraces'].forEach(function (id) { document.getElementById(id).addEventListener('change', generate); });
    document.getElementById('copyBtn').addEventListener('click', function () { if (!out.value) return; var done = function () { document.getElementById('copyMsg').classList.remove('d-none'); }; if (navigator.clipboard) navigator.clipboard.writeText(out.value).then(done).catch(done); });
    document.getElementById('downloadBtn').addEventListener('click', function () { if (!out.value) return; var blob = new Blob([out.value], { type: 'text/plain' }); var url = URL.createObjectURL(blob); var a = document.createElement('a'); a.href = url; a.download = 'uuids.txt'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 2000); });
    generate();
})();
</script>
@endsection
