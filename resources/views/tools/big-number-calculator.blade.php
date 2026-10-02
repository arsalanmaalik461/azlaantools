@extends('layouts.app')

@section('title', 'Big Number Calculator — Free Online Tool')
@section('meta_description', 'Exact add, subtract, multiply and divide of very big numbers.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Big Number Calculator</h1>
            <p class="lead small text-muted">Exact add, subtract, multiply and divide of very big numbers</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-12"><label for="numA" class="form-label">First big number (digits only)</label><input type="text" class="form-control" id="numA" value="123456789012345678901234567890" step="any"></div><div class="col-12"><label for="numB" class="form-label">Second big number (digits only)</label><input type="text" class="form-control" id="numB" value="98765432109876543210" step="any"></div><div class="col-md-6"><label for="op" class="form-label">Operation</label><select class="form-select" id="op"><option value="add">Add (+)</option><option value="sub">Subtract (-)</option><option value="mul">Multiply (x)</option><option value="div">Divide (quotient + remainder)</option></select></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter very big numbers as digits — there is no practical length limit.</li>
                        <li>Select the operation.</li>
                        <li>You get the exact answer — no rounding and no scientific notation.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> This calculator uses JavaScript BigInt (arbitrary precision integer), so the answer is exact down to the last digit. Decimal (fraction) numbers are not supported here.</p>
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
    function $(id) { return document.getElementById(id); }
    function v(id) { var n = parseFloat($(id).value); return isFinite(n) ? n : 0; }
    function fmt(n, d) { if (!isFinite(n)) { return "—"; } if (d === undefined) { d = 2; } return n.toLocaleString("en-US", { maximumFractionDigits: d }); }
    function fmtd(n, d) { if (!isFinite(n)) { return "—"; } return n.toLocaleString("en-US", { maximumFractionDigits: d, minimumFractionDigits: d }); }
    function table(rowsArr) { var h = "<table class=\"table table-sm align-middle mb-0\"><tbody>"; rowsArr.forEach(function (r) { h += "<tr><td>" + r[0] + "</td><td class=\"text-end fw-semibold\">" + r[1] + "</td></tr>"; }); return h + "</tbody></table>"; }
    function bad(msg) { $("res").innerHTML = "<span class=\"text-danger fw-semibold\">" + msg + "</span>"; }
    function parseList(id) { return $(id).value.split(/[\s,;]+/).map(function (x) { return parseFloat(x); }).filter(function (x) { return isFinite(x); }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = $(id); if (el) { el.addEventListener("input", fn); el.addEventListener("change", fn); } }); }

    function parseBig(id) {
        var t = $(id).value.replace(/[\s,]/g, "");
        if (!/^-?\d+$/.test(t)) { return null; }
        return BigInt(t);
    }
    function calc() {
        var a = parseBig("numA"), b = parseBig("numB"), op = $("op").value;
        if (a === null || b === null) { bad("Enter only digits (and a minus sign at the start) in both boxes."); return; }
        var out = "";
        if (op === "add") { out = (a + b).toString(); }
        else if (op === "sub") { out = (a - b).toString(); }
        else if (op === "mul") { out = (a * b).toString(); }
        else {
            if (b === 0n) { bad("Cannot divide by zero."); return; }
            var q = a / b, r = a % b;
            $("res").innerHTML = table([["Quotient", q.toString()], ["Remainder", r.toString()], ["Quotient digits", fmt(q.toString().replace("-", "").length, 0)]]);
            return;
        }
        $("res").innerHTML = table([["Result", out], ["Result digits", fmt(out.replace("-", "").length, 0)], ["First number digits", fmt(a.toString().replace("-", "").length, 0)], ["Second number digits", fmt(b.toString().replace("-", "").length, 0)]]);
    }
  
    bind(["numA", "numB", "op"], calc);
    calc();
})();
</script>
@endsection
