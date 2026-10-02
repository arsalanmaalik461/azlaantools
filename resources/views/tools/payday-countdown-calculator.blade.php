@extends('layouts.app')

@section('title', 'Payday Countdown Calculator — Free Online Tool')
@section('meta_description', 'Find your next payday and days remaining from weekly or monthly pay cycles.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Payday Countdown Calculator</h1>
                    <p class="lead small text-muted">Weekly, every two weeks, or monthly on a fixed date — enter your pay cycle and see the next payday, the days left, and the following paydays.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="pdCycle">Pay cycle</label><select class="form-select" id="pdCycle"><option value="monthly">Monthly (fixed date)</option><option value="weekly">Weekly</option><option value="biweekly">Every 2 weeks</option></select></div>
                        <div class="col-md-4" id="pdDayWrap"><label class="form-label" for="pdDay">Pay day of month (1 to 31)</label><input type="number" class="form-control" id="pdDay" value="1" min="1" max="31" step="1"></div>
                        <div class="col-md-4 d-none" id="pdLastWrap"><label class="form-label" for="pdLast">Last payday</label><input type="date" class="form-control" id="pdLast"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="pdOut">Set your pay cycle to see the next payday.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose monthly, weekly or every 2 weeks.</li>
                        <li>For monthly, enter the day of the month you are paid; for weekly cycles, enter your last payday.</li>
                        <li>Read the next payday, days remaining, and the next three paydays after it.</li>
                    </ol>
                    <p class="small text-muted mb-0">Monthly paydays that fall on the 29th, 30th or 31st are clamped to the last day of shorter months. The calculator counts calendar days and does not shift paydays for weekends or bank holidays, which depend on your employer.</p>
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
    el("pdLast").value = todayStr();
    function syncWrap() { var c = el("pdCycle").value; el("pdDayWrap").classList.toggle("d-none", c !== "monthly"); el("pdLastWrap").classList.toggle("d-none", c === "monthly"); }
    function addMonthsClamped(base, months, dayWanted) { var y = base.getFullYear(), m = base.getMonth() + months; var last = new Date(y, m + 1, 0).getDate(); return new Date(y, m, Math.min(dayWanted, last)); }
    function calc() {
        syncWrap();
        var out = el("pdOut");
        var today = parseD(todayStr());
        var cycle = el("pdCycle").value;
        var next = null, stepFn = null;
        if (cycle === "monthly") {
            var dayWanted = parseInt(el("pdDay").value, 10);
            if (isNaN(dayWanted) || dayWanted < 1 || dayWanted > 31) { out.textContent = "Please enter a pay day between 1 and 31."; return; }
            next = addMonthsClamped(today, 0, dayWanted);
            if (next < today) { next = addMonthsClamped(today, 1, dayWanted); }
            stepFn = function (d, k) { return addMonthsClamped(d, k, dayWanted); };
        } else {
            var last = parseD(el("pdLast").value);
            if (!last) { out.textContent = "Please enter your last payday."; return; }
            var period = cycle === "weekly" ? 7 : 14;
            var diff = Math.round((today - last) / 86400000);
            var k = diff <= 0 ? 0 : Math.ceil(diff / period);
            next = new Date(last.getTime()); next.setDate(next.getDate() + k * period);
            stepFn = function (d, kk) { var r = new Date(d.getTime()); r.setDate(r.getDate() + kk * period); return r; };
        }
        var daysLeft = Math.round((next - today) / 86400000);
        var list = "";
        for (var i = 1; i <= 3; i++) { list += "<li>" + fmtD(stepFn(next, i)) + "</li>"; }
        out.innerHTML = (daysLeft === 0 ? "<strong>Payday is today.</strong>" : "<strong>Next payday:</strong> " + fmtD(next) + " — in <strong>" + daysLeft + "</strong> days.") + "<br>Following paydays:<ul class=\"mb-0\">" + list + "</ul>";
    }
    el("pdCycle").addEventListener("change", calc);
    ["pdDay", "pdLast"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
