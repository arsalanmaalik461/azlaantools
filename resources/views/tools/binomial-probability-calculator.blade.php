@extends('layouts.app')

@section('title', 'Binomial Probability Calculator — Free Online Tool')
@section('meta_description', 'Find the probability of exactly or at most successes with the binomial distribution')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Binomial Probability Calculator</h1>
            <p class="lead small text-muted">Find the probability of exactly or at most successes with the binomial distribution</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="n" class="form-label">Trials n</label><input type="number" class="form-control" id="n" value="10" step="any"></div><div class="col-md-4"><label for="p" class="form-label">Success probability p (0 to 1)</label><input type="number" class="form-control" id="p" value="0.5" step="any"></div><div class="col-md-4"><label for="k" class="form-label">Successes k</label><input type="number" class="form-control" id="k" value="3" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the number of trials n.</li>
                        <li>Enter the success probability p for one trial as a decimal (50% = 0.5).</li>
                        <li>Enter the number of successes k.</li>
                        <li>The exactly, at most and at least probabilities will show below.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Formula: P(X = k) = C(n, k) x p^k x (1-p)^(n-k). The calculation uses a stable recurrence so it does not overflow even for large n.</p>
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
        var n = Math.floor(v("n")), p = v("p"), k = Math.floor(v("k"));
        if (n < 1 || n > 1000 || p < 0 || p > 1 || k < 0 || k > n) { bad("Please enter valid values: n (1-1000), p (0-1), k (0-n)."); return; }
        var pmf = [], i;
        if (p === 0) { for (i = 0; i <= n; i++) { pmf.push(i === 0 ? 1 : 0); } }
        else if (p === 1) { for (i = 0; i <= n; i++) { pmf.push(i === n ? 1 : 0); } }
        else {
            pmf.push(Math.pow(1 - p, n));
            for (i = 0; i < n; i++) { pmf.push(pmf[i] * (n - i) / (i + 1) * p / (1 - p)); }
        }
        var exact = pmf[k], atMost = 0, atLeast = 0;
        for (i = 0; i <= n; i++) { if (i <= k) { atMost += pmf[i]; } if (i >= k) { atLeast += pmf[i]; } }
        var mean = n * p, sd = Math.sqrt(n * p * (1 - p));
        function pc(x) { return fmtd(x * 100, 4) + "% (" + fmtd(x, 6) + ")"; }
        $("res").innerHTML = table([
            ["P(X = " + fmt(k, 0) + ") exactly", pc(exact)],
            ["P(X <= " + fmt(k, 0) + ") at most", pc(atMost)],
            ["P(X >= " + fmt(k, 0) + ") at least", pc(atLeast)],
            ["Mean (np)", fmt(mean)],
            ["Standard deviation", fmt(sd)],
            ["Most likely outcome (mode)", fmt(Math.floor((n + 1) * p), 0)]
        ]);
    }
  
    bind(["n", "p", "k"], calc);
    calc();
})();
</script>
@endsection
