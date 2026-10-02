@extends('layouts.app')

@section('title', 'Fraction Calculator — Azlaan Tools')
@section('meta_description', 'Free online fraction calculator. Add, subtract, multiply and divide fractions, simplify fractions, and convert fractions to decimals and percentages. Instant results, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Fraction Calculator</h1>
            <p class="lead text-muted">Calculate with two fractions — add, subtract, multiply or divide — and get the result as a simplified fraction, mixed number and decimal. The result appears as you type the numbers.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Add / Subtract / Multiply / Divide Fractions</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-3">
                            <label for="n1" class="form-label">Numerator 1</label>
                            <input type="number" class="form-control form-control-lg" id="n1" placeholder="1" step="any">
                        </div>
                        <div class="col-3">
                            <label for="d1" class="form-label">Denominator 1</label>
                            <input type="number" class="form-control form-control-lg" id="d1" placeholder="2" step="any">
                        </div>
                        <div class="col-2">
                            <label for="fop" class="form-label">Operation</label>
                            <select class="form-select form-select-lg" id="fop">
                                <option value="+">+</option>
                                <option value="-">&minus;</option>
                                <option value="*">&times;</option>
                                <option value="/">&divide;</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="n2" class="form-label">Num. 2</label>
                            <input type="number" class="form-control form-control-lg" id="n2" placeholder="1" step="any">
                        </div>
                        <div class="col-2">
                            <label for="d2" class="form-label">Den. 2</label>
                            <input type="number" class="form-control form-control-lg" id="d2" placeholder="3" step="any">
                        </div>
                    </div>
                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Result (simplified fraction)</div>
                        <div class="fs-3 fw-bold" id="fResult">—</div>
                    </div>
                    <div class="row g-3 text-center mt-1">
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="fMixed">—</div>
                                <div class="text-muted small">Mixed Number</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="fDecimal">—</div>
                                <div class="text-muted small">Decimal</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="fPercent">—</div>
                                <div class="text-muted small">Percent</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="border rounded p-3 h-100">
                                <div class="fs-5 fw-bold" id="fStep">—</div>
                                <div class="text-muted small">Before Simplifying</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none mb-0" id="fError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Simplify a Fraction</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="sn" class="form-label">Numerator</label>
                            <input type="number" class="form-control form-control-lg" id="sn" placeholder="e.g. 12" step="any">
                        </div>
                        <div class="col-4">
                            <label for="sd" class="form-label">Denominator</label>
                            <input type="number" class="form-control form-control-lg" id="sd" placeholder="e.g. 18" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Simplified</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="sResult">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Simplified mixed number: <span class="fw-semibold" id="sMixed">—</span> &nbsp;|&nbsp; Decimal: <span class="fw-semibold" id="sDecimal">—</span></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Fraction to Decimal &amp; Percent</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-3">
                            <label for="cn" class="form-label">Numerator</label>
                            <input type="number" class="form-control form-control-lg" id="cn" placeholder="e.g. 3" step="any">
                        </div>
                        <div class="col-3">
                            <label for="cd" class="form-label">Denominator</label>
                            <input type="number" class="form-control form-control-lg" id="cd" placeholder="e.g. 4" step="any">
                        </div>
                        <div class="col-3">
                            <label class="form-label">Decimal</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="cDecimal">—</div>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Percent</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="cPercent">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>A fraction means dividing one number by another — the top part is the <strong>numerator</strong> and the bottom part is the <strong>denominator</strong>. The calculator first finds the answer, then divides the numerator and denominator by their greatest common factor (GCD) to give the simplest form.</p>
                    <ul>
                        <li>Example 1: 1/2 + 1/3 = 5/6 (decimal 0.8333). First we make the common denominator 6: 3/6 + 2/6 = 5/6.</li>
                        <li>Example 2: 12/18 simplifies to 2/3, because both 12 and 18 are divisible by 6.</li>
                        <li>Example 3: 7/4 as a mixed number is 1 3/4, as a decimal it is 1.75 and as a percent it is 175%.</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: the denominator can never be 0 — dividing by 0 has no answer, so the calculator will show a friendly error.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) {
        var v = document.getElementById(id).value;
        return v === '' ? null : parseFloat(v);
    }
    function gcd(a, b) {
        a = Math.abs(Math.round(a)); b = Math.abs(Math.round(b));
        while (b) { var t = a % b; a = b; b = t; }
        return a || 1;
    }
    function fmt(n) {
        if (!isFinite(n)) { return '—'; }
        return parseFloat(n.toPrecision(10)).toLocaleString('en-US', { maximumFractionDigits: 6 });
    }
    function simplify(n, d) {
        // Returns [n, d] reduced, sign carried by numerator. Inputs must be integers.
        if (d < 0) { n = -n; d = -d; }
        var g = gcd(n, d);
        return [n / g, d / g];
    }
    function mixedStr(n, d) {
        if (d === 1) { return String(n); }
        var sign = n < 0 ? '-' : '';
        var an = Math.abs(n);
        var whole = Math.floor(an / d);
        var rem = an % d;
        if (rem === 0) { return sign + String(whole); }
        if (whole === 0) { return sign + rem + '/' + d; }
        return sign + whole + ' ' + rem + '/' + d;
    }
    function isInt(x) { return Math.floor(x) === x; }

    function calcMain() {
        var n1 = val('n1'), d1 = val('d1'), n2 = val('n2'), d2 = val('d2');
        var op = document.getElementById('fop').value;
        var err = document.getElementById('fError');
        var ids = ['fResult', 'fMixed', 'fDecimal', 'fPercent', 'fStep'];
        function clear(msg) {
            ids.forEach(function (id) { document.getElementById(id).textContent = '—'; });
            if (msg) { err.textContent = msg; err.classList.remove('d-none'); }
            else { err.classList.add('d-none'); }
        }
        if (n1 === null || d1 === null || n2 === null || d2 === null) { clear(null); return; }
        if (!isInt(n1) || !isInt(d1) || !isInt(n2) || !isInt(d2)) { clear('Write whole numbers (integers) for fractions — a decimal numerator or denominator does not work here.'); return; }
        if (d1 === 0 || d2 === 0) { clear('The denominator cannot be 0.'); return; }
        var rn, rd;
        if (op === '+') { rn = n1 * d2 + n2 * d1; rd = d1 * d2; }
        else if (op === '-') { rn = n1 * d2 - n2 * d1; rd = d1 * d2; }
        else if (op === '*') { rn = n1 * n2; rd = d1 * d2; }
        else {
            if (n2 === 0) { clear('You cannot divide by a fraction with 0 on top.'); return; }
            rn = n1 * d2; rd = d1 * n2;
        }
        if (rd < 0) { rn = -rn; rd = -rd; }
        document.getElementById('fStep').textContent = rn + '/' + rd;
        var s = simplify(rn, rd);
        var dec = s[0] / s[1];
        document.getElementById('fResult').textContent = s[1] === 1 ? String(s[0]) : s[0] + '/' + s[1];
        document.getElementById('fMixed').textContent = mixedStr(s[0], s[1]);
        document.getElementById('fDecimal').textContent = fmt(dec);
        document.getElementById('fPercent').textContent = fmt(dec * 100) + '%';
        err.classList.add('d-none');
    }

    function calcSimplify() {
        var n = val('sn'), d = val('sd');
        if (n === null || d === null || d === 0 || !isInt(n) || !isInt(d)) {
            document.getElementById('sResult').textContent = '—';
            document.getElementById('sMixed').textContent = '—';
            document.getElementById('sDecimal').textContent = '—';
            return;
        }
        var s = simplify(n, d);
        document.getElementById('sResult').textContent = s[1] === 1 ? String(s[0]) : s[0] + '/' + s[1];
        document.getElementById('sMixed').textContent = mixedStr(s[0], s[1]);
        document.getElementById('sDecimal').textContent = fmt(s[0] / s[1]);
    }

    function calcConvert() {
        var n = val('cn'), d = val('cd');
        if (n === null || d === null || d === 0) {
            document.getElementById('cDecimal').textContent = '—';
            document.getElementById('cPercent').textContent = '—';
            return;
        }
        var dec = n / d;
        document.getElementById('cDecimal').textContent = fmt(dec);
        document.getElementById('cPercent').textContent = fmt(dec * 100) + '%';
    }

    ['n1', 'd1', 'n2', 'd2'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcMain); });
    document.getElementById('fop').addEventListener('change', calcMain);
    ['sn', 'sd'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcSimplify); });
    ['cn', 'cd'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcConvert); });
    calcMain(); calcSimplify(); calcConvert();
})();
</script>
@endsection
