@extends('layouts.app')

@section('title', 'Diff Checker Online Free - Compare Two Texts | Azlaan Tools')
@section('meta_description', 'Free diff checker: compare two texts line by line, see additions in green and deletions in red with counts, side-by-side or unified view. Runs in your browser.')

@section('styles')
<style>
.diff-add{background-color:#d1e7dd}
.diff-del{background-color:#f8d7da}
.diff-box{white-space:pre-wrap;word-break:break-word;font-size:0.85rem}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Diff Checker</h1>
            <p class="lead text-muted">Compare two texts line by line. Additions are green, deletions are red. This tool runs 100% in your browser — nothing is uploaded or saved.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold" for="origText">Original Text</label><textarea id="origText" class="form-control font-monospace" rows="10">Hello world
This is line two.
Old line here.
Common line.</textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold" for="changedText">Changed Text</label><textarea id="changedText" class="form-control font-monospace" rows="10">Hello world
This is line two, edited.
New line added.
Common line.</textarea></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                        <button type="button" class="btn btn-primary" id="compareBtn">Compare</button>
                        <div class="form-check form-check-inline ms-2"><input class="form-check-input" type="radio" name="viewMode" id="viewUnified" checked><label class="form-check-label" for="viewUnified">Unified</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="viewMode" id="viewSide"><label class="form-check-label" for="viewSide">Side-by-side</label></div>
                        <span class="badge bg-success" id="addCount">0 additions</span>
                        <span class="badge bg-danger" id="delCount">0 deletions</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="swapBtn">Swap Texts</button>
                    </div>
                    <div id="diffOut" class="mt-3"></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Paste the original text on the left and the changed text on the right.</li>
                <li>Click <strong>Compare</strong> (the diff also updates as you type).</li>
                <li>Read green lines as additions and red lines as deletions, and switch between unified and side-by-side views.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/diff@5.2.0/dist/diff.min.js"></script>
<script>
(function () {
    var origEl = document.getElementById('origText'); var changedEl = document.getElementById('changedText'); var out = document.getElementById('diffOut');
    function esc(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    function lcsDiff(aLines, bLines) {
        var n = aLines.length; var m = bLines.length;
        var dp = []; for (var i = 0; i <= n; i++) { dp.push(new Array(m + 1).fill(0)); }
        for (var x = n - 1; x >= 0; x--) { for (var y = m - 1; y >= 0; y--) { dp[x][y] = aLines[x] === bLines[y] ? dp[x + 1][y + 1] + 1 : Math.max(dp[x + 1][y], dp[x][y + 1]); } }
        var ops = []; var i2 = 0; var j2 = 0;
        while (i2 < n && j2 < m) { if (aLines[i2] === bLines[j2]) { ops.push({ type: 'same', text: aLines[i2] }); i2++; j2++; } else if (dp[i2 + 1][j2] >= dp[i2][j2 + 1]) { ops.push({ type: 'del', text: aLines[i2] }); i2++; } else { ops.push({ type: 'add', text: bLines[j2] }); j2++; } }
        while (i2 < n) { ops.push({ type: 'del', text: aLines[i2] }); i2++; }
        while (j2 < m) { ops.push({ type: 'add', text: bLines[j2] }); j2++; }
        return ops;
    }
    function getOps() {
        var a = origEl.value.split('\n'); var b = changedEl.value.split('\n');
        if (window.Diff && window.Diff.diffLines) {
            var ops = []; var parts = window.Diff.diffLines(origEl.value, changedEl.value);
            parts.forEach(function (part) {
                var lines = part.value.replace(/\n$/, '').split('\n');
                if (part.value === '') lines = [];
                lines.forEach(function (line) { ops.push({ type: part.added ? 'add' : (part.removed ? 'del' : 'same'), text: line }); });
            });
            return ops;
        }
        return lcsDiff(a, b);
    }
    function compare() {
        var ops = getOps(); var adds = 0; var dels = 0;
        ops.forEach(function (op) { if (op.type === 'add') adds++; if (op.type === 'del') dels++; });
        document.getElementById('addCount').textContent = adds + ' additions';
        document.getElementById('delCount').textContent = dels + ' deletions';
        out.innerHTML = '';
        if (document.getElementById('viewSide').checked) {
            var left = []; var right = [];
            ops.forEach(function (op) { if (op.type === 'same') { left.push(op); right.push(op); } else if (op.type === 'del') { left.push(op); } else { right.push(op); } });
            var row = document.createElement('div'); row.className = 'row g-2';
            [left, right].forEach(function (side, idx) {
                var col = document.createElement('div'); col.className = 'col-md-6';
                var box = document.createElement('div'); box.className = 'border rounded p-2 font-monospace diff-box';
                box.innerHTML = side.map(function (op) { var cls = op.type === 'add' ? 'diff-add' : (op.type === 'del' ? 'diff-del' : ''); var sign = op.type === 'add' ? '+ ' : (op.type === 'del' ? '- ' : '  '); return '<div class="' + cls + '">' + esc(sign + op.text) + '</div>'; }).join('');
                col.appendChild(box); row.appendChild(col);
            });
            out.appendChild(row);
        } else {
            var box2 = document.createElement('div'); box2.className = 'border rounded p-2 font-monospace diff-box';
            box2.innerHTML = ops.map(function (op) { var cls = op.type === 'add' ? 'diff-add' : (op.type === 'del' ? 'diff-del' : ''); var sign = op.type === 'add' ? '+ ' : (op.type === 'del' ? '- ' : '  '); return '<div class="' + cls + '">' + esc(sign + op.text) + '</div>'; }).join('');
            out.appendChild(box2);
        }
    }
    document.getElementById('compareBtn').addEventListener('click', compare);
    [origEl, changedEl].forEach(function (el) { el.addEventListener('input', compare); });
    document.querySelectorAll('input[name="viewMode"]').forEach(function (el) { el.addEventListener('change', compare); });
    document.getElementById('swapBtn').addEventListener('click', function () { var t = origEl.value; origEl.value = changedEl.value; changedEl.value = t; compare(); });
    compare();
})();
</script>
@endsection
