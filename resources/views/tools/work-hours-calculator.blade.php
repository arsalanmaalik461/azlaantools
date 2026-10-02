@extends('layouts.app')

@section('title', 'Work Hours Calculator — Free Online Tool')
@section('meta_description', 'Add clock in, clock out and break times to get daily and weekly work hours.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Work Hours Calculator</h1>
                    <p class="lead small text-muted">Fill the week row by row — clock in, clock out and break — and get daily totals, the weekly total, overtime beyond your threshold, and pay at your hourly rate.</p>
                    <div class="table-responsive"><table class="table table-sm align-middle mb-2"><thead><tr><th>Day</th><th>Clock in</th><th>Clock out</th><th>Break (min)</th><th>Hours</th></tr></thead><tbody id="whBody"></tbody></table></div>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="whOt">Overtime threshold (hours per day)</label><input type="number" class="form-control" id="whOt" value="8" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="whRate">Hourly rate (Rs, optional)</label><input type="number" class="form-control" id="whRate" value="0" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="whOtMult">Overtime multiplier</label><input type="number" class="form-control" id="whOtMult" value="1.5" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="whOut">Enter clock in and clock out times to total your hours.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>For each day worked, enter clock in, clock out and break minutes.</li>
                        <li>Set the daily overtime threshold and, if you want pay, your hourly rate.</li>
                        <li>Read the weekly total, overtime hours and estimated gross pay.</li>
                    </ol>
                    <p class="small text-muted mb-0">A clock out earlier than clock in is treated as the next day (overnight shift). Pay is a simple estimate: regular hours at the base rate and overtime hours at the base rate times the multiplier, before any tax or deductions.</p>
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
    function el(id) { return document.getElementById(id); }
    var days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    var body = el("whBody");
    days.forEach(function (d, i) {
        var tr = document.createElement("tr");
        tr.innerHTML = "<td>" + d + "</td><td><input type=\"time\" class=\"form-control form-control-sm wh-in\"></td><td><input type=\"time\" class=\"form-control form-control-sm wh-out\"></td><td><input type=\"number\" class=\"form-control form-control-sm wh-brk\" value=\"0\" min=\"0\"></td><td class=\"wh-hrs fw-semibold\">—</td>";
        body.appendChild(tr);
    });
    function toMin(t) { if (!t) { return null; } var p = t.split(":"); return parseInt(p[0], 10) * 60 + parseInt(p[1], 10); }
    function calc() {
        var rows = body.querySelectorAll("tr");
        var otThresh = parseFloat(el("whOt").value); if (isNaN(otThresh) || otThresh < 0) { otThresh = 8; }
        var rate = parseFloat(el("whRate").value); if (isNaN(rate) || rate < 0) { rate = 0; }
        var mult = parseFloat(el("whOtMult").value); if (isNaN(mult) || mult <= 0) { mult = 1.5; }
        var totalMin = 0, otMin = 0;
        for (var i = 0; i < rows.length; i++) {
            var s = toMin(rows[i].querySelector(".wh-in").value), e = toMin(rows[i].querySelector(".wh-out").value);
            var brk = parseInt(rows[i].querySelector(".wh-brk").value, 10); if (isNaN(brk) || brk < 0) { brk = 0; }
            var cell = rows[i].querySelector(".wh-hrs");
            if (s === null || e === null) { cell.textContent = "—"; continue; }
            if (e < s) { e += 1440; }
            var net = e - s - brk;
            if (net < 0) { net = 0; }
            totalMin += net;
            otMin += Math.max(0, net - otThresh * 60);
            cell.textContent = (net / 60).toFixed(2) + " h";
        }
        var regMin = totalMin - otMin;
        var html = "<strong>Week total:</strong> " + (totalMin / 60).toFixed(2) + " hours (" + totalMin + " minutes) &nbsp; <strong>Regular:</strong> " + (regMin / 60).toFixed(2) + " h &nbsp; <strong>Overtime:</strong> " + (otMin / 60).toFixed(2) + " h";
        if (rate > 0) { var pay = (regMin / 60) * rate + (otMin / 60) * rate * mult; html += "<br><strong>Estimated gross pay:</strong> Rs " + Math.round(pay).toLocaleString("en-PK"); }
        el("whOut").innerHTML = html;
    }
    body.addEventListener("input", calc);
    ["whOt", "whRate", "whOtMult"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
