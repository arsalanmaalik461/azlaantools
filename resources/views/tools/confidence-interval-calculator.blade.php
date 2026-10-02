@extends('layouts.app')

@section('title', 'Confidence Interval Calculator — Free Online Tool')
@section('meta_description', 'Calculate the 90, 95 or 99 percent confidence interval for a mean')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Confidence Interval Calculator</h1>
            <p class="lead small text-muted">Calculate the 90, 95 or 99 percent confidence interval for a mean</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="mean" class="form-label">Sample mean</label><input type="number" class="form-control" id="mean" value="100" step="any"></div><div class="col-md-4"><label for="sd" class="form-label">Standard deviation</label><input type="number" class="form-control" id="sd" value="15" step="any"></div><div class="col-md-4"><label for="n" class="form-label">Sample size n</label><input type="number" class="form-control" id="n" value="64" step="any"></div><div class="col-md-4"><label for="level" class="form-label">Confidence level</label><select class="form-select" id="level"><option value="1.645">90% (z = 1.645)</option><option value="1.96">95% (z = 1.96)</option><option value="2.576">99% (z = 2.576)</option></select></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the sample mean, standard deviation and sample size.</li>
                        <li>Select a confidence level — 95% is the most common in research.</li>
                        <li>The margin of error and both interval limits are shown below.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> This is a Z interval — when the population standard deviation is known, or the sample is large (n &gt;= 30), this is the standard method. For small samples (n &lt; 30) with unknown sigma, use the t interval, whose critical values come from the t table.</p>
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
        var mean = v("mean"), sd = v("sd"), n = v("n"), z = v("level");
        if (sd <= 0 || n < 1) { bad("Please enter a valid standard deviation and sample size."); return; }
        var se = sd / Math.sqrt(n), m = z * se;
        $("res").innerHTML = table([
            ["Standard error = sd / sqrt(n)", fmt(se, 4)],
            ["Margin of error = z x SE", fmt(m, 4)],
            ["Confidence interval", fmtd(mean - m, 4) + " to " + fmtd(mean + m, 4)],
            ["Interval width", fmt(2 * m, 4)],
            ["Note","A larger sample narrows the interval — with a 4 times larger sample, the margin is halved"]
        ]);
    }
  
    bind(["mean", "sd", "n", "level"], calc);
    calc();
})();
</script>
@endsection
