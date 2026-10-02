@extends('layouts.app')

@section('title', 'Statistics Calculator — Azlaan Tools')
@section('meta_description', 'Free online statistics calculator. Mean, median, mode, range, variance, standard deviation, quartiles (Q1, Q3) and IQR for any list of numbers. Instant results, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Statistics Calculator</h1>
            <p class="lead text-muted">Enter your list of data — get mean, median, mode, variance, standard deviation and quartiles all at once. Perfect for exam data, surveys or any set of numbers.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="statInput" class="form-label fw-semibold">Your numbers (separated by comma, space or new line)</label>
                    <textarea class="form-control form-control-lg" id="statInput" rows="4" placeholder="e.g. 4, 8, 15, 16, 23, 42"></textarea>

                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Mean (Average)</div>
                        <div class="fs-3 fw-bold" id="stMean">—</div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr><th style="width:55%">Count (n)</th><td id="stCount">—</td></tr>
                                <tr><th>Sum</th><td id="stSum">—</td></tr>
                                <tr><th>Median (middle value)</th><td id="stMedian">—</td></tr>
                                <tr><th>Mode (most frequent)</th><td id="stMode">—</td></tr>
                                <tr><th>Minimum</th><td id="stMin">—</td></tr>
                                <tr><th>Maximum</th><td id="stMax">—</td></tr>
                                <tr><th>Range (Max − Min)</th><td id="stRange">—</td></tr>
                                <tr><th>Variance — Population (σ²)</th><td id="stVarPop">—</td></tr>
                                <tr><th>Variance — Sample (s²)</th><td id="stVarSam">—</td></tr>
                                <tr><th>Standard Deviation — Population (σ)</th><td id="stSdPop">—</td></tr>
                                <tr><th>Standard Deviation — Sample (s)</th><td id="stSdSam">—</td></tr>
                                <tr><th>Q1 (first quartile)</th><td id="stQ1">—</td></tr>
                                <tr><th>Q3 (third quartile)</th><td id="stQ3">—</td></tr>
                                <tr><th>IQR (Q3 − Q1)</th><td id="stIqr">—</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mt-3 mb-0"><strong>Sorted list:</strong> <span id="stSorted">—</span></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p><strong>Mean</strong> is the average of all numbers. <strong>Mode</strong> is the number that appears most often. <strong>Variance</strong> and <strong>standard deviation</strong> show how spread out the data is around the mean — the bigger the value, the more scattered the data.</p>
                    <ul>
                        <li>Example: data 4, 8, 15, 16, 23, 42 — count 6, sum 108, mean = <strong>18</strong>, median = (15+16)/2 = <strong>15.5</strong>, range = 42 − 4 = <strong>38</strong>.</li>
                        <li>Population vs Sample: if you have the full data (for example, all marks in a class), use the <strong>population</strong> values; if your data is only part (a sample) of a larger group, the <strong>sample</strong> values are correct — sample variance divides by (n−1).</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: Q1 and Q3 use the "median-of-halves" method — the sorted list is split into two halves and the median of each half is taken (with an odd count, the middle number is left out of both halves).</p>
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
    function medianOf(arr) {
        if (arr.length === 0) { return null; }
        var mid = Math.floor(arr.length / 2);
        return arr.length % 2 === 1 ? arr[mid] : (arr[mid - 1] + arr[mid]) / 2;
    }

    var cellIds = ['stMean', 'stCount', 'stSum', 'stMedian', 'stMode', 'stMin', 'stMax', 'stRange',
        'stVarPop', 'stVarSam', 'stSdPop', 'stSdSam', 'stQ1', 'stQ3', 'stIqr'];
    function set(id, txt) { document.getElementById(id).textContent = txt; }

    function calc() {
        var nums = parseNumbers(document.getElementById('statInput').value);
        if (nums.length === 0) {
            cellIds.forEach(function (id) { set(id, '—'); });
            set('stSorted', '—');
            return;
        }
        var n = nums.length;
        var sorted = nums.slice().sort(function (a, b) { return a - b; });
        var sum = 0;
        nums.forEach(function (x) { sum += x; });
        var mean = sum / n;
        var median = medianOf(sorted);

        // Mode(s)
        var freq = {};
        var maxFreq = 0;
        nums.forEach(function (x) {
            var k = String(x);
            freq[k] = (freq[k] || 0) + 1;
            if (freq[k] > maxFreq) { maxFreq = freq[k]; }
        });
        var modeText;
        if (maxFreq <= 1) { modeText = 'No mode (each number appears once)'; }
        else {
            var modes = [];
            Object.keys(freq).forEach(function (k) { if (freq[k] === maxFreq) { modes.push(parseFloat(k)); } });
            modes.sort(function (a, b) { return a - b; });
            modeText = modes.map(fmt).join(', ') + ' (' + maxFreq + ' times)';
        }

        // Variance
        var sqSum = 0;
        nums.forEach(function (x) { sqSum += (x - mean) * (x - mean); });
        var varPop = sqSum / n;
        var varSam = n > 1 ? sqSum / (n - 1) : null;

        // Quartiles — median of halves (exclude overall median when n is odd)
        var mid = Math.floor(n / 2);
        var lower = sorted.slice(0, mid);
        var upper = n % 2 === 1 ? sorted.slice(mid + 1) : sorted.slice(mid);
        var q1 = medianOf(lower);
        var q3 = medianOf(upper);

        set('stMean', fmt(mean));
        set('stCount', n.toLocaleString());
        set('stSum', fmt(sum));
        set('stMedian', fmt(median));
        set('stMode', modeText);
        set('stMin', fmt(sorted[0]));
        set('stMax', fmt(sorted[n - 1]));
        set('stRange', fmt(sorted[n - 1] - sorted[0]));
        set('stVarPop', fmt(varPop));
        set('stVarSam', varSam === null ? '— (n must be 2+)' : fmt(varSam));
        set('stSdPop', fmt(Math.sqrt(varPop)));
        set('stSdSam', varSam === null ? '— (n must be 2+)' : fmt(Math.sqrt(varSam)));
        set('stQ1', q1 === null ? '—' : fmt(q1));
        set('stQ3', q3 === null ? '—' : fmt(q3));
        set('stIqr', (q1 === null || q3 === null) ? '—' : fmt(q3 - q1));
        set('stSorted', sorted.map(fmt).join(', '));
    }
    document.getElementById('statInput').addEventListener('input', calc);
    calc();
})();
</script>
@endsection
