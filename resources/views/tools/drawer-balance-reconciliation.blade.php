@extends('layouts.app')

@section('title', 'Cash Drawer Reconciliation - Azlaan Tools')
@section('meta_description', 'Compare the opening balance, todays sale and counted cash — spot any shortage or extra cash in your drawer. Free drawer reconciliation tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Cash Drawer Reconciliation</h1>
            <p class="lead text-muted">Compare the opening balance, today's cash sale and the cash in your drawer — any shortage (short) or extra (excess) cash shows up right away. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="recDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="recDate">
                        </div>
                        <div class="col-md-6">
                            <label for="recOpening" class="form-label fw-semibold">Opening balance — cash in drawer in the morning (Rs)</label>
                            <input type="number" class="form-control" id="recOpening" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="recSales" class="form-label fw-semibold">Today's cash sales — income (Rs)</label>
                            <input type="number" class="form-control" id="recSales" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="recExpenses" class="form-label fw-semibold">Cash purchases / expenses — taken from drawer (Rs)</label>
                            <input type="number" class="form-control" id="recExpenses" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="recCounted" class="form-label fw-semibold">Counted cash — cash in drawer now (Rs)</label>
                            <input type="number" class="form-control" id="recCounted" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="recNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="recNote" placeholder="e.g. evening count, counted by Ali">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mb-2" id="calcBtn">Calculate</button>
                    <button type="button" class="btn btn-success w-100" id="saveBtn">Save Session</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="resultPane" class="mt-4 d-none">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Expected cash (should be)</small><div class="fw-bold" id="rExpected">Rs 0</div></div></div></div>
                            <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Counted cash (counted)</small><div class="fw-bold" id="rCounted">Rs 0</div></div></div></div>
                            <div class="col-6 col-md-4"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Difference</small><div class="fw-bold" id="rDiff">Rs 0</div></div></div></div>
                        </div>
                        <div class="alert d-none" id="verdictBox" role="status"></div>
                        <p class="small text-muted mb-0">Formula: Expected = Opening + Cash Sales − Cash Purchases/Expenses. Difference = Counted − Expected.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Saved reconciliation sessions</h5>
                    <div id="sessionList"></div>
                    <p class="small text-muted mb-0" id="sessionEmpty">No saved sessions yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the <strong>opening balance</strong> in the morning, the day's <strong>cash sales</strong> and the <strong>expenses</strong> taken from the drawer.</li>
                <li>At the end of the day, <strong>count</strong> the drawer, enter the actual cash and click <strong>Calculate</strong>.</li>
                <li>The system will tell you: cash <strong>matches (tally)</strong>, is <strong>less (short)</strong> or is <strong>more (excess)</strong>.</li>
                <li>Use <strong>Save Session</strong> to keep a daily record.</li>
            </ol>
            <p class="text-muted small">Note: this record is saved only in your browser, nothing is uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_drawer_recon';
    var recDate = document.getElementById('recDate');
    var recOpening = document.getElementById('recOpening');
    var recSales = document.getElementById('recSales');
    var recExpenses = document.getElementById('recExpenses');
    var recCounted = document.getElementById('recCounted');
    var recNote = document.getElementById('recNote');
    var calcBtn = document.getElementById('calcBtn');
    var saveBtn = document.getElementById('saveBtn');
    var errorBox = document.getElementById('errorBox');
    var resultPane = document.getElementById('resultPane');
    var rExpected = document.getElementById('rExpected');
    var rCounted = document.getElementById('rCounted');
    var rDiff = document.getElementById('rDiff');
    var verdictBox = document.getElementById('verdictBox');
    var sessionList = document.getElementById('sessionList');
    var sessionEmpty = document.getElementById('sessionEmpty');

    var lastResult = null;

    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    recDate.value = today();

    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function num(el) {
        var v = parseFloat(el.value);
        return isNaN(v) ? 0 : v;
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function compute() {
        var opening = num(recOpening);
        var sales = num(recSales);
        var expenses = num(recExpenses);
        var counted = num(recCounted);
        var expected = opening + sales - expenses;
        var diff = counted - expected;
        return { date: recDate.value, opening: opening, sales: sales, expenses: expenses, counted: counted, expected: expected, diff: diff, note: recNote.value.trim() };
    }
    function verdictText(r) {
        var d = Math.round(r.diff * 100) / 100;
        if (d === 0) return { cls: 'alert-success', html: '<strong>✓ Cash Tally!</strong> Count and record match exactly — no shortage, no extra.' };
        if (d < 0) return { cls: 'alert-danger', html: '<strong>✗ Short: ' + fmt(Math.abs(d)) + '</strong> — the drawer has this much less cash than expected. Please check your sales and expense records again.' };
        return { cls: 'alert-warning', html: '<strong>⚠ Excess: ' + fmt(d) + '</strong> — the drawer has this much more cash than expected. Did you miss any entry?' };
    }

    calcBtn.addEventListener('click', function () {
        hideError();
        if (!recDate.value) { showError('Please select the date first.'); return; }
        var r = compute();
        lastResult = r;
        resultPane.classList.remove('d-none');
        rExpected.textContent = fmt(r.expected);
        rCounted.textContent = fmt(r.counted);
        var d = Math.round(r.diff * 100) / 100;
        rDiff.textContent = (d > 0 ? '+' : '') + fmt(d);
        rDiff.className = 'fw-bold ' + (d === 0 ? 'text-success' : (d < 0 ? 'text-danger' : 'text-warning'));
        var v = verdictText(r);
        verdictBox.className = 'alert ' + v.cls;
        verdictBox.innerHTML = v.html;
        verdictBox.classList.remove('d-none');
        resultPane.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    function loadSessions() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function saveSessions(s) {
        try { localStorage.setItem(KEY, JSON.stringify(s)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid() {
        return 'dr' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }

    saveBtn.addEventListener('click', function () {
        hideError();
        if (!recDate.value) { showError('Please select the date first.'); return; }
        var r = lastResult || compute();
        var sessions = loadSessions();
        sessions.unshift({
            id: uid(),
            savedAt: new Date().toLocaleString('en-PK'),
            data: r
        });
        if (sessions.length > 90) sessions.length = 90;
        saveSessions(sessions);
        renderSessions();
        saveBtn.textContent = 'Saved!';
        setTimeout(function () { saveBtn.textContent = 'Save Session'; }, 2000);
    });

    function renderSessions() {
        var sessions = loadSessions();
        sessionList.innerHTML = '';
        sessionEmpty.style.display = sessions.length ? 'none' : '';
        sessions.forEach(function (s) {
            var r = s.data;
            var d = Math.round((r.diff || 0) * 100) / 100;
            var badge = d === 0
                ? '<span class="badge bg-success">Tally</span>'
                : (d < 0 ? '<span class="badge bg-danger">Short ' + fmt(Math.abs(d)) + '</span>'
                         : '<span class="badge bg-warning text-dark">Excess ' + fmt(d) + '</span>');
            var div = document.createElement('div');
            div.className = 'border rounded p-2 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2';
            var left = document.createElement('div');
            left.innerHTML = '<strong>' + r.date + '</strong> ' + badge +
                '<br><small class="text-muted">Opening ' + fmt(r.opening) + ' + Sales ' + fmt(r.sales) +
                ' − Expenses ' + fmt(r.expenses) + ' = Expected ' + fmt(r.expected) +
                ' · Counted ' + fmt(r.counted) + (r.note ? ' · ' + r.note : '') + '</small>';
            var delB = document.createElement('button');
            delB.type = 'button';
            delB.className = 'btn btn-sm btn-outline-danger';
            delB.textContent = 'Delete';
            delB.addEventListener('click', function () {
                if (!confirm('Delete this session?')) return;
                saveSessions(loadSessions().filter(function (x) { return x.id !== s.id; }));
                renderSessions();
            });
            div.appendChild(left);
            div.appendChild(delB);
            sessionList.appendChild(div);
        });
    }

    renderSessions();
})();
</script>
@endsection
