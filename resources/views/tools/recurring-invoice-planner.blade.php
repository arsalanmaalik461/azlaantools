@extends('layouts.app')

@section('title', 'Recurring Invoice Planner - Azlaan Tools')
@section('meta_description', 'Schedule planner for monthly repeating invoices. A calendar of due dates and a one-click next invoice draft. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Recurring Invoice Planner</h1>
            <p class="lead text-muted">A schedule for monthly/weekly repeating invoices (retainer, maintenance, subscription) — a calendar of upcoming due dates and a one-click draft of the next invoice. <strong>No auto-send happens</strong> — the draft is created only in your browser. Data is saved only in your browser, never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">New recurring profile</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rpName" class="form-label fw-semibold">Profile name</label>
                            <input type="text" class="form-control" id="rpName" placeholder="e.g. Monthly AC maintenance — Mr. Ahmed">
                        </div>
                        <div class="col-md-6">
                            <label for="rpClientPick" class="form-label fw-semibold">Client</label>
                            <select class="form-select" id="rpClientPick">
                                <option value="">— Manual client —</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpAmount" class="form-label fw-semibold">Amount per invoice (Rs)</label>
                            <input type="number" class="form-control" id="rpAmount" min="0.01" step="0.01" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpFreq" class="form-label fw-semibold">Repeat</label>
                            <select class="form-select" id="rpFreq">
                                <option value="weekly">Weekly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="quarterly">Quarterly (every 3 months)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpStart" class="form-label fw-semibold">Start date</label>
                            <input type="date" class="form-control" id="rpStart">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="rpCycles" class="form-label fw-semibold">How many times (cycles)</label>
                            <input type="number" class="form-control" id="rpCycles" min="1" max="60" value="12">
                        </div>
                        <div class="col-12">
                            <label for="rpDesc" class="form-label fw-semibold">Invoice item details</label>
                            <input type="text" class="form-control" id="rpDesc" placeholder="e.g. Monthly solar system maintenance charges">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="rpErrorBox" role="alert"></div>
                    <button type="button" class="btn btn-primary mt-3" id="rpAddBtn">Save Profile</button>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Saved profiles</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Profile</th><th>Client</th><th class="text-end">Amount</th><th>Repeat</th><th>Start</th><th>Cycles</th><th></th></tr>
                            </thead>
                            <tbody id="rpRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="rpEmpty">No recurring profiles yet.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Upcoming due dates (schedule)</h2>
                        <select class="form-select form-select-sm" id="rpHorizon" style="width:auto;">
                            <option value="6">Next 6 due dates</option>
                            <option value="12" selected>Next 12 due dates</option>
                            <option value="24">Next 24 due dates</option>
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Due date</th><th>Profile</th><th>Client</th><th class="text-end">Amount</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody id="schedRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="schedEmpty">Add a profile first to see the schedule.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the profile name, client, amount, repeat and start date, then press <strong>Save Profile</strong>.</li>
                <li>The upcoming due dates will appear in the schedule.</li>
                <li>Press <strong>Create invoice draft</strong> on any due date — the draft that opens in "Invoice Maker" is saved in "azlaan_invoices" with <em>draft</em> status, so you can check and finalize it.</li>
            </ol>
            <p class="small text-muted">Note: this tool does <strong>not send</strong> the invoice itself — it only creates a draft. Data stays only in your browser. Figures are informational.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_recurring_invoices';
    var INV_KEY = 'azlaan_invoices';
    var CLIENT_KEY = 'azlaan_clients';
    var CNT_KEY = 'azlaan_invoice_counter';

    var $ = function (id) { return document.getElementById(id); };
    var rpName = $('rpName'), rpClientPick = $('rpClientPick'),
        rpAmount = $('rpAmount'), rpFreq = $('rpFreq'), rpStart = $('rpStart'),
        rpCycles = $('rpCycles'), rpDesc = $('rpDesc'),
        errorBox = $('rpErrorBox'), rpRows = $('rpRows'), rpEmpty = $('rpEmpty'),
        schedRows = $('schedRows'), schedEmpty = $('schedEmpty'), rpHorizon = $('rpHorizon');

    function loadJson(key, fallback) {
        try { var raw = localStorage.getItem(key); if (raw) return JSON.parse(raw); } catch (e) {}
        return fallback;
    }
    function saveJson(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) {} }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function addMonths(dateStr, n) {
        var parts = dateStr.split('-');
        var d = new Date(Number(parts[0]), Number(parts[1]) - 1 + n, Number(parts[2]));
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }
    function addWeeks(dateStr, n) {
        var parts = dateStr.split('-');
        var d = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        d.setDate(d.getDate() + n * 7);
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }
    function nextDate(dateStr, freq, step) {
        if (freq === 'weekly') return addWeeks(dateStr, step);
        if (freq === 'quarterly') return addMonths(dateStr, step * 3);
        return addMonths(dateStr, step);
    }
    function freqLabel(f) {
        return f === 'weekly' ? 'Weekly' : (f === 'quarterly' ? 'Quarterly' : 'Monthly');
    }

    function loadClients() {
        var clients = loadJson(CLIENT_KEY, []);
        rpClientPick.innerHTML = '<option value="">— Manual client —</option>';
        clients.forEach(function (c, i) {
            var o = document.createElement('option');
            o.value = String(i);
            o.textContent = c.name || ('Client ' + (i + 1));
            rpClientPick.appendChild(o);
        });
        rpClientPick._clients = clients;
    }

    $('rpAddBtn').addEventListener('click', function () {
        hideError();
        var name = rpName.value.trim();
        var amount = Number(rpAmount.value) || 0;
        if (!name) { showError('Please enter the profile name.'); return; }
        if (amount <= 0) { showError('Please enter an amount more than 0.'); return; }
        if (!rpStart.value) { showError('Please select a start date.'); return; }
        var cycles = Math.max(1, Math.min(60, Number(rpCycles.value) || 12));
        var clients = rpClientPick._clients || [];
        var sel = clients[Number(rpClientPick.value)];
        var profiles = loadJson(KEY, []);
        profiles.push({
            id: 'rp' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            name: name,
            client: sel ? { name: sel.name || '', address: sel.address || '', phone: sel.phone || '' } : { name: '', address: '', phone: '' },
            amount: Math.round(amount * 100) / 100,
            freq: rpFreq.value,
            startDate: rpStart.value,
            cycles: cycles,
            desc: rpDesc.value.trim(),
            createdAt: new Date().toISOString()
        });
        saveJson(KEY, profiles);
        rpName.value = ''; rpAmount.value = ''; rpDesc.value = ''; rpClientPick.value = '';
        renderProfiles();
        renderSchedule();
    });

    function renderProfiles() {
        var profiles = loadJson(KEY, []);
        rpRows.innerHTML = '';
        rpEmpty.style.display = profiles.length ? 'none' : '';
        profiles.forEach(function (p) {
            var tr = document.createElement('tr');
            var tdN = document.createElement('td'); tdN.className = 'fw-semibold'; tdN.textContent = p.name;
            var tdC = document.createElement('td'); tdC.textContent = (p.client && p.client.name) || '—';
            var tdA = document.createElement('td'); tdA.className = 'text-end'; tdA.textContent = fmt(p.amount);
            var tdF = document.createElement('td'); tdF.textContent = freqLabel(p.freq);
            var tdS = document.createElement('td'); tdS.textContent = p.startDate;
            var tdCy = document.createElement('td'); tdCy.textContent = p.cycles;
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.setAttribute('aria-label', 'Delete profile');
            del.addEventListener('click', function () {
                if (!confirm('Delete the "' + p.name + '" profile?')) return;
                saveJson(KEY, loadJson(KEY, []).filter(function (x) { return x.id !== p.id; }));
                renderProfiles(); renderSchedule();
            });
            tdX.appendChild(del);
            tr.appendChild(tdN); tr.appendChild(tdC); tr.appendChild(tdA);
            tr.appendChild(tdF); tr.appendChild(tdS); tr.appendChild(tdCy); tr.appendChild(tdX);
            rpRows.appendChild(tr);
        });
    }

    function buildSchedule() {
        var profiles = loadJson(KEY, []);
        var list = [];
        profiles.forEach(function (p) {
            for (var i = 0; i < p.cycles; i++) {
                var d = nextDate(p.startDate, p.freq, i);
                if (d >= todayStr()) list.push({ date: d, profile: p, seq: i + 1 });
            }
        });
        list.sort(function (a, b) { return a.date < b.date ? -1 : (a.date > b.date ? 1 : 0); });
        return list;
    }

    function draftKey(pId, date) { return pId + '|' + date; }
    function draftedSet() {
        var s = {};
        loadJson(INV_KEY, []).forEach(function (inv) {
            if (inv.recurringDraft) s[inv.recurringDraft] = true;
        });
        return s;
    }

    function renderSchedule() {
        var list = buildSchedule().slice(0, Number(rpHorizon.value) || 12);
        var drafted = draftedSet();
        schedRows.innerHTML = '';
        schedEmpty.style.display = list.length ? 'none' : '';
        list.forEach(function (e) {
            var key = draftKey(e.profile.id, e.date);
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.className = 'fw-semibold'; tdD.textContent = e.date;
            var tdP = document.createElement('td'); tdP.textContent = e.profile.name + ' (#' + e.seq + ')';
            var tdC = document.createElement('td'); tdC.textContent = (e.profile.client && e.profile.client.name) || '—';
            var tdA = document.createElement('td'); tdA.className = 'text-end'; tdA.textContent = fmt(e.profile.amount);
            var tdS = document.createElement('td');
            tdS.innerHTML = drafted[key]
                ? '<span class="badge bg-success">draft created</span>'
                : (e.date < todayStr() ? '<span class="badge bg-danger">missed</span>' : '<span class="badge bg-secondary">upcoming</span>');
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            if (!drafted[key]) {
                var btn = document.createElement('button');
                btn.type = 'button'; btn.className = 'btn btn-sm btn-primary';
                btn.textContent = 'Create invoice draft';
                btn.addEventListener('click', function () { makeDraft(e.profile, e.date); });
                tdX.appendChild(btn);
            }
            tr.appendChild(tdD); tr.appendChild(tdP); tr.appendChild(tdC);
            tr.appendChild(tdA); tr.appendChild(tdS); tr.appendChild(tdX);
            schedRows.appendChild(tr);
        });
    }

    function nextInvNumber() {
        var c = Number(loadJson(CNT_KEY, 0)) || 0;
        c += 1;
        saveJson(CNT_KEY, c);
        return 'INV-' + new Date().getFullYear() + '-' + ('0000' + c).slice(-4);
    }

    function makeDraft(p, date) {
        var key = draftKey(p.id, date);
        if (draftedSet()[key]) return;
        var invs = loadJson(INV_KEY, []);
        invs.push({
            id: 'inv' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            number: nextInvNumber(),
            date: date,
            dueDate: addWeeks(date, 1),
            client: p.client || { name: '', address: '', phone: '' },
            items: [{ desc: p.desc || p.name, qty: 1, rate: p.amount, tax: 0 }],
            discount: { type: 'flat', value: 0, amount: 0 },
            subtotal: p.amount,
            taxTotal: 0,
            grandTotal: p.amount,
            notes: 'Recurring profile: ' + p.name,
            status: 'draft',
            payments: [],
            recurringDraft: key,
            createdAt: new Date().toISOString()
        });
        saveJson(INV_KEY, invs);
        renderSchedule();
        showError('');
        errorBox.textContent = 'Draft invoice created — check ' + p.name + ' (' + date + ') in "Invoice Maker" or "Invoice Payment Tracker".';
        errorBox.classList.remove('d-none', 'alert-danger');
        errorBox.classList.add('alert-success');
        setTimeout(function () {
            errorBox.classList.add('alert-danger');
            errorBox.classList.remove('alert-success');
            hideError();
        }, 4000);
    }

    rpHorizon.addEventListener('change', renderSchedule);

    rpStart.value = todayStr();
    loadClients();
    renderProfiles();
    renderSchedule();
})();
</script>
@endsection
