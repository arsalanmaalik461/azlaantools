@extends('layouts.app')

@section('title', 'LCM & GCF Calculator — Azlaan Tools')
@section('meta_description', 'Free online LCM and GCF calculator. Enter two to four numbers to find their Greatest Common Factor and Least Common Multiple with prime factorization steps.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">LCM &amp; GCF Calculator</h1>
            <p class="lead text-muted">Find the GCF (greatest common factor) and LCM (least common multiple) of two or more numbers instantly — with prime factorization.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="num1" class="form-label fw-semibold">Number 1</label>
                            <input type="number" class="form-control form-control-lg" id="num1" placeholder="e.g. 12" step="1">
                        </div>
                        <div class="col-6">
                            <label for="num2" class="form-label fw-semibold">Number 2</label>
                            <input type="number" class="form-control form-control-lg" id="num2" placeholder="e.g. 18" step="1">
                        </div>
                        <div class="col-6">
                            <label for="num3" class="form-label fw-semibold">Number 3 (optional)</label>
                            <input type="number" class="form-control form-control-lg" id="num3" placeholder="optional" step="1">
                        </div>
                        <div class="col-6">
                            <label for="num4" class="form-label fw-semibold">Number 4 (optional)</label>
                            <input type="number" class="form-control form-control-lg" id="num4" placeholder="optional" step="1">
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Negative numbers use their absolute value. With zero, GCF = the other number and LCM = 0.</p>

                    <div class="result-box mt-3" id="lcmGcfResult">
                        <div class="row text-center g-3">
                            <div class="col-6">
                                <div class="text-muted small">GCF (Greatest Common Factor)</div>
                                <div class="display-6 fw-bold" id="gcfValue">—</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">LCM (Least Common Multiple)</div>
                                <div class="display-6 fw-bold" id="lcmValue">—</div>
                            </div>
                        </div>
                        <p class="mt-3 mb-1 fw-semibold" id="stepLine">Enter at least two numbers — the result appears here live.</p>
                        <div id="factorBreakdown" class="small text-muted"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>The GCF comes from the Euclidean algorithm: divide the bigger number by the smaller one and keep the remainder, repeating until the remainder is 0. The LCM formula is: <strong>LCM = a &times; b &divide; GCF</strong>. With three or four numbers, the same step is applied to each number one by one.</p>
                    <p class="mb-1"><strong>Example 1:</strong> 12 and 18 — 12 = 2&sup2; &times; 3, 18 = 2 &times; 3&sup2; — GCF = 6, LCM = 12 &times; 18 &divide; 6 = <strong>36</strong>.</p>
                    <p class="mb-0"><strong>Example 2:</strong> 8 and 14 have GCF = 2, so LCM = 8 &times; 14 &divide; 2 = <strong>56</strong>. The LCM is used as a common denominator for adding fractions, and the GCF for simplifying them.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var ids = ['num1', 'num2', 'num3', 'num4'];

    function gcd2(a, b) {
        a = Math.abs(a); b = Math.abs(b);
        while (b !== 0) { var t = a % b; a = b; b = t; }
        return a;
    }
    function lcm2(a, b) {
        a = Math.abs(a); b = Math.abs(b);
        if (a === 0 || b === 0) return 0;
        var g = gcd2(a, b);
        var v = (a / g) * b;
        return v;
    }
    function primeFactors(n) {
        n = Math.abs(n);
        if (n < 2) return String(n);
        var parts = [];
        var d = 2;
        var x = n;
        while (d * d <= x) {
            var count = 0;
            while (x % d === 0) { x = x / d; count++; }
            if (count === 1) parts.push(String(d));
            else if (count > 1) parts.push(d + '^' + count);
            d = (d === 2) ? 3 : d + 2;
        }
        if (x > 1) parts.push(String(x));
        return parts.join(' × ');
    }
    function fmt(n) {
        if (!isFinite(n)) return '—';
        return Number(n).toLocaleString('en-PK');
    }
    function calc() {
        var nums = [];
        var raw = [];
        for (var i = 0; i < ids.length; i++) {
            var v = document.getElementById(ids[i]).value;
            if (v !== '' && v !== null) {
                var p = parseFloat(v);
                if (isFinite(p)) { nums.push(Math.round(Math.abs(p))); raw.push(p); }
            }
        }
        var gcfEl = document.getElementById('gcfValue');
        var lcmEl = document.getElementById('lcmValue');
        var stepEl = document.getElementById('stepLine');
        var brEl = document.getElementById('factorBreakdown');
        if (nums.length < 2) {
            gcfEl.textContent = '—'; lcmEl.textContent = '—';
            stepEl.textContent = 'Enter at least two numbers — the result appears here live.';
            brEl.innerHTML = '';
            return;
        }
        var g = nums[0], l = nums[0], overflow = false;
        for (var j = 1; j < nums.length; j++) {
            g = gcd2(g, nums[j]);
            l = lcm2(l, nums[j]);
            if (!isFinite(l) || l > 9007199254740991) overflow = true;
        }
        gcfEl.textContent = fmt(g);
        lcmEl.textContent = overflow ? 'Too large' : fmt(l);
        if (nums.length === 2) {
            stepEl.textContent = 'LCM = ' + fmt(nums[0]) + ' × ' + fmt(nums[1]) + ' ÷ GCF (' + fmt(g) + ') = ' + (overflow ? 'too large' : fmt(l));
        } else {
            stepEl.textContent = 'GCF and LCM were found step by step, adding each number (' + nums.length + ' numbers).';
        }
        var lines = [];
        for (var k = 0; k < nums.length; k++) {
            lines.push('<div>' + fmt(nums[k]) + ' = ' + primeFactors(nums[k]) + '</div>');
        }
        brEl.innerHTML = lines.join('');
    }
    for (var i = 0; i < ids.length; i++) {
        document.getElementById(ids[i]).addEventListener('input', calc);
    }
    calc();
})();
</script>
@endsection
