@extends('layouts.app')

@section('title', 'Cash Flow Summary - Azlaan Tools')
@section('meta_description', 'Daily, weekly and monthly summary of cash in vs cash out — income, expenses and balance trend.')

@section('content')
<style>
.cf-chart { display: flex; align-items: flex-end; gap: 6px; height: 180px; padding: 10px 4px 0 4px; border-bottom: 2px solid #dee2e6; }
.cf-col { flex: 1; display: flex; align-items: flex-end; justify-content: center; gap: 3px; height: 100%; min-width: 0; }
.cf-bar { width: 12px; border-radius: 3px 3px 0 0; min-height: 2px; }
.cf-bar-in { background-color: #198754; }
.cf-bar-out { background-color: #dc3545; }
.cf-labels { display: flex; gap: 6px; padding-top: 6px; }
.cf-label { flex: 1; text-align: center; font-size: 11px; color: #6c757d; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cf-net-pos { color: #198754; font-weight: 600; font-size: 11px; }
.cf-net-neg { color: #dc3545; font-weight: 600; font-size: 11px; }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Cash Flow Summary</h1>
            <p class="lead text-muted">Daily, weekly and monthly summary of cash in vs cash out — income, expenses and balance trend at a glance. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">New Entry</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="cfDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="cfDate">
                        </div>
                        <div class="col-md-3">
                            <label for="cfType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="cfType">
                                <option value="in">Cash In (Income)</option>
                                <option value="out">Cash Out (Expense)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cfCategory" class="form-label fw-semibold">Category</label>
                            <input type="text" class="form-control" id="cfCategory" list="cfCatList" placeholder="e.g. Sale, Rent">
                            <datalist id="cfCatList">
                                <option value="Sale"></option>
                                <option value="Service Income"></option>
                                <option value="Other Income"></option>
                                <option value="Stock Purchase"></option>
                                <option value="Rent"></option>
                                <option value="Salary"></option>
                                <option value="Electricity / Gas Bill"></option>
                                <option value="Transport"></option>
                                <option value="Marketing"></option>
                                <option value="Loan Payment"></option>
                                <option value="Other Expense"></option>
                            </datalist>
                        </div>
                        <div class="col-md-3">
                            <label for="cfAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="cfAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                    </div>
                    <div class="row g-3 mt-0">
                        <div class="col-12">
                            <label for="cfNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="cfNote" placeholder="e.g. Morning sale, payment to supplier">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="cfAddBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="cfError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Select Period</h2>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="cfPeriod" class="form-label fw-semibold">Period</label>
                            <select class="form-select" id="cfPeriod">
                                <option value="day">Daily</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="cfRefDate" class="form-label fw-semibold">Reference date</label>
                            <input type="date" class="form-control" id="cfRefDate">
                            <div class="form-text">Weekly = the week (Mon–Sun) of this date · Monthly = the month of this date</div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-light border mb-0" role="note">
                                <span class="fw-semibold" id="cfRangeLabel"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center g-2 mt-3">
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <div class="small text-muted">Cash In</div>
                                <div class="fw-bold text-success" id="cfTotalIn">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <div class="small text-muted">Cash Out</div>
                                <div class="fw-bold text-danger" id="cfTotalOut">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <div class="small text-muted">Net Cash Flow</div>
                                <div class="fw-bold" id="cfNet">Rs 0</div>
                            </div></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-light"><div class="card-body py-2">
                                <div class="small text-muted">Entries</div>
                                <div class="fw-bold" id="cfCount">0</div>
                            </div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-1">Balance trend</h2>
                    <p class="small text-muted mb-3" id="cfChartTitle"></p>
                    <div class="cf-chart" id="cfChart"></div>
                    <div class="cf-labels" id="cfChartLabels"></div>
                    <div class="d-flex gap-3 mt-2 small">
                        <span><span class="badge bg-success">&nbsp;</span> Cash In</span>
                        <span><span class="badge bg-danger">&nbsp;</span> Cash Out</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Period Entries</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Type</th><th>Category</th><th>Note</th><th class="text-end">Amount (Rs)</th><th></th></tr>
                            </thead>
                            <tbody id="cfRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted" id="cfEmpty">No entries in this period.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-success" id="cfCsvBtn">CSV Download</button>
                        <button type="button" class="btn btn-outline-danger" id="cfClearBtn">Clear All Data</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add an <strong>entry</strong> for every income or expense — with date, type, category and amount.</li>
                <li>Choose a <strong>Daily / Weekly / Monthly</strong> period — the summary cards will show the totals for that period.</li>
                <li>In the <strong>Balance trend</strong> chart, compare cash in (green) vs cash out (red) for the last 14 days, 12 weeks or 12 months.</li>
            </ol>
            <p class="small text-muted">Note: data is saved only in this browser, nothing is uploaded. This is a simple informational summary — not an audit-grade calculation.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_cashflow';
    var dateEl = document.getElementById('cfDate');
    var typeEl = document.getElementById('cfType');
    var categoryEl = document.getElementById('cfCategory');
    var noteEl = document.getElementById('cfNote');
    var amountEl = document.getElementById('cfAmount');
    var addBtn = document.getElementById('cfAddBtn');
    var errorBox = document.getElementById('cfError');
    var periodEl = document.getElementById('cfPeriod');
    var refDateEl = document.getElementById('cfRefDate');
    var rangeLabel = document.getElementById('cfRangeLabel');
    var rowsEl = document.getElementById('cfRows');
    var emptyEl = document.getElementById('cfEmpty');
    var chartEl = document.getElementById('cfChart');
    var chartLabelsEl = document.getElementById('cfChartLabels');
    var chartTitleEl = document.getElementById('cfChartTitle');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var parsed = JSON.parse(raw);
                if (parsed && parsed.entries) return parsed;
            }
        } catch (e) {}
        return { entries: [] };
    }
    function save(data) {
        try { localStorage.setItem(KEY, JSON.stringify(data)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() {
        return 'f' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function round2(n) {
        return Math.round(Number(n) * 100) / 100;
    }
    function todayStr() {
        var d = new Date();
        return iso(d);
    }
    function iso(d) {
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function parseDate(s) {
        return new Date(s + 'T00:00:00');
    }
    function addDays(d, n) {
        var r = new Date(d.getTime());
        r.setDate(r.getDate() + n);
        return r;
    }
    function shortLabel(d) {
        var names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return d.getDate() + ' ' + names[d.getMonth()];
    }
    function periodRange() {
        var ref = parseDate(refDateEl.value || todayStr());
        var p = periodEl.value;
        var start, end, label;
        if (p === 'day') {
            start = ref; end = ref;
            label = shortLabel(ref);
        } else if (p === 'week') {
            var dow = (ref.getDay() + 6) % 7; // Monday = 0
            start = addDays(ref, -dow);
            end = addDays(start, 6);
            label = shortLabel(start) + ' – ' + shortLabel(end);
        } else {
            start = new Date(ref.getFullYear(), ref.getMonth(), 1);
            end = new Date(ref.getFullYear(), ref.getMonth() + 1, 0);
            var names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            label = names[ref.getMonth()] + ' ' + ref.getFullYear();
        }
        return { start: iso(start), end: iso(end), label: label };
    }

    function inRange(dateStr, range) {
        return dateStr >= range.start && dateStr <= range.end;
    }

    function trendBuckets(period) {
        var buckets = [];
        var ref = parseDate(refDateEl.value || todayStr());
        var i, start, end, lbl;
        if (period === 'day') {
            for (i = 13; i >= 0; i--) {
                start = addDays(ref, -i);
                buckets.push({ start: iso(start), end: iso(start), label: shortLabel(start) });
            }
        } else if (period === 'week') {
            var dow = (ref.getDay() + 6) % 7;
            var thisMon = addDays(ref, -dow);
            for (i = 11; i >= 0; i--) {
                start = addDays(thisMon, -7 * i);
                end = addDays(start, 6);
                buckets.push({ start: iso(start), end: iso(end), label: shortLabel(start) });
            }
        } else {
            for (i = 11; i >= 0; i--) {
                var m = new Date(ref.getFullYear(), ref.getMonth() - i, 1);
                var me = new Date(m.getFullYear(), m.getMonth() + 1, 0);
                var names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                buckets.push({ start: iso(m), end: iso(me), label: names[m.getMonth()] + ' ' + String(m.getFullYear()).slice(2) });
            }
        }
        return buckets;
    }

    function render() {
        hideError();
        if (!refDateEl.value) refDateEl.value = todayStr();
        var data = load();
        var range = periodRange();
        rangeLabel.textContent = range.label;

        var tIn = 0, tOut = 0;
        var periodEntries = [];
        data.entries.forEach(function (e) {
            if (inRange(e.date, range)) {
                periodEntries.push(e);
                if (e.type === 'in') tIn += e.amount; else tOut += e.amount;
            }
        });
        tIn = round2(tIn); tOut = round2(tOut);
        var net = round2(tIn - tOut);
        document.getElementById('cfTotalIn').textContent = fmt(tIn);
        document.getElementById('cfTotalOut').textContent = fmt(tOut);
        var netEl = document.getElementById('cfNet');
        netEl.textContent = fmt(net);
        netEl.classList.remove('text-success', 'text-danger');
        netEl.classList.add(net >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('cfCount').textContent = periodEntries.length;

        emptyEl.style.display = periodEntries.length ? 'none' : '';
        rowsEl.innerHTML = '';
        periodEntries.sort(function (a, b) {
            if (a.date === b.date) return b.id < a.id ? -1 : 1;
            return a.date < b.date ? 1 : -1;
        });
        periodEntries.forEach(function (e) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(e.date) + '</td>' +
                '<td><span class="badge ' + (e.type === 'in' ? 'bg-success' : 'bg-danger') + '">' + (e.type === 'in' ? 'In' : 'Out') + '</span></td>' +
                '<td>' + esc(e.category || '—') + '</td>' +
                '<td>' + esc(e.note || '—') + '</td>' +
                '<td class="text-end">' + fmt(e.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn" data-id="' + e.id + '" aria-label="Delete">×</button></td>';
            rowsEl.appendChild(tr);
        });
        rowsEl.querySelectorAll('.del-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var d = load();
                var id = btn.getAttribute('data-id');
                d.entries = d.entries.filter(function (e) { return e.id !== id; });
                save(d);
                render();
            });
        });

        // Trend chart
        var buckets = trendBuckets(periodEl.value);
        var titles = { day: 'Last 14 days — cash in vs cash out', week: 'Last 12 weeks — cash in vs cash out', month: 'Last 12 months — cash in vs cash out' };
        chartTitleEl.textContent = titles[periodEl.value];
        var bIn = [], bOut = [], bNet = [];
        var maxV = 1;
        buckets.forEach(function (bk) {
            var bi = 0, bo = 0;
            data.entries.forEach(function (e) {
                if (inRange(e.date, { start: bk.start, end: bk.end })) {
                    if (e.type === 'in') bi += e.amount; else bo += e.amount;
                }
            });
            bi = round2(bi); bo = round2(bo);
            bIn.push(bi); bOut.push(bo); bNet.push(round2(bi - bo));
            if (bi > maxV) maxV = bi;
            if (bo > maxV) maxV = bo;
        });
        chartEl.innerHTML = '';
        chartLabelsEl.innerHTML = '';
        buckets.forEach(function (bk, i) {
            var col = document.createElement('div');
            col.className = 'cf-col';
            col.setAttribute('title', bk.label + ' — In: ' + fmt(bIn[i]) + ', Out: ' + fmt(bOut[i]) + ', Net: ' + fmt(bNet[i]));
            var hIn = Math.max(2, Math.round(bIn[i] / maxV * 100));
            var hOut = Math.max(2, Math.round(bOut[i] / maxV * 100));
            col.innerHTML = '<div class="cf-bar cf-bar-in" style="height:' + hIn + '%"></div>' +
                '<div class="cf-bar cf-bar-out" style="height:' + hOut + '%"></div>';
            chartEl.appendChild(col);
            var lab = document.createElement('div');
            lab.className = 'cf-label';
            lab.innerHTML = esc(bk.label) + '<br><span class="' + (bNet[i] >= 0 ? 'cf-net-pos' : 'cf-net-neg') + '">' + (bNet[i] >= 0 ? '+' : '−') + fmt(Math.abs(bNet[i])).replace('Rs ', '') + '</span>';
            chartLabelsEl.appendChild(lab);
        });
        if (!data.entries.length) {
            chartEl.innerHTML = '<div class="text-muted small w-100 text-center align-self-center">No entries yet — the trend will appear here.</div>';
            chartLabelsEl.innerHTML = '';
        }
    }

    addBtn.addEventListener('click', function () {
        hideError();
        if (!dateEl.value) { showError('Pick a date.'); return; }
        var amount = Number(amountEl.value);
        if (!amount || amount <= 0) { showError('Enter an amount greater than 0.'); return; }
        var data = load();
        data.entries.push({
            id: uid(),
            date: dateEl.value,
            type: typeEl.value,
            category: categoryEl.value.trim(),
            note: noteEl.value.trim(),
            amount: round2(amount)
        });
        save(data);
        categoryEl.value = '';
        noteEl.value = '';
        amountEl.value = '';
        render();
    });

    periodEl.addEventListener('change', render);
    refDateEl.addEventListener('change', function () { hideError(); render(); });

    document.getElementById('cfCsvBtn').addEventListener('click', function () {
        hideError();
        var data = load();
        if (!data.entries.length) { showError('Add an entry first before downloading the CSV.'); return; }
        var rows = ['Date,Type,Category,Note,Amount'];
        data.entries.forEach(function (e) {
            rows.push(e.date + ',' + e.type + ',"' + String(e.category || '').replace(/"/g, '""') + '","' + String(e.note || '').replace(/"/g, '""') + '",' + e.amount);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'cash-flow-summary.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.getElementById('cfClearBtn').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all cash flow data?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    dateEl.value = todayStr();
    refDateEl.value = todayStr();
    render();
})();
</script>
@endsection
