@extends('layouts.app')

@section('title', 'Savings Challenge Planner - Azlaan Tools')
@section('meta_description', 'Plan a 52-week or 100-envelope savings challenge: week-by-week deposit schedule, running total and tick-off progress.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Savings Challenge Planner</h1>
            <p class="lead text-muted">Plan your full 52-week or 100-envelope savings challenge — week-by-week deposit schedule, running total and progress tick-off. Data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="chType" class="form-label fw-semibold">Challenge type</label>
                            <select class="form-select" id="chType">
                                <option value="52">52-week challenge</option>
                                <option value="100">100-envelope challenge</option>
                                <option value="custom">Custom challenge</option>
                            </select>
                            <div class="form-text">52-week: 52 weeks, one deposit each week. 100-envelope: 100 envelopes, one each day.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="chBase" class="form-label fw-semibold">Base amount (Rs)</label>
                            <input type="number" class="form-control" id="chBase" placeholder="100" min="1" step="1" value="100">
                            <div class="form-text">Step for each period — for example with 100: week 1 = 100, week 2 = 200…</div>
                        </div>
                        <div class="col-md-4">
                            <label for="chDir" class="form-label fw-semibold">Direction</label>
                            <select class="form-select" id="chDir">
                                <option value="asc">Small to big</option>
                                <option value="desc">Big to small</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="chStart" class="form-label fw-semibold">Start date</label>
                            <input type="date" class="form-control" id="chStart">
                        </div>
                        <div class="col-md-4 d-none" id="customWrap">
                            <label for="chPeriods" class="form-label fw-semibold">Periods (days)</label>
                            <input type="number" class="form-control" id="chPeriods" placeholder="30" min="1" max="365" value="30">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="genBtn">Make Plan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="planWrap" class="d-none">
                <div class="row text-center g-2 mb-3">
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Total target</small><div class="fw-bold" id="sumTotal">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Saved</small><div class="fw-bold text-success" id="sumDone">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Left</small><div class="fw-bold" id="sumLeft">-</div></div></div></div>
                    <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Progress</small><div class="fw-bold" id="sumPct">-</div></div></div></div>
                </div>
                <div class="progress mb-3" style="height: 18px;">
                    <div class="progress-bar bg-success" id="progBar" role="progressbar" style="width: 0%;">0%</div>
                </div>
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <button type="button" class="btn btn-outline-success btn-sm" id="csvBtn">Schedule CSV Download</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="resetBtn">Reset plan</button>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr><th></th><th>#</th><th>Date</th><th class="text-end">Deposit (Rs)</th><th class="text-end">Running total (Rs)</th></tr>
                                </thead>
                                <tbody id="schedBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the challenge type, base amount and start date, then press <strong>Make Plan</strong>.</li>
                <li>After saving each period's amount, tick that row's checkbox — progress is saved.</li>
                <li>To change the plan or progress, press <strong>Reset plan</strong> (makes a new plan, tick-offs are cleared).</li>
            </ol>
            <p class="text-muted small">Note: this is a savings planning tool, not financial or investment advice. Data is saved only in your browser, nothing is uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_challenge';
    var chType = document.getElementById('chType');
    var chBase = document.getElementById('chBase');
    var chDir = document.getElementById('chDir');
    var chStart = document.getElementById('chStart');
    var chPeriods = document.getElementById('chPeriods');
    var customWrap = document.getElementById('customWrap');
    var genBtn = document.getElementById('genBtn');
    var errorBox = document.getElementById('errorBox');
    var planWrap = document.getElementById('planWrap');
    var schedBody = document.getElementById('schedBody');
    var sumTotal = document.getElementById('sumTotal');
    var sumDone = document.getElementById('sumDone');
    var sumLeft = document.getElementById('sumLeft');
    var sumPct = document.getElementById('sumPct');
    var progBar = document.getElementById('progBar');
    var csvBtn = document.getElementById('csvBtn');
    var resetBtn = document.getElementById('resetBtn');

    var plan = null;   // {type, base, dir, start, periods, perDay, rows:[{n,date,dep,run}]}
    var done = {};     // n -> true

    function showError(m) { errorBox.textContent = m; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function fmt(n) { return 'Rs ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 0 }); }
    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function fmtDate(iso) {
        var parts = iso.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    function addDays(iso, days) {
        var d = new Date(iso + 'T00:00:00');
        d.setDate(d.getDate() + days);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify({ plan: plan, done: done })); } catch (e) {}
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (!raw) return;
            var s = JSON.parse(raw);
            if (s && s.plan && s.plan.rows) {
                plan = s.plan;
                done = s.done || {};
            }
        } catch (e) {}
    }

    chType.addEventListener('change', function () {
        customWrap.classList.toggle('d-none', chType.value !== 'custom');
    });

    function buildPlan() {
        hideError();
        var type = chType.value;
        var base = Number(chBase.value);
        if (!base || base <= 0) { showError('Enter a base amount above 0.'); return; }
        var start = chStart.value;
        if (!start) { showError('Select a start date.'); return; }
        var periods = type === '52' ? 52 : (type === '100' ? 100 : Number(chPeriods.value));
        if (type === 'custom' && (!periods || periods < 1 || periods > 365)) {
            showError('Enter custom periods between 1 and 365.');
            return;
        }
        var dir = chDir.value;
        var perDay = (type !== '52'); // 52-week: weekly; envelope/custom: daily
        var rows = [];
        var run = 0;
        for (var i = 1; i <= periods; i++) {
            var mult = dir === 'asc' ? i : (periods - i + 1);
            var dep = mult * base;
            run += dep;
            rows.push({ n: i, date: addDays(start, (i - 1) * (perDay ? 1 : 7)), dep: dep, run: run });
        }
        plan = { type: type, base: base, dir: dir, start: start, periods: periods, perDay: perDay, rows: rows };
        done = {};
        save();
        render();
    }

    function render() {
        if (!plan) { planWrap.classList.add('d-none'); return; }
        planWrap.classList.remove('d-none');
        var label = plan.type === '52' ? 'Week' : (plan.type === '100' ? 'Envelope' : 'Day');
        schedBody.innerHTML = '';
        var total = plan.rows[plan.rows.length - 1].run;
        var paid = 0, count = 0;
        plan.rows.forEach(function (r) {
            var tr = document.createElement('tr');
            if (done[r.n]) tr.className = 'table-success';
            var tdC = document.createElement('td');
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input';
            cb.checked = !!done[r.n];
            cb.setAttribute('aria-label', 'tick ' + label + ' ' + r.n);
            cb.addEventListener('change', function () {
                if (cb.checked) done[r.n] = true; else delete done[r.n];
                save();
                render();
            });
            tdC.appendChild(cb);
            tr.appendChild(tdC);
            var tdN = document.createElement('td'); tdN.textContent = r.n;
            var tdD = document.createElement('td'); tdD.textContent = fmtDate(r.date);
            var tdDep = document.createElement('td'); tdDep.className = 'text-end'; tdDep.textContent = fmt(r.dep);
            var tdR = document.createElement('td'); tdR.className = 'text-end text-muted'; tdR.textContent = fmt(r.run);
            tr.appendChild(tdN); tr.appendChild(tdD); tr.appendChild(tdDep); tr.appendChild(tdR);
            schedBody.appendChild(tr);
            if (done[r.n]) { paid += r.dep; count++; }
        });
        var left = total - paid;
        var pct = total > 0 ? Math.round(paid / total * 100) : 0;
        sumTotal.textContent = fmt(total);
        sumDone.textContent = fmt(paid);
        sumLeft.textContent = fmt(left);
        sumPct.textContent = pct + '% (' + count + '/' + plan.rows.length + ')';
        progBar.style.width = pct + '%';
        progBar.textContent = pct + '%';
    }

    csvBtn.addEventListener('click', function () {
        hideError();
        if (!plan) return;
        var rows = ['#,' + 'Date,Deposit (Rs),Running Total (Rs),Done'];
        plan.rows.forEach(function (r) {
            rows.push([r.n, r.date, r.dep, r.run, done[r.n] ? 'yes' : 'no'].join(','));
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'savings-challenge-plan.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    resetBtn.addEventListener('click', function () {
        if (!confirm('Reset the plan and tick-off progress?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        plan = null; done = {};
        render();
    });

    genBtn.addEventListener('click', buildPlan);

    chStart.value = today();
    load();
    render();
})();
</script>
@endsection
