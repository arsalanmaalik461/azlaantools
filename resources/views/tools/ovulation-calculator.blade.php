@extends('layouts.app')
@section('title', 'Ovulation Calculator — Azlaan Tools')
@section('meta_description', 'Free ovulation and fertile window calculator. Enter your last period date and cycle length to find ovulation day, fertile days and next period.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Ovulation Calculator</h1>
            <p class="lead text-muted">Estimate your ovulation day, fertile window and next period from your last period — simple, private and free.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="lmp">First Day of Last Period</label><input type="date" class="form-control" id="lmp"></div>
                    <div class="col-md-6"><label class="form-label" for="periodLen">Period Length (days)</label><input type="number" class="form-control" id="periodLen" value="5" min="2" max="10"></div>
                    <div class="col-12"><label class="form-label" for="cycle">Cycle Length: <strong id="cycleVal">28 days</strong></label><input type="range" class="form-range" id="cycle" min="21" max="35" value="28"></div>
                </div>
                <div class="row g-3 mt-2 d-none" id="results">
                    <div class="col-md-4"><div class="border rounded p-3 bg-success text-white text-center"><div class="small">Ovulation Day</div><div class="fs-5 fw-bold" id="ovOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Fertile Window</div><div class="fs-6 fw-bold" id="fertOut">—</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Next Period</div><div class="fs-5 fw-bold" id="nextOut">—</div></div></div>
                    <div class="col-12"><ul class="list-group" id="monthList"></ul></div>
                </div>
                <p class="small text-muted mt-3 mb-0">This is an estimate for information only — not medical advice, and not a contraception method. Cycles vary; confirm important decisions with your doctor.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Enter the first day of your last period.</li><li>Slide the cycle length to your usual cycle (21–35 days) and set period length.</li><li>See your ovulation day, fertile window and next period instantly.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function fmt(d) { return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }); }
    function addDays(d, n) { var x = new Date(d.getTime()); x.setDate(x.getDate() + n); return x; }
    function calc() {
        var cycle = parseInt(document.getElementById('cycle').value, 10) || 28;
        document.getElementById('cycleVal').textContent = cycle + ' days';
        var v = document.getElementById('lmp').value;
        var res = document.getElementById('results');
        if (!v) { res.classList.add('d-none'); return; }
        var p = v.split('-');
        var lmp = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
        var periodLen = parseInt(document.getElementById('periodLen').value, 10) || 5;
        var ov = addDays(lmp, cycle - 14);
        var fertStart = addDays(ov, -5);
        var next = addDays(lmp, cycle);
        document.getElementById('ovOut').textContent = fmt(ov);
        document.getElementById('fertOut').textContent = fmt(fertStart) + ' to ' + fmt(ov);
        document.getElementById('nextOut').textContent = fmt(next);
        var rows = [
            ['Period starts', fmt(lmp) + ' (' + periodLen + ' days)'],
            ['Fertile window opens', fmt(fertStart)],
            ['Ovulation (most fertile)', fmt(ov)],
            ['Fertile window closes', fmt(addDays(ov, 1))],
            ['Next period expected', fmt(next)]
        ];
        var html = '';
        rows.forEach(function (r) { html += '<li class="list-group-item d-flex justify-content-between"><span>' + r[0] + '</span><strong>' + r[1] + '</strong></li>'; });
        document.getElementById('monthList').innerHTML = html;
        res.classList.remove('d-none');
    }
    ['lmp','cycle','periodLen'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); document.getElementById(id).addEventListener('change', calc); });
    calc();
})();
</script>
@endsection
