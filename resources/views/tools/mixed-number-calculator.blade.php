@extends('layouts.app')

@section('title', 'Mixed Number Calculator — Free Online Tool')
@section('meta_description', 'Add, subtract, multiply and divide mixed numbers.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Mixed Number Calculator</h1>
            <p class="lead small text-muted">Choose two mixed numbers and an operation — both are first changed into improper fractions, then the operation is applied, and finally changed back into a mixed number, with all steps shown.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                                        <div class="col-md-4"><label class="form-label" for="mnW1">First mixed: whole</label><input type="number" class="form-control inp" id="mnW1" placeholder="e.g. 2" step="any" value="col-md-3"></div>                    <div class="col-md-4"><label class="form-label" for="mnN1">Numerator</label><input type="number" class="form-control inp" id="mnN1" placeholder="" step="col-md-3" value=""></div>                    <div class="col-md-4"><label class="form-label" for="mnD1">Denominator</label><input type="number" class="form-control inp" id="mnD1" placeholder="" step="col-md-3" value=""></div>                    <div class="col-md-3"><label class="form-label" for="mnOp">Operation</label><select class="form-select inp" id="mnOp"><option value="add">Add (+)</option><option value="sub">Subtract (−)</option><option value="mul">Multiply (x)</option><option value="div">Divide (÷)</option></select></div>
                    </div>
                    <div class="row g-3 mt-1">
                                        <div class="col-md-4"><label class="form-label" for="mnW2">Second mixed: whole</label><input type="number" class="form-control inp" id="mnW2" placeholder="e.g. 1" step="any" value="col-md-3"></div>                    <div class="col-md-4"><label class="form-label" for="mnN2">Numerator</label><input type="number" class="form-control inp" id="mnN2" placeholder="" step="col-md-3" value=""></div>                    <div class="col-md-4"><label class="form-label" for="mnD2">Denominator</label><input type="number" class="form-control inp" id="mnD2" placeholder="" step="col-md-3" value=""></div>
                    </div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the whole, numerator and denominator of both mixed numbers.</li><li>Select the operation.</li><li>You will get the result as a simplified mixed number.</li></ol>
                    <p class="small text-muted mb-0">Note: To divide, the second fraction is flipped (reciprocal) and then multiplied. For negative results, the sign is added at the end.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function toImp(w, n, d) { var s = w < 0 ? -1 : 1; return { n: s * (Math.abs(w) * d + n), d: d }; }
    function calc() {
        var w1 = intv("mnW1"), n1 = intv("mnN1"), d1 = intv("mnD1"), w2 = intv("mnW2"), n2 = intv("mnN2"), d2 = intv("mnD2"), op = el("mnOp").value;
        if ([w1, n1, d1, w2, n2, d2].some(function (v) { return v === null; })) { err("Please enter all 6 fields as whole numbers (0 works for whole too)."); return; }
        if (d1 === 0 || d2 === 0) { err("Denominator cannot be 0."); return; }
        if (n1 < 0 || n2 < 0) { err("Enter numerator and denominator of a mixed number as positive; put the sign on the whole number."); return; }
        var f1 = toImp(w1, n1, Math.abs(d1)), f2 = toImp(w2, n2, Math.abs(d2));
        var st = ["First number → improper: " + f1.n + "/" + f1.d, "Second number → improper: " + f2.n + "/" + f2.d];
        var rn, rd;
        if (op === "add") { rn = f1.n * f2.d + f2.n * f1.d; rd = f1.d * f2.d; st.push("Add: (" + f1.n + "x" + f2.d + " + " + f2.n + "x" + f1.d + ") / (" + f1.d + "x" + f2.d + ") = " + rn + "/" + rd); }
        else if (op === "sub") { rn = f1.n * f2.d - f2.n * f1.d; rd = f1.d * f2.d; st.push("Subtract: (" + f1.n + "x" + f2.d + " − " + f2.n + "x" + f1.d + ") / (" + f1.d + "x" + f2.d + ") = " + rn + "/" + rd); }
        else if (op === "mul") { rn = f1.n * f2.n; rd = f1.d * f2.d; st.push("Multiply: " + f1.n + "x" + f2.n + " / " + f1.d + "x" + f2.d + " = " + rn + "/" + rd); }
        else { if (f2.n === 0) { err("Cannot divide by zero — the second number is 0."); return; } rn = f1.n * f2.d; rd = f1.d * f2.n; if (rd < 0) { rd = -rd; rn = -rn; } st.push("Divide: flip the second fraction and multiply → " + rn + "/" + rd); }
        if (rn === 0) { show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Result = 0</strong></div>"); showSteps(st); return; }
        var g = gcd(rn, rd); var sn = rn / g, sd = rd / g; var sign = sn < 0 ? "-" : ""; var anw = Math.floor(Math.abs(sn) / sd), anr = Math.abs(sn) % sd;
        st.push("Simplify (GCD = " + g + "): " + sign + Math.abs(sn) + "/" + sd);
        var res = anr === 0 ? sign + anw : (anw === 0 ? sign + anr + "/" + sd : sign + anw + " " + anr + "/" + sd);
        st.push("Back to mixed number: " + res);
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Result = " + res + "</strong> &nbsp;<span class=\"fs-6\">(fraction: " + sn + "/" + sd + ", decimal: " + fmt(sn / sd, 6) + ")</span></div>"); showSteps(st);
    }
    bind(calc); calc();

})();
</script>
@endsection
