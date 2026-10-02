@extends('layouts.app')

@section('title', 'Binary Calculator — Free Online Tool')
@section('meta_description', 'Add, subtract, multiply and divide binary numbers right in binary.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Binary Calculator</h1>
            <p class="lead small text-muted">Add, subtract, multiply and divide binary numbers right in binary</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-6"><label for="binA" class="form-label">First binary number</label><input type="text" class="form-control" id="binA" value="101101" step="any"></div><div class="col-md-6"><label for="binB" class="form-label">Second binary number</label><input type="text" class="form-control" id="binB" value="1101" step="any"></div><div class="col-md-6"><label for="op" class="form-label">Operation</label><select class="form-select" id="op"><option value="add">Add (+)</option><option value="sub">Subtract (-)</option><option value="mul">Multiply (x)</option><option value="div">Divide (quotient + remainder)</option></select></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter both binary numbers (only 0 and 1).</li>
                        <li>Select the operation.</li>
                        <li>The answer shows in both binary and decimal.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> You get exact answers even for large binary numbers because BigInt arithmetic runs behind the scenes. A negative result is shown in binary with a minus sign (not two's complement).</p>
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

    function parseBin(id) {
        var t = $(id).value.replace(/\s/g, "");
        if (!/^-?[01]+$/.test(t)) { return null; }
        var neg = t.charAt(0) === "-";
        var n = BigInt("0b" + (neg ? t.slice(1) : t));
        return neg ? -n : n;
    }
    function calc() {
        var a = parseBin("binA"), b = parseBin("binB"), op = $("op").value;
        if (a === null || b === null) { bad("A binary number must contain only 0 and 1."); return; }
        var decA = a.toString(), decB = b.toString(), html = "";
        if (op === "div") {
            if (b === 0n) { bad("Cannot divide by zero."); return; }
            html = table([["A (decimal)", decA], ["B (decimal)", decB], ["Quotient (binary)", (a / b).toString(2)], ["Quotient (decimal)", (a / b).toString()], ["Remainder (binary)", (a % b).toString(2)]]);
        } else {
            var r = op === "add" ? a + b : (op === "sub" ? a - b : a * b);
            html = table([["A (decimal)", decA], ["B (decimal)", decB], ["Result (binary)", r.toString(2)], ["Result (decimal)", r.toString()]]);
        }
        $("res").innerHTML = html;
    }
  
    bind(["binA", "binB", "op"], calc);
    calc();
})();
</script>
@endsection
