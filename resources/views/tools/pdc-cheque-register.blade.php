@extends('layouts.app')

@section('title', 'Post-Dated Cheque Register - Azlaan Tools')
@section('meta_description', 'Free post-dated cheque register. Keep a record of issued PDC cheques and get reminders before maturity.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Post-Dated Cheque Register</h1>
            <p class="lead text-muted">Keep a record of issued post-dated cheques — with reminders before maturity so no cheque is missed. Data is saved only in your browser.</p>

            <div id="alertPane"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new cheque</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="chqParty" class="form-label fw-semibold">Party name</label>
                            <input type="text" class="form-control" id="chqParty" placeholder="The person you gave the cheque to">
                        </div>
                        <div class="col-md-4">
                            <label for="chqNo" class="form-label fw-semibold">Cheque number</label>
                            <input type="text" class="form-control" id="chqNo" placeholder="Cheque no.">
                        </div>
                        <div class="col-md-4">
                            <label for="chqBank" class="form-label fw-semibold">Bank</label>
                            <input type="text" class="form-control" id="chqBank" placeholder="Bank name">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="chqAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="chqAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="chqIssue" class="form-label fw-semibold">Issue date</label>
                            <input type="date" class="form-control" id="chqIssue">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="chqMaturity" class="form-label fw-semibold">Maturity date</label>
                            <input type="date" class="form-control" id="chqMaturity">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="chqStatus" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="chqStatus">
                                <option value="pending">Pending</option>
                                <option value="cleared">Cleared</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="chqNote" class="form-label fw-semibold">Note (optional)</label>
                        <input type="text" class="form-control" id="chqNote" placeholder="e.g. Supplier payment, stock installment">
                    </div>
                    <button type="button" class="btn btn-primary" id="addChqBtn">Add Cheque</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="row text-center g-3 mb-4">
                <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Pending Cheques</small><div class="fw-bold" id="sumPending">0</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Pending Amount</small><div class="fw-bold text-warning" id="sumPendingAmt">Rs 0</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Cleared</small><div class="fw-bold text-success" id="sumCleared">0</div></div></div></div>
                <div class="col-6 col-md-3"><div class="card bg-light"><div class="card-body py-2"><small class="text-muted">Bounced</small><div class="fw-bold text-danger" id="sumBounced">0</div></div></div></div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Cheque register</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Maturity</th><th>Party</th><th>Cheque No.</th><th>Bank</th><th class="text-end">Amount</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody id="chqRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="chqEmpty">No cheques yet. Add one from above.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the cheque details and press <strong>Add Cheque</strong>.</li>
                <li>Cheques whose maturity is near or has passed will appear in the <strong>reminder</strong> above.</li>
                <li>If a cheque clears, mark it <strong>Cleared</strong> in the row; if it bounces, mark it <strong>Bounced</strong>.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser, it is not uploaded anywhere. Clearing browser data will delete this record — take a backup from time to time with the backup tool below.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_pdc_cheques';
    var chqParty = document.getElementById('chqParty');
    var chqNo = document.getElementById('chqNo');
    var chqBank = document.getElementById('chqBank');
    var chqAmount = document.getElementById('chqAmount');
    var chqIssue = document.getElementById('chqIssue');
    var chqMaturity = document.getElementById('chqMaturity');
    var chqStatus = document.getElementById('chqStatus');
    var chqNote = document.getElementById('chqNote');
    var addChqBtn = document.getElementById('addChqBtn');
    var errorBox = document.getElementById('errorBox');
    var chqRows = document.getElementById('chqRows');
    var chqEmpty = document.getElementById('chqEmpty');
    var alertPane = document.getElementById('alertPane');

    var data = { cheques: [] };
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.cheques) data = parsed;
        }
    } catch (e) { data = { cheques: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 'q' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function todayYmd() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function addDaysYmd(n) {
        var d = new Date();
        d.setDate(d.getDate() + n);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function daysLeft(maturity) {
        if (!maturity) return null;
        var t = new Date(todayYmd());
        var m2 = new Date(maturity);
        return Math.round((m2 - t) / 86400000);
    }
    function statusBadge(s) {
        if (s === 'cleared') return '<span class="badge bg-success">Cleared</span>';
        if (s === 'bounced') return '<span class="badge bg-danger">Bounced</span>';
        return '<span class="badge bg-warning text-dark">Pending</span>';
    }

    function renderAlerts() {
        var today = todayYmd();
        var week = addDaysYmd(7);
        var overdue = [], upcoming = [];
        data.cheques.forEach(function (c) {
            if (c.status !== 'pending' || !c.maturity) return;
            if (c.maturity < today) overdue.push(c);
            else if (c.maturity <= week) upcoming.push(c);
        });
        var html = '';
        overdue.forEach(function (c) {
            html += '<div class="alert alert-danger" role="alert"><strong>Maturity has passed!</strong> ' +
                esc(c.party) + ' — Cheque ' + esc(c.no || '-') + ' (' + fmt(c.amount) + ') matured on ' +
                esc(c.maturity) + '. Please follow up now.</div>';
        });
        upcoming.forEach(function (c) {
            var dl = daysLeft(c.maturity);
            html += '<div class="alert alert-warning" role="alert"><strong>Reminder:</strong> ' +
                esc(c.party) + ' — Cheque ' + esc(c.no || '-') + ' (' + fmt(c.amount) + ') matures ' +
                (dl === 0 ? 'today' : 'in ' + dl + ' days') + ' (' + esc(c.maturity) + ').</div>';
        });
        alertPane.innerHTML = html;
    }

    function renderTable() {
        var pend = 0, pendAmt = 0, cleared = 0, bounced = 0;
        var sorted = data.cheques.slice().sort(function (a, b) {
            var am = a.maturity || '9999', bm = b.maturity || '9999';
            return am < bm ? -1 : (am > bm ? 1 : 0);
        });
        chqRows.innerHTML = '';
        sorted.forEach(function (c) {
            if (c.status === 'pending') { pend++; pendAmt += c.amount; }
            else if (c.status === 'cleared') cleared++;
            else if (c.status === 'bounced') bounced++;
            var tr = document.createElement('tr');
            var tdM = document.createElement('td');
            var dl = daysLeft(c.maturity);
            tdM.innerHTML = esc(c.maturity || '-') + (c.status === 'pending' && dl !== null && dl < 0 ? ' <span class="badge bg-danger">Overdue</span>' : '');
            var tdP = document.createElement('td');
            tdP.innerHTML = esc(c.party) + (c.note ? '<br><small class="text-muted">' + esc(c.note) + '</small>' : '');
            var tdN = document.createElement('td'); tdN.textContent = c.no || '-';
            var tdB = document.createElement('td'); tdB.textContent = c.bank || '-';
            var tdA = document.createElement('td'); tdA.className = 'text-end'; tdA.textContent = fmt(c.amount);
            var tdS = document.createElement('td'); tdS.innerHTML = statusBadge(c.status);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var grp = document.createElement('div');
            grp.className = 'btn-group btn-group-sm';
            var bC = document.createElement('button');
            bC.type = 'button'; bC.className = 'btn btn-outline-success'; bC.textContent = 'Cleared';
            bC.addEventListener('click', function () { setStatus(c.id, 'cleared'); });
            var bB = document.createElement('button');
            bB.type = 'button'; bB.className = 'btn btn-outline-warning'; bB.textContent = 'Bounced';
            bB.addEventListener('click', function () { setStatus(c.id, 'bounced'); });
            var bD = document.createElement('button');
            bD.type = 'button'; bD.className = 'btn btn-outline-danger'; bD.textContent = '×';
            bD.setAttribute('aria-label', 'Delete');
            bD.addEventListener('click', function () {
                if (!confirm('Delete this cheque record?')) return;
                data.cheques = data.cheques.filter(function (x) { return x.id !== c.id; });
                save(); renderAll();
            });
            grp.appendChild(bC); grp.appendChild(bB); grp.appendChild(bD);
            tdX.appendChild(grp);
            tr.appendChild(tdM); tr.appendChild(tdP); tr.appendChild(tdN);
            tr.appendChild(tdB); tr.appendChild(tdA); tr.appendChild(tdS); tr.appendChild(tdX);
            chqRows.appendChild(tr);
        });
        document.getElementById('sumPending').textContent = pend;
        document.getElementById('sumPendingAmt').textContent = fmt(pendAmt);
        document.getElementById('sumCleared').textContent = cleared;
        document.getElementById('sumBounced').textContent = bounced;
        chqEmpty.style.display = sorted.length ? 'none' : '';
    }

    function setStatus(id, s) {
        data.cheques.forEach(function (c) { if (c.id === id) c.status = s; });
        save(); renderAll();
    }
    function renderAll() {
        renderAlerts();
        renderTable();
    }

    addChqBtn.addEventListener('click', function () {
        hideError();
        var party = chqParty.value.trim();
        var amt = Number(chqAmount.value);
        if (!party) { showError('Please enter the party name.'); return; }
        if (!amt || amt <= 0) { showError('Please enter a valid amount (more than 0).'); return; }
        if (!chqMaturity.value) { showError('Please select the maturity date.'); return; }
        data.cheques.push({
            id: uid(),
            party: party,
            no: chqNo.value.trim(),
            bank: chqBank.value.trim(),
            amount: Math.round(amt * 100) / 100,
            issue: chqIssue.value,
            maturity: chqMaturity.value,
            status: chqStatus.value,
            note: chqNote.value.trim()
        });
        save();
        chqParty.value = ''; chqNo.value = ''; chqBank.value = '';
        chqAmount.value = ''; chqIssue.value = ''; chqMaturity.value = '';
        chqNote.value = ''; chqStatus.value = 'pending';
        renderAll();
    });

    renderAll();
})();
</script>
@endsection
