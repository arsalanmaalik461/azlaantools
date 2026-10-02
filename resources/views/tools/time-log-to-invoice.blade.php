@extends('layouts.app')

@section('title', 'Time Log to Invoice - Azlaan Tools')
@section('meta_description', 'Log your work hours, set your hourly rate, and make an invoice draft in one click. Freelancer billable hours tracker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Time Log to Invoice</h1>
            <p class="lead text-muted">Keep a log of your billable hours — work, hours, rate — then make an invoice draft in one click (unpaid), which will show in the invoice payment tracker. Data is saved only in your browser, it is not uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">New time entry</h5>
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-2">
                            <label for="tlDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="tlDate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="tlClient" class="form-label fw-semibold">Client</label>
                            <select class="form-select" id="tlClient">
                                <option value="">— Select —</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="tlTask" class="form-label fw-semibold">Work (task) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tlTask" placeholder="Example: Website design">
                        </div>
                        <div class="col-4 col-md-1">
                            <label for="tlHours" class="form-label fw-semibold">Hours <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tlHours" placeholder="0" min="0.25" step="0.25">
                        </div>
                        <div class="col-8 col-md-2">
                            <label for="tlRate" class="form-label fw-semibold">Rate/hr (Rs) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tlRate" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="tlAdd">Add Entry</button>
                        <button type="button" class="btn btn-outline-success" id="tlInvoice">Make Invoice Draft in One Click</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="tlError" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="tlDone" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Time log <span class="badge bg-secondary" id="tlCount">0</span></h5>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select form-select-sm w-auto" id="tlFilterClient" aria-label="Client filter">
                                <option value="">All clients</option>
                            </select>
                            <strong id="tlTotal">Rs 0</strong>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Client</th><th>Task</th><th class="text-end">Hours</th><th class="text-end">Rate/hr</th><th class="text-end">Amount</th><th></th></tr>
                            </thead>
                            <tbody id="tlRows"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="tlEmpty">No entries yet. Add a time entry from above.</p>
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="tlCsv">CSV Download</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="tlClear">Clear Log</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the date, client, work, hours and rate, then press <strong>Add Entry</strong>. Clients come from the <strong>Billing Client Book</strong> (if not there, leave the client select empty).</li>
                <li>Press <strong>Make Invoice Draft in One Click</strong> — an invoice draft will be made from all log entries (status: unpaid), each entry as one line item.</li>
                <li>The draft invoice will show in the <strong>Invoice Payment Tracker</strong> — record the payment there.</li>
            </ol>
            <p class="small text-muted">This record stays only in your own browser — nothing goes to a server. It is a finance record, not tax advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var LOG_KEY = 'azlaan_timelog';
    var CLIENT_KEY = 'azlaan_clients';
    var INV_KEY = 'azlaan_invoices';

    var tlDate = document.getElementById('tlDate');
    var tlClient = document.getElementById('tlClient');
    var tlTask = document.getElementById('tlTask');
    var tlHours = document.getElementById('tlHours');
    var tlRate = document.getElementById('tlRate');
    var tlAdd = document.getElementById('tlAdd');
    var tlInvoice = document.getElementById('tlInvoice');
    var tlError = document.getElementById('tlError');
    var tlDone = document.getElementById('tlDone');
    var tlRows = document.getElementById('tlRows');
    var tlEmpty = document.getElementById('tlEmpty');
    var tlCount = document.getElementById('tlCount');
    var tlTotal = document.getElementById('tlTotal');
    var tlFilterClient = document.getElementById('tlFilterClient');
    var tlCsv = document.getElementById('tlCsv');
    var tlClear = document.getElementById('tlClear');

    var log = [];
    var clients = [];

    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function loadAll() {
        try {
            var r1 = localStorage.getItem(LOG_KEY);
            if (r1) { var p1 = JSON.parse(r1); if (Array.isArray(p1)) log = p1; }
        } catch (e) { log = []; }
        try {
            var r2 = localStorage.getItem(CLIENT_KEY);
            if (r2) { var p2 = JSON.parse(r2); if (Array.isArray(p2)) clients = p2; }
        } catch (e) { clients = []; }
    }
    function saveLog() {
        try { localStorage.setItem(LOG_KEY, JSON.stringify(log)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid(prefix) {
        return prefix + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function showError(msg) {
        tlDone.classList.add('d-none');
        tlError.textContent = msg;
        tlError.classList.remove('d-none');
    }
    function showDone(msg) {
        tlError.classList.add('d-none');
        tlDone.textContent = msg;
        tlDone.classList.remove('d-none');
    }
    function hideMsgs() {
        tlError.classList.add('d-none'); tlError.textContent = '';
        tlDone.classList.add('d-none'); tlDone.textContent = '';
    }
    function clientName(id) {
        if (!id) return '—';
        for (var i = 0; i < clients.length; i++) {
            if (clients[i].id === id) return clients[i].name;
        }
        return '—';
    }
    function clientObj(id) {
        for (var i = 0; i < clients.length; i++) {
            if (clients[i].id === id) return clients[i];
        }
        return null;
    }
    function refreshClientSelects() {
        var keep1 = tlClient.value;
        var keep2 = tlFilterClient.value;
        tlClient.innerHTML = '<option value="">— Select —</option>';
        tlFilterClient.innerHTML = '<option value="">All clients</option>';
        clients.forEach(function (c) {
            var o1 = document.createElement('option');
            o1.value = c.id;
            o1.textContent = c.name;
            tlClient.appendChild(o1);
            var o2 = document.createElement('option');
            o2.value = c.id;
            o2.textContent = c.name;
            tlFilterClient.appendChild(o2);
        });
        tlClient.value = keep1;
        tlFilterClient.value = keep2;
    }

    function render() {
        hideMsgs();
        refreshClientSelects();
        var f = tlFilterClient.value;
        tlRows.innerHTML = '';
        var shown = log.filter(function (e) { return !f || e.clientId === f; });
        tlCount.textContent = log.length;
        tlEmpty.style.display = log.length ? 'none' : '';
        var total = 0;
        var sorted = shown.slice().sort(function (a, b) { return a.date < b.date ? 1 : -1; });
        sorted.forEach(function (e) {
            var amt = Math.round(e.hours * e.rate * 100) / 100;
            total += amt;
            var tr = document.createElement('tr');
            var cells = [
                { t: e.date },
                { t: clientName(e.clientId) },
                { t: e.task }
            ];
            cells.forEach(function (c) {
                var td = document.createElement('td');
                td.textContent = c.t;
                tr.appendChild(td);
            });
            var tdH = document.createElement('td'); tdH.className = 'text-end'; tdH.textContent = e.hours; tr.appendChild(tdH);
            var tdR = document.createElement('td'); tdR.className = 'text-end'; tdR.textContent = fmt(e.rate); tr.appendChild(tdR);
            var tdA = document.createElement('td'); tdA.className = 'text-end fw-bold'; tdA.textContent = fmt(amt); tr.appendChild(tdA);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete entry');
            del.addEventListener('click', function () {
                log = log.filter(function (x) { return x.id !== e.id; });
                saveLog();
                render();
            });
            tdX.appendChild(del);
            tr.appendChild(tdX);
            tlRows.appendChild(tr);
        });
        tlTotal.textContent = fmt(total);
    }

    tlAdd.addEventListener('click', function () {
        hideMsgs();
        var task = tlTask.value.trim();
        var hours = parseFloat(tlHours.value);
        var rate = parseFloat(tlRate.value);
        if (!tlDate.value) { showError('Please select a date.'); return; }
        if (!task) { showError('Please enter the work (task).'); tlTask.focus(); return; }
        if (isNaN(hours) || hours <= 0) { showError('Please enter hours more than 0.'); tlHours.focus(); return; }
        if (isNaN(rate) || rate < 0) { showError('Please enter a valid rate (0 or more).'); tlRate.focus(); return; }
        log.push({
            id: uid('tl'),
            date: tlDate.value,
            clientId: tlClient.value || '',
            task: task,
            hours: Math.round(hours * 100) / 100,
            rate: Math.round(rate * 100) / 100,
            createdAt: new Date().toISOString()
        });
        saveLog();
        tlTask.value = '';
        tlHours.value = '';
        tlRate.value = '';
        render();
    });

    tlInvoice.addEventListener('click', function () {
        hideMsgs();
        var f = tlFilterClient.value;
        var entries = log.filter(function (e) { return !f || e.clientId === f; });
        if (!entries.length) { showError('Please add time entries first for the invoice.'); return; }
        var invoices = [];
        try {
            var raw = localStorage.getItem(INV_KEY);
            if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) invoices = p; }
        } catch (e) { invoices = []; }
        var maxNum = 0;
        invoices.forEach(function (inv) {
            var m = /INV-(\d+)/.exec(inv.invoiceNo || '');
            if (m) maxNum = Math.max(maxNum, parseInt(m[1], 10));
        });
        var num = maxNum + 1;
        var invoiceNo = 'INV-' + String(num).padStart(4, '0');
        var c = f ? clientObj(f) : null;
        var itemsArr = entries.map(function (e) {
            var line = Math.round(e.hours * e.rate * 100) / 100;
            return {
                name: e.task + ' (' + e.date + ', ' + e.hours + ' hrs)',
                qty: e.hours,
                unit: 'hrs',
                rate: e.rate,
                taxPct: 0,
                lineTotal: line
            };
        });
        var subtotal = 0;
        itemsArr.forEach(function (it) { subtotal += it.lineTotal; });
        subtotal = Math.round(subtotal * 100) / 100;
        var inv = {
            id: uid('inv'),
            invoiceNo: invoiceNo,
            date: todayStr(),
            dueDate: '',
            client: c ? { id: c.id, name: c.name, phone: c.phone || '', address: c.address || '' }
                      : { id: '', name: (f ? clientName(f) : 'Walk-in'), phone: '', address: '' },
            items: itemsArr,
            subtotal: subtotal,
            taxTotal: 0,
            discount: 0,
            total: subtotal,
            status: 'unpaid',
            source: 'time-log',
            notes: 'Auto-generated from time log (' + entries.length + ' entries)',
            createdAt: new Date().toISOString()
        };
        invoices.unshift(inv);
        try {
            localStorage.setItem(INV_KEY, JSON.stringify(invoices));
        } catch (e) { showError('Could not save the invoice — storage is blocked.'); return; }
        showDone('Invoice draft made: ' + invoiceNo + ' — ' + fmt(subtotal) + ' (status: unpaid). It will show in the Invoice Payment Tracker.');
    });

    tlFilterClient.addEventListener('change', render);

    tlCsv.addEventListener('click', function () {
        hideMsgs();
        if (!log.length) { showError('Please add an entry first for the CSV.'); return; }
        var rows = ['Date,Client,Task,Hours,RatePerHour,Amount'];
        log.forEach(function (e) {
            var amt = Math.round(e.hours * e.rate * 100) / 100;
            rows.push([e.date, '"' + clientName(e.clientId).replace(/"/g, '""') + '"',
                '"' + String(e.task).replace(/"/g, '""') + '"', e.hours, e.rate, amt].join(','));
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'time-log.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    tlClear.addEventListener('click', function () {
        hideMsgs();
        if (!log.length) return;
        if (!confirm('Clear the whole time log?')) return;
        log = [];
        saveLog();
        render();
    });

    tlDate.value = todayStr();
    loadAll();
    render();
})();
</script>
@endsection
