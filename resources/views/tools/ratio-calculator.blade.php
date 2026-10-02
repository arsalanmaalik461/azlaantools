@extends('layouts.app')

@section('title', 'Ratio Calculator — Azlaan Tools')
@section('meta_description', 'Free online ratio calculator. Simplify ratios, scale a ratio to a target total or part, and solve proportions like a:b = c:x. Instant results, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Ratio Calculator</h1>
            <p class="lead text-muted">Simplify a ratio, scale it to a total, or find the missing number (x) in a proportion. Every calculation updates live as you type.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">1. Simplify a Ratio</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label for="ra" class="form-label">First number (a)</label>
                            <input type="number" class="form-control form-control-lg" id="ra" placeholder="e.g. 12" step="any">
                        </div>
                        <div class="col-1 text-center fs-3 fw-bold pb-1">:</div>
                        <div class="col-3">
                            <label for="rb" class="form-label">Second (b)</label>
                            <input type="number" class="form-control form-control-lg" id="rb" placeholder="e.g. 18" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Simplified Ratio</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="rSimple">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="rSimpleNote">Dividing both numbers by their GCD gives the smallest form.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">2. Scale a Ratio</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-3">
                            <label for="sa" class="form-label">Ratio a</label>
                            <input type="number" class="form-control form-control-lg" id="sa" placeholder="2" step="any">
                        </div>
                        <div class="col-3">
                            <label for="sb" class="form-label">Ratio b</label>
                            <input type="number" class="form-control form-control-lg" id="sb" placeholder="3" step="any">
                        </div>
                        <div class="col-6">
                            <label for="stotal" class="form-label">Target total (total of a + b)</label>
                            <input type="number" class="form-control form-control-lg" id="stotal" placeholder="e.g. 100" step="any">
                        </div>
                    </div>
                    <div class="result-box mt-3 text-center">
                        <div class="text-muted small">Scaled parts</div>
                        <div class="fs-4 fw-bold" id="rScale">—</div>
                    </div>
                    <div class="row g-2 align-items-end mt-3">
                        <div class="col-6">
                            <label for="spart" class="form-label">Or: if the first part (a) is this…</label>
                            <input type="number" class="form-control form-control-lg" id="spart" placeholder="e.g. 40" step="any">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Then the second part (b) and total</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="rScalePart">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">3. Solve a Proportion — find x</h2>
                    <p class="small text-muted">First case: a : b = c : x</p>
                    <div class="row g-2 align-items-end">
                        <div class="col-2">
                            <label for="pa" class="form-label">a</label>
                            <input type="number" class="form-control form-control-lg" id="pa" placeholder="2" step="any">
                        </div>
                        <div class="col-2">
                            <label for="pb" class="form-label">b</label>
                            <input type="number" class="form-control form-control-lg" id="pb" placeholder="3" step="any">
                        </div>
                        <div class="col-1 text-center fs-4 pb-1">=</div>
                        <div class="col-3">
                            <label for="pc" class="form-label">c</label>
                            <input type="number" class="form-control form-control-lg" id="pc" placeholder="10" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">x =</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="rX1">—</div>
                        </div>
                    </div>
                    <p class="small text-muted mt-3">Second case: a : b = x : d</p>
                    <div class="row g-2 align-items-end">
                        <div class="col-2">
                            <label for="qa" class="form-label">a</label>
                            <input type="number" class="form-control form-control-lg" id="qa" placeholder="4" step="any">
                        </div>
                        <div class="col-2">
                            <label for="qb" class="form-label">b</label>
                            <input type="number" class="form-control form-control-lg" id="qb" placeholder="5" step="any">
                        </div>
                        <div class="col-1 text-center fs-4 pb-1">=</div>
                        <div class="col-3">
                            <label for="qd" class="form-label">d</label>
                            <input type="number" class="form-control form-control-lg" id="qd" placeholder="20" step="any">
                        </div>
                        <div class="col-4">
                            <label class="form-label">x =</label>
                            <div class="form-control form-control-lg bg-light fw-bold" id="rX2">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>A ratio shows the relation between two quantities — for example, a recipe with 2 cups of flour and 3 cups of sugar has the ratio 2:3. Simplifying a ratio is just like simplifying a fraction: divide both numbers by their largest common factor.</p>
                    <ul>
                        <li>Example 1: 12:18 simplifies to <strong>2:3</strong> (both divided by 6).</li>
                        <li>Example 2: scaling the ratio 2:3 to a total of 100 gives parts <strong>40 and 60</strong>.</li>
                        <li>Example 3: in 2:3 = 10:x, x = (3 &times; 10) &divide; 2 = <strong>15</strong>.</li>
                    </ul>
                    <p class="small text-muted mb-0">Note: the proportion formula is cross-multiplication — if a:b = c:x then x = (b &times; c) &divide; a.</p>
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
    function fmt(n) {
        if (!isFinite(n)) { return '—'; }
        return parseFloat(n.toPrecision(10)).toLocaleString('en-US', { maximumFractionDigits: 4 });
    }
    function gcd(a, b) {
        a = Math.abs(a); b = Math.abs(b);
        while (b > 1e-10) { var t = a % b; a = b; b = t; }
        return a;
    }

    function calcSimple() {
        var a = val('ra'), b = val('rb');
        var out = document.getElementById('rSimple');
        var note = document.getElementById('rSimpleNote');
        if (a === null || b === null || b === 0 || a === 0) { out.textContent = '—'; return; }
        if (Math.floor(a) === a && Math.floor(b) === b) {
            var g = gcd(a, b);
            out.textContent = fmt(a / g) + ' : ' + fmt(b / g);
            note.textContent = 'Both numbers were divided by GCD ' + fmt(g) + '.';
        } else {
            var unit = a < b ? a : b;
            out.textContent = fmt(a / unit) + ' : ' + fmt(b / unit);
            note.textContent = 'The decimal ratio is shown with the smaller number as 1, by dividing by the smaller number.';
        }
    }

    function calcScale() {
        var a = val('sa'), b = val('sb'), total = val('stotal'), part = val('spart');
        var out = document.getElementById('rScale');
        var outPart = document.getElementById('rScalePart');
        if (a === null || b === null || a + b === 0 || a <= 0 || b <= 0) {
            out.textContent = '—'; outPart.textContent = '—'; return;
        }
        if (total !== null && total > 0) {
            var pa = total * a / (a + b);
            var pb = total * b / (a + b);
            out.textContent = fmt(pa) + ' : ' + fmt(pb) + '  (total ' + fmt(pa + pb) + ')';
        } else { out.textContent = '—'; }
        if (part !== null && part > 0) {
            var other = part * b / a;
            outPart.textContent = 'b = ' + fmt(other) + '  |  total = ' + fmt(part + other);
        } else { outPart.textContent = '—'; }
    }

    function calcProp1() {
        var a = val('pa'), b = val('pb'), c = val('pc');
        var out = document.getElementById('rX1');
        if (a === null || b === null || c === null || a === 0) { out.textContent = '—'; return; }
        out.textContent = fmt((b * c) / a);
    }
    function calcProp2() {
        var a = val('qa'), b = val('qb'), d = val('qd');
        var out = document.getElementById('rX2');
        if (a === null || b === null || d === null || b === 0) { out.textContent = '—'; return; }
        out.textContent = fmt((a * d) / b);
    }

    ['ra', 'rb'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcSimple); });
    ['sa', 'sb', 'stotal', 'spart'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcScale); });
    ['pa', 'pb', 'pc'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcProp1); });
    ['qa', 'qb', 'qd'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcProp2); });
    calcSimple(); calcScale(); calcProp1(); calcProp2();
})();
</script>
@endsection
