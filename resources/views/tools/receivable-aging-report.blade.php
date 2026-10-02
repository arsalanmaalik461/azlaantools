@extends('layouts.app')

@section('title', 'Receivables Aging Report - Azlaan Tools')
@section('meta_description', 'Customer credit amounts in 30/60/90+ day buckets — an aging report for collection and recovery.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Receivables Aging Report</h1>
            <p class="lead text-muted">Customer credit amounts in 30 / 60 / 90+ day buckets. Take quick action on old credit. Data is saved only in your browser, never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add a credit entry</h5>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="agParty" class="form-label fw-semibold">Party / customer name</label>
                            <input type="text" class="form-control" id="agParty" placeholder="e.g. Ahmed Store">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="agAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="agAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="agDate" class="form-label fw-semibold">Credit date</label>
                            <input type="date" class="form-control" id="agDate">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="addAgBtn">Add</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="agError" role="alert"></div>
                </div>
            </div>

            <div class="row g-3 mb-4" id="bucketCards">
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center border-success"><div class="card-body py-2"><small class="text-muted">0–30 days</small><div class="fw-bold fs-5 text-success" id="bk1">Rs 0</div><div class="small text-muted" id="bk1c">0 entries</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center border-warning"><div class="card-body py-2"><small class="text-muted">31–60 days</small><div class="fw-bold fs-5 text-warning" id="bk2">Rs 0</div><div class="small text-muted" id="bk2c">0 entries</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center border-warning"><div class="card-body py-2"><small class="text-muted">61–90 days</small><div class="fw-bold fs-5 text-warning" id="bk3">Rs 0</div><div class="small text-muted" id="bk3c">0 entries</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm text-center border-danger"><div class="card-body py-2"><small class="text-muted">90+ days</small><div class="fw-bold fs-5 text-danger" id="bk4">Rs 0</div><div class="small text-muted" id="bk4c">0 entries</div></div></div></div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Aging detail <small class="text-muted">(oldest first)</small></h5>
                        <button type="button" class="btn btn-outline-success btn-sm" id="agCsv">CSV Download</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>Party</th><th>Date</th><th class="text-end">Days</th><th class="text-end">Bucket</th><th class="text-end">Amount</th><th></th></tr>
                            </thead>
                            <tbody id="agRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="agEmpty">No entries yet. Add one above.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Party-wise total</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Party</th><th class="text-end">Total due</th><th class="text-end">Oldest entry</th></tr>
                            </thead>
                            <tbody id="agPartyRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="agPartyEmpty">No entries yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the party name, credit amount and date, then press <strong>Add</strong>.</li>
                <li>The report splits into 4 buckets automatically. Contact the customer at once for amounts in the 90+ day bucket.</li>
                <li>When you get the payment, mark the entry as <strong>Received</strong> to remove it.</li>
            </ol>
            <p class="text-muted small">Note: data stays safe in this same browser. Days are counted from today.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_recv_aging';
    var agParty = document.getElementById('agParty');
    var agAmount = document.getElementById('agAmount');
    var agDate = document.getElementById('agDate');
    var addAgBtn = document.getElementById('addAgBtn');
    var agError = document.getElementById('agError');
    var agRows = document.getElementById('agRows');
    var agEmpty = document.getElementById('agEmpty');
    var agPartyRows = document.getElementById('agPartyRows');
    var agPartyEmpty = document.getElementById('agPartyEmpty');
    var agCsv = document.getElementById('agCsv');
    var bk1 = document.getElementById('bk1');
    var bk2 = document.getElementById('bk2');
    var bk3 = document.getElementById('bk3');
    var bk4 = document.getElementById('bk4');
    var bk1c = document.getElementById('bk1c');
    var bk2c = document.getElementById('bk2c');
    var bk3c = document.getElementById('bk3c');
    var bk4c = document.getElementById('bk4c');

    var entries = [];
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) entries = p; }
    } catch (e) { entries = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(entries)); } catch (e) {}
    }
    function uid() {
        return 'a' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36);
    }
    function showError(msg) {
        agError.textContent = msg;
        agError.classList.remove('d-none');
    }
    function hideError() {
        agError.classList.add('d-none');
        agError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function ageDays(dateStr) {
        var a = new Date(dateStr + 'T00:00:00');
        var b = new Date(todayStr() + 'T00:00:00');
        var diff = Math.round((b - a) / 86400000);
        return diff < 0 ? 0 : diff;
    }
    function bucketOf(days) {
        if (days <= 30) return 1;
        if (days <= 60) return 2;
        if (days <= 90) return 3;
        return 4;
    }
    function bucketLabel(b) {
        return b === 1 ? '0–30' : (b === 2 ? '31–60' : (b === 3 ? '61–90' : '90+'));
    }
    function bucketClass(b) {
        return b === 1 ? 'bg-success' : (b === 4 ? 'bg-danger' : 'bg-warning text-dark');
    }

    function render() {
        hideError();
        agRows.innerHTML = '';
        agEmpty.style.display = entries.length ? 'none' : '';
        var totals = { 1: 0, 2: 0, 3: 0, 4: 0 };
        var counts = { 1: 0, 2: 0, 3: 0, 4: 0 };
        var ordered = entries.slice().sort(function (a, b) {
            return ageDays(b.date) - ageDays(a.date);
        });
        ordered.forEach(function (e) {
            var days = ageDays(e.date);
            var b = bucketOf(days);
            totals[b] += e.amount;
            counts[b]++;
            var tr = document.createElement('tr');
            if (b === 4) tr.classList.add('table-danger');
            var tdP = document.createElement('td');
            tdP.innerHTML = '<strong>' + esc(e.party) + '</strong>';
            var tdD = document.createElement('td');
            tdD.textContent = e.date;
            var tdA = document.createElement('td');
            tdA.className = 'text-end';
            tdA.textContent = days;
            var tdB = document.createElement('td');
            tdB.className = 'text-end';
            var badge = document.createElement('span');
            badge.className = 'badge ' + bucketClass(b);
            badge.textContent = bucketLabel(b) + ' days';
            tdB.appendChild(badge);
            var tdR = document.createElement('td');
            tdR.className = 'text-end fw-bold';
            tdR.textContent = fmt(e.amount);
            var tdX = document.createElement('td');
            tdX.className = 'text-end';
            var got = document.createElement('button');
            got.type = 'button';
            got.className = 'btn btn-sm btn-outline-success';
            got.textContent = 'Received';
            got.title = 'Payment received — remove the entry';
            got.addEventListener('click', function () {
                if (!confirm(e.party + ' paid ' + fmt(e.amount) + '? Remove the entry.')) return;
                entries = entries.filter(function (x) { return x.id !== e.id; });
                save(); render();
            });
            tdX.appendChild(got);
            tr.appendChild(tdP); tr.appendChild(tdD); tr.appendChild(tdA); tr.appendChild(tdB); tr.appendChild(tdR); tr.appendChild(tdX);
            agRows.appendChild(tr);
        });
        bk1.textContent = fmt(totals[1]); bk1c.textContent = counts[1] + ' entries';
        bk2.textContent = fmt(totals[2]); bk2c.textContent = counts[2] + ' entries';
        bk3.textContent = fmt(totals[3]); bk3c.textContent = counts[3] + ' entries';
        bk4.textContent = fmt(totals[4]); bk4c.textContent = counts[4] + ' entries';

        agPartyRows.innerHTML = '';
        agPartyEmpty.style.display = entries.length ? 'none' : '';
        var byParty = {};
        entries.forEach(function (e) {
            if (!byParty[e.party]) byParty[e.party] = { total: 0, oldest: 0 };
            byParty[e.party].total += e.amount;
            var d = ageDays(e.date);
            if (d > byParty[e.party].oldest) byParty[e.party].oldest = d;
        });
        var parties = Object.keys(byParty).sort(function (a, b) { return byParty[b].total - byParty[a].total; });
        parties.forEach(function (name) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.innerHTML = '<strong>' + esc(name) + '</strong>';
            var tdT = document.createElement('td');
            tdT.className = 'text-end fw-bold';
            tdT.textContent = fmt(byParty[name].total);
            var tdO = document.createElement('td');
            tdO.className = 'text-end';
            tdO.textContent = byParty[name].oldest + ' days';
            tr.appendChild(tdN); tr.appendChild(tdT); tr.appendChild(tdO);
            agPartyRows.appendChild(tr);
        });
    }

    addAgBtn.addEventListener('click', function () {
        hideError();
        var party = agParty.value.trim();
        var amount = parseFloat(agAmount.value);
        var date = agDate.value || todayStr();
        if (!party) { showError('Please enter the party name.'); return; }
        if (isNaN(amount) || amount <= 0) { showError('Please enter an amount more than 0.'); return; }
        if (date > todayStr()) { showError('The date cannot be after today.'); return; }
        entries.push({
            id: uid(),
            party: party,
            amount: Math.round(amount * 100) / 100,
            date: date
        });
        save();
        agParty.value = ''; agAmount.value = ''; agDate.value = todayStr();
        agParty.focus();
        render();
    });

    agCsv.addEventListener('click', function () {
        hideError();
        if (!entries.length) { showError('Add an entry first to download the CSV.'); return; }
        var lines = ['Party,Date,Age Days,Bucket,Amount'];
        entries.slice().sort(function (a, b) { return ageDays(b.date) - ageDays(a.date); }).forEach(function (e) {
            var nm = '"' + e.party.replace(/"/g, '""') + '"';
            lines.push([nm, e.date, ageDays(e.date), bucketLabel(bucketOf(ageDays(e.date))), e.amount].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'receivables-aging-report.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    agDate.value = todayStr();
    render();
})();
</script>
@endsection
