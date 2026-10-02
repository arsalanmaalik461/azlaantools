@extends('layouts.app')

@section('title', 'CGPA Calculator — Free Online Tool')
@section('meta_description', 'Calculate cumulative CGPA from the SGPA and credit hours of all semesters')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">CGPA Calculator</h1>
            <p class="lead small text-muted">Calculate cumulative CGPA from the SGPA and credit hours of all semesters</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-3"><label for="s1" class="form-label">Sem 1 SGPA</label><input type="number" class="form-control" id="s1" value="3.6" step="any"></div><div class="col-md-3"><label for="c1" class="form-label">Sem 1 credit hours</label><input type="number" class="form-control" id="c1" value="16" step="any"></div><div class="col-md-3"><label for="s2" class="form-label">Sem 2 SGPA</label><input type="number" class="form-control" id="s2" value="3.8" step="any"></div><div class="col-md-3"><label for="c2" class="form-label">Sem 2 credit hours</label><input type="number" class="form-control" id="c2" value="15" step="any"></div><div class="col-md-3"><label for="s3" class="form-label">Sem 3 SGPA</label><input type="number" class="form-control" id="s3" value="" step="any"></div><div class="col-md-3"><label for="c3" class="form-label">Sem 3 credit hours</label><input type="number" class="form-control" id="c3" value="" step="any"></div><div class="col-md-3"><label for="s4" class="form-label">Sem 4 SGPA</label><input type="number" class="form-control" id="s4" value="" step="any"></div><div class="col-md-3"><label for="c4" class="form-label">Sem 4 credit hours</label><input type="number" class="form-control" id="c4" value="" step="any"></div><div class="col-md-3"><label for="s5" class="form-label">Sem 5 SGPA</label><input type="number" class="form-control" id="s5" value="" step="any"></div><div class="col-md-3"><label for="c5" class="form-label">Sem 5 credit hours</label><input type="number" class="form-control" id="c5" value="" step="any"></div><div class="col-md-3"><label for="s6" class="form-label">Sem 6 SGPA</label><input type="number" class="form-control" id="s6" value="" step="any"></div><div class="col-md-3"><label for="c6" class="form-label">Sem 6 credit hours</label><input type="number" class="form-control" id="c6" value="" step="any"></div><div class="col-md-3"><label for="s7" class="form-label">Sem 7 SGPA</label><input type="number" class="form-control" id="s7" value="" step="any"></div><div class="col-md-3"><label for="c7" class="form-label">Sem 7 credit hours</label><input type="number" class="form-control" id="c7" value="" step="any"></div><div class="col-md-3"><label for="s8" class="form-label">Sem 8 SGPA</label><input type="number" class="form-control" id="s8" value="" step="any"></div><div class="col-md-3"><label for="c8" class="form-label">Sem 8 credit hours</label><input type="number" class="form-control" id="c8" value="" step="any"></div><div class="col-md-3"><label for="s9" class="form-label">Sem 9 SGPA</label><input type="number" class="form-control" id="s9" value="" step="any"></div><div class="col-md-3"><label for="c9" class="form-label">Sem 9 credit hours</label><input type="number" class="form-control" id="c9" value="" step="any"></div><div class="col-md-3"><label for="s10" class="form-label">Sem 10 SGPA</label><input type="number" class="form-control" id="s10" value="" step="any"></div><div class="col-md-3"><label for="c10" class="form-label">Sem 10 credit hours</label><input type="number" class="form-control" id="c10" value="" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the SGPA and credit hours of each semester — empty semesters are skipped automatically.</li>
                        <li>CGPA is the weighted average by credit hours, not a simple average.</li>
                        <li>A semester with more credit hours and a good SGPA will affect your CGPA more.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> Percentage conversion (CGPA x 9.5) is only a rough estimate — every university has its own formula, so confirm your university official formula for your degree.</p>
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
        var sum = 0, credits = 0, count = 0, rowsHtml = "";
        for (var i = 1; i <= 10; i++) {
            var sEl = $("s" + i), cEl = $("c" + i);
            var s = parseFloat(sEl.value), c = parseFloat(cEl.value);
            if (isFinite(s) && isFinite(c) && c > 0) {
                if (s < 0 || s > 4) { bad("Semester " + i + " SGPA must be between 0 and 4."); return; }
                sum += s * c; credits += c; count++;
                rowsHtml += "<tr><td>Semester " + i + "</td><td class=\"text-end fw-semibold\">" + fmt(s) + " x " + fmt(c, 0) + " cr = " + fmt(s * c) + "</td></tr>";
            }
        }
        if (count === 0 || credits === 0) { bad("Enter the SGPA and credit hours of at least one semester."); return; }
        var cgpa = sum / credits;
        $("res").innerHTML = "<table class=\"table table-sm align-middle mb-2\"><tbody>" + rowsHtml + "</tbody></table>" + table([
            ["Semesters counted", fmt(count, 0)],
            ["Total credit hours", fmt(credits, 0)],
            ["CGPA (weighted average)", fmtd(cgpa, 3)],
            ["Approx percentage (CGPA x 9.5)", fmt(cgpa * 9.5) + "%"]
        ]);
    }
  
    bind(["s1", "c1", "s2", "c2", "s3", "c3", "s4", "c4", "s5", "c5", "s6", "c6", "s7", "c7", "s8", "c8", "s9", "c9", "s10", "c10"], calc);
    calc();
})();
</script>
@endsection
