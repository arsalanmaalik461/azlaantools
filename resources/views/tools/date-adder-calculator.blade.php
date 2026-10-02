@extends('layouts.app')

@section('title', 'Date Adder Calculator — Free Online Tool')
@section('meta_description', 'Add or subtract days, weeks, months and years from any date online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Date Adder Calculator</h1>
                    <p class="lead small text-muted">Pick a starting date, choose add or subtract, and enter days, weeks, months and years — the result date handles month ends and leap years correctly.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="daDate">Start date</label><input type="date" class="form-control" id="daDate"></div>
                        <div class="col-md-4"><label class="form-label" for="daOp">Operation</label><select class="form-select" id="daOp"><option value="add">Add</option><option value="sub">Subtract</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="daYears">Years</label><input type="number" class="form-control" id="daYears" value="0" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="daMonths">Months</label><input type="number" class="form-control" id="daMonths" value="0" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="daWeeks">Weeks</label><input type="number" class="form-control" id="daWeeks" value="0" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="daDays">Days</label><input type="number" class="form-control" id="daDays" value="30" min="0" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="daOut">Pick a date to see the result.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick the start date (today is prefilled).</li>
                        <li>Choose Add or Subtract and enter any mix of years, months, weeks and days.</li>
                        <li>Read the resulting date with its day of the week.</li>
                    </ol>
                    <p class="small text-muted mb-0">Months and years are added on the calendar first, with the day clamped to the last day of the target month (for example 31 January plus 1 month becomes 28 or 29 February), then weeks and days are added. All math uses the local calendar with no time zone shifting.</p>
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
    function parseD(v) { if (!v) { return null; } var p = v.split("-"); var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)); return isNaN(d.getTime()) ? null : d; }
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    function fmtD(d) { return d.toLocaleDateString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric" }); }
    el("daDate").value = todayStr();
    function calc() {
        var base = parseD(el("daDate").value);
        var out = el("daOut");
        if (!base) { out.textContent = "Please pick a valid start date."; return; }
        var sign = el("daOp").value === "add" ? 1 : -1;
        function iv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) || v < 0 ? 0 : v; }
        var r = new Date(base.getTime());
        var totalMonths = sign * (iv("daYears") * 12 + iv("daMonths"));
        var day = r.getDate();
        r.setDate(1);
        r.setMonth(r.getMonth() + totalMonths);
        var lastDay = new Date(r.getFullYear(), r.getMonth() + 1, 0).getDate();
        r.setDate(Math.min(day, lastDay));
        r.setDate(r.getDate() + sign * (iv("daWeeks") * 7 + iv("daDays")));
        var diffDays = Math.round((r - base) / 86400000);
        out.innerHTML = "<strong>Result date:</strong> " + fmtD(r) + "<br>Total change: " + diffDays.toLocaleString("en-US") + " days from the start date.";
    }
    ["daDate", "daOp", "daYears", "daMonths", "daWeeks", "daDays"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
