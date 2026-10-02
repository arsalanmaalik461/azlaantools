@extends('layouts.app')

@section('title', 'Arithmetic Sequence Calculator — Free Online Tool')
@section('meta_description', 'Find the nth term of an arithmetic sequence and any term by position')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Arithmetic Sequence Calculator</h1>
            <p class="lead small text-muted">Find the nth term of an arithmetic sequence and any term by position</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="a1" class="form-label">First term a</label><input type="number" class="form-control" id="a1" value="2" step="any"></div><div class="col-md-4"><label for="cd" class="form-label">Common difference d</label><input type="number" class="form-control" id="cd" value="3" step="any"></div><div class="col-md-4"><label for="n" class="form-label">Term number n</label><input type="number" class="form-control" id="n" value="10" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the first term a.</li>
                        <li>Enter the common difference d (the gap between terms).</li>
                        <li>Enter n.</li>
                        <li>The nth term, the sum of the first n terms and the sequence will be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> If d is negative the sequence will decrease — the formula stays the same.</p>
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

    function calc() {
        var a = v("a1"), d = v("cd"), n = Math.floor(v("n"));
        if (n < 1 || n > 10000) { bad("Enter n between 1 and 10000."); return; }
        var nth = a + (n - 1) * d;
        var sum = n / 2 * (2 * a + (n - 1) * d);
        var show = Math.min(n, 50), terms = [];
        for (var i = 0; i < show; i++) { terms.push(fmt(a + i * d)); }
        $("res").innerHTML = table([
            ["nth term: a + (n-1)d = " + fmt(a) + " + " + fmt(n - 1, 0) + " x " + fmt(d), fmt(nth)],
            ["Sum of first n terms: n/2 (2a + (n-1)d)", fmt(sum)],
            ["Average of first n terms", fmt(sum / n)],
            ["Sequence (first " + fmt(show, 0) + " terms)", terms.join(", ") + (n > show ? " ..." : "")]
        ]);
    }
  
    bind(["a1", "cd", "n"], calc);
    calc();
})();
</script>
@endsection
