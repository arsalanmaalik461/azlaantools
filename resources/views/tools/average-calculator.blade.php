@extends('layouts.app')

@section('title', 'Average Calculator — Azlaan Tools')
@section('meta_description', 'Free online average calculator. Find mean, median, sum, count, min, max and range of any list of numbers, plus a weighted average calculator. Instant, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Average Calculator</h1>
            <p class="lead text-muted">Type your list of numbers — you get the average (mean), median, total sum and other stats right away. There is a weighted average mode below too, for example subject marks weighted by credit hours.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Average of a List of Numbers</h2>
                    <label for="avgInput" class="form-label">Numbers (separate with comma, space or a new line)</label>
                    <textarea class="form-control form-control-lg" id="avgInput" rows="4" placeholder="e.g. 10, 20, 30, 40, 50"></textarea>
                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Average (Mean)</div>
                        <div class="fs-3 fw-bold" id="avgMean">—</div>
                    </div>
                    <div class="row g-3 text-center mt-1">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgCount">—</div>
                                <div class="text-muted small">Count</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgSum">—</div>
                                <div class="text-muted small">Sum</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgMedian">—</div>
                                <div class="text-muted small">Median</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgMin">—</div>
                                <div class="text-muted small">Min</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgMax">—</div>
                                <div class="text-muted small">Max</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="avgRange">—</div>
                                <div class="text-muted small">Range (Max − Min)</div>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0" id="avgNote">Type numbers and the stats update here live.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Weighted Average</h2>
                    <p class="small text-muted">Type each value with its weight — for example a subject's marks and its credit hours.</p>
                    <div id="wRows"></div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="wAdd">+ Add Row</button>
                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Weighted Average = &Sigma;(value &times; weight) &divide; &Sigma;weights</div>
                        <div class="fs-3 fw-bold" id="wResult">—</div>
                        <div class="small text-muted" id="wDetail">—</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p><strong>Mean (average)</strong> = sum of all numbers &divide; their count. <strong>Median</strong> is the middle number when the list is sorted from small to big. In a <strong>weighted average</strong>, each value is multiplied by its weight and added up, so the more important values have more effect.</p>
                    <ul>
                        <li>Example 1: the sum of 10, 20, 30, 40, 50 is 150, count is 5, so average = 150 &divide; 5 = <strong>30</strong>. The median is also 30.</li>
                        <li>Example 2: weighted — marks 80 (weight 3) and 90 (weight 2): (80&times;3 + 90&times;2) &divide; (3+2) = 420 &divide; 5 = <strong>84</strong>.</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: invalid characters (letters, etc.) are ignored by themselves — only valid numbers are counted.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function fmt(n) {
        if (!isFinite(n)) { return '—'; }
        return parseFloat(n.toPrecision(12)).toLocaleString('en-US', { maximumFractionDigits: 6 });
    }
    function parseNumbers(text) {
        var parts = String(text).split(/[\s,;]+/);
        var out = [];
        parts.forEach(function (p) {
            if (p === '') { return; }
            var v = parseFloat(p);
            if (isFinite(v)) { out.push(v); }
        });
        return out;
    }

    var ids = ['avgMean', 'avgCount', 'avgSum', 'avgMedian', 'avgMin', 'avgMax', 'avgRange'];
    function calcAvg() {
        var nums = parseNumbers(document.getElementById('avgInput').value);
        var note = document.getElementById('avgNote');
        if (nums.length === 0) {
            ids.forEach(function (id) { document.getElementById(id).textContent = '—'; });
            note.textContent = 'Type numbers and the stats update here live.';
            return;
        }
        var sum = 0;
        nums.forEach(function (n) { sum += n; });
        var sorted = nums.slice().sort(function (a, b) { return a - b; });
        var mid = Math.floor(sorted.length / 2);
        var median = sorted.length % 2 === 1 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2;
        document.getElementById('avgMean').textContent = fmt(sum / nums.length);
        document.getElementById('avgCount').textContent = nums.length.toLocaleString();
        document.getElementById('avgSum').textContent = fmt(sum);
        document.getElementById('avgMedian').textContent = fmt(median);
        document.getElementById('avgMin').textContent = fmt(sorted[0]);
        document.getElementById('avgMax').textContent = fmt(sorted[sorted.length - 1]);
        document.getElementById('avgRange').textContent = fmt(sorted[sorted.length - 1] - sorted[0]);
        note.textContent = nums.length + ' numbers found. Sorted: ' + sorted.map(fmt).join(', ');
    }
    document.getElementById('avgInput').addEventListener('input', calcAvg);

    // ---- Weighted average ----
    var wRows = document.getElementById('wRows');
    function addRow(v, w) {
        var row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-2 w-row';
        row.innerHTML =
            '<div class="col-5"><label class="form-label">Value</label>' +
            '<input type="number" step="any" class="form-control form-control-lg w-val" placeholder="e.g. 80"></div>' +
            '<div class="col-5"><label class="form-label">Weight</label>' +
            '<input type="number" step="any" class="form-control form-control-lg w-wt" placeholder="e.g. 3"></div>' +
            '<div class="col-2"><button type="button" class="btn btn-outline-danger btn-lg w-100 w-del" title="Remove row">&times;</button></div>';
        if (v !== undefined) { row.querySelector('.w-val').value = v; }
        if (w !== undefined) { row.querySelector('.w-wt').value = w; }
        row.querySelector('.w-del').addEventListener('click', function () {
            row.remove();
            calcWeighted();
        });
        wRows.appendChild(row);
    }
    function calcWeighted() {
        var rows = wRows.querySelectorAll('.w-row');
        var sw = 0, svw = 0, used = 0;
        rows.forEach(function (row) {
            var v = parseFloat(row.querySelector('.w-val').value);
            var w = parseFloat(row.querySelector('.w-wt').value);
            if (isFinite(v) && isFinite(w)) { svw += v * w; sw += w; used++; }
        });
        if (used === 0 || sw === 0) {
            document.getElementById('wResult').textContent = '—';
            document.getElementById('wDetail').textContent = '—';
            return;
        }
        document.getElementById('wResult').textContent = fmt(svw / sw);
        document.getElementById('wDetail').textContent = used + ' rows used · total weight = ' + fmt(sw);
    }
    wRows.addEventListener('input', calcWeighted);
    document.getElementById('wAdd').addEventListener('click', function () { addRow(); });
    addRow(80, 3);
    addRow(90, 2);
    addRow();
    calcAvg();
    calcWeighted();
})();
</script>
@endsection
