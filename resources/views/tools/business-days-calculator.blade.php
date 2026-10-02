@extends('layouts.app')
@section('title', 'Business Days Calculator — Azlaan Tools')
@section('meta_description', 'Free business days calculator. Count working days between two dates or add business days to a date, with Sat-Sun or Fri-Sat weekend options.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Business Days Calculator</h1>
            <p class="lead text-muted">Count working days between dates, or find the date after adding business days — with weekend options for Pakistan offices.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <label class="form-label" for="weekend">Weekend</label>
                <select class="form-select mb-3" id="weekend"><option value="sat-sun" selected>Saturday – Sunday</option><option value="fri-sat">Friday – Saturday</option></select>
                <h3 class="h5">Mode 1 — Days between two dates</h3>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="startDate">Start Date</label><input type="date" class="form-control" id="startDate"></div>
                    <div class="col-md-6"><label class="form-label" for="endDate">End Date</label><input type="date" class="form-control" id="endDate"></div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total Days</div><div class="fs-4 fw-bold" id="totalOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-primary text-white text-center"><div class="small">Business Days</div><div class="fs-4 fw-bold" id="bizOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Weekend Days</div><div class="fs-4 fw-bold" id="wkndOut">—</div></div></div>
                </div>
                <hr>
                <h3 class="h5">Mode 2 — Add business days</h3>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="addStart">From Date</label><input type="date" class="form-control" id="addStart"></div>
                    <div class="col-md-6"><label class="form-label" for="addDays">Business Days to Add</label><input type="number" class="form-control" id="addDays" value="10"></div>
                </div>
                <div class="border rounded p-3 bg-light text-center mt-3"><div class="text-muted small">Result Date</div><div class="fs-4 fw-bold" id="addOut">—</div></div>
                <p class="small text-muted mt-3 mb-0">Note: public holidays are not excluded — they change every year, so please subtract them manually if needed.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Choose your weekend: Sat–Sun or Fri–Sat.</li><li>Mode 1: pick start and end dates to see total, business and weekend days.</li><li>Mode 2: pick a date and number of business days to get the result date.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function parseDate(v) { if (!v) return null; var p = v.split('-'); return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)); }
    function fmt(d) { return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
    function isWeekend(d) {
        var day = d.getDay();
        if (document.getElementById('weekend').value === 'fri-sat') return day === 5 || day === 6;
        return day === 0 || day === 6;
    }
    function calcRange() {
        var s = parseDate(document.getElementById('startDate').value);
        var e = parseDate(document.getElementById('endDate').value);
        if (!s || !e || e < s) { return; }
        var total = Math.round((e - s) / 86400000) + 1;
        var biz = 0, wk = 0;
        var cur = new Date(s.getTime());
        for (var i = 0; i < total; i++) { if (isWeekend(cur)) wk++; else biz++; cur.setDate(cur.getDate() + 1); }
        document.getElementById('totalOut').textContent = total;
        document.getElementById('bizOut').textContent = biz;
        document.getElementById('wkndOut').textContent = wk;
    }
    function calcAdd() {
        var s = parseDate(document.getElementById('addStart').value);
        var n = parseInt(document.getElementById('addDays').value, 10) || 0;
        if (!s) { return; }
        var cur = new Date(s.getTime());
        var step = n >= 0 ? 1 : -1;
        var left = Math.abs(n);
        while (left > 0) { cur.setDate(cur.getDate() + step); if (!isWeekend(cur)) left--; }
        document.getElementById('addOut').textContent = fmt(cur);
    }
    function calcAll() { calcRange(); calcAdd(); }
    ['startDate','endDate','addStart','addDays','weekend'].forEach(function (id) { document.getElementById(id).addEventListener('input', calcAll); document.getElementById(id).addEventListener('change', calcAll); });
    var today = new Date();
    function iso(d) { var mm = d.getMonth() + 1, dd = d.getDate(); return d.getFullYear() + '-' + (mm < 10 ? '0' : '') + mm + '-' + (dd < 10 ? '0' : '') + dd; }
    document.getElementById('startDate').value = iso(today);
    var plus = new Date(today.getTime()); plus.setDate(plus.getDate() + 14);
    document.getElementById('endDate').value = iso(plus);
    document.getElementById('addStart').value = iso(today);
    calcAll();
})();
</script>
@endsection
