@extends('layouts.app')

@section('title', 'Finance Calendar View - Azlaan Tools')
@section('meta_description', 'Daily income and expense calendar with a budget forecast. Free finance calendar tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Finance Calendar View</h1>
            <p class="lead text-muted">Track daily income and expenses in a calendar — with a budget forecast. Your data is saved only in your browser; nothing is uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary" id="fcPrev">&lt; Previous</button>
                        <h2 class="h4 mb-0" id="fcTitle">-</h2>
                        <button type="button" class="btn btn-outline-primary" id="fcNext">Next &gt;</button>
                    </div>
                    <div class="row text-center g-1 mb-1 small fw-semibold text-muted" id="fcWeekdays"></div>
                    <div id="fcGrid"></div>
                    <div class="row text-center g-2 mt-3">
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Month Income</div><div class="fw-bold text-success" id="fcMonthIn">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Month Expense</div><div class="fw-bold text-danger" id="fcMonthOut">Rs 0</div></div></div>
                        <div class="col-4"><div class="border rounded p-2"><div class="small text-muted">Net</div><div class="fw-bold" id="fcMonthNet">Rs 0</div></div></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="fcForecast"></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add an entry</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="fcDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="fcDate">
                        </div>
                        <div class="col-md-3">
                            <label for="fcType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="fcType">
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fcAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="fcAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="fcNote" class="form-label fw-semibold">Note</label>
                            <input type="text" class="form-control" id="fcNote" placeholder="e.g. salary, electricity bill">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="fcAdd">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="fcError" role="alert"></div>

                    <h2 class="h5 mt-4">Selected day: <span id="fcSelDay">-</span></h2>
                    <div id="fcDayList" class="list-group"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="fcClear">Clear all data</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Add an entry for any day — it will appear in that calendar cell.</li>
                <li>Click a calendar cell to see that day's details below.</li>
                <li><strong>Forecast:</strong> you get a warning if this month's spending is higher than the income.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_fin_calendar';

    var fcPrev = document.getElementById('fcPrev');
    var fcNext = document.getElementById('fcNext');
    var fcTitle = document.getElementById('fcTitle');
    var fcWeekdays = document.getElementById('fcWeekdays');
    var fcGrid = document.getElementById('fcGrid');
    var fcMonthIn = document.getElementById('fcMonthIn');
    var fcMonthOut = document.getElementById('fcMonthOut');
    var fcMonthNet = document.getElementById('fcMonthNet');
    var fcForecast = document.getElementById('fcForecast');
    var fcDate = document.getElementById('fcDate');
    var fcType = document.getElementById('fcType');
    var fcAmt = document.getElementById('fcAmt');
    var fcNote = document.getElementById('fcNote');
    var fcAdd = document.getElementById('fcAdd');
    var fcError = document.getElementById('fcError');
    var fcSelDay = document.getElementById('fcSelDay');
    var fcDayList = document.getElementById('fcDayList');
    var fcClear = document.getElementById('fcClear');

    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    var now = new Date();
    var viewY = now.getFullYear();
    var viewM = now.getMonth();
    var selDate = null;

    function pad(n) { return ('0' + n).slice(-2); }
    function dstr(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function showError(m) { fcError.textContent = m; fcError.classList.remove('d-none'); }
    function hideError() { fcError.classList.add('d-none'); fcError.textContent = ''; }
    function load() {
        try { var r = localStorage.getItem(KEY); if (r) { var p = JSON.parse(r); if (Array.isArray(p)) return p; } } catch (e) {}
        return [];
    }
    function save(arr) { try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {} }

    fcWeekdays.innerHTML = DAYS.map(function (d) { return '<div class="col">' + d + '</div>'; }).join('');

    function renderCal() {
        var entries = load();
        fcTitle.textContent = MONTHS[viewM] + ' ' + viewY;
        var first = new Date(viewY, viewM, 1);
        var lead = (first.getDay() + 6) % 7; // Monday-first
        var daysIn = new Date(viewY, viewM + 1, 0).getDate();

        var daySum = {};
        entries.forEach(function (e) {
            if (!daySum[e.date]) daySum[e.date] = { income: 0, expense: 0, list: [] };
            daySum[e.date][e.type] += e.amount;
            daySum[e.date].list.push(e);
        });

        var cells = '';
        for (var i = 0; i < lead; i++) cells += '<div class="col border rounded bg-light p-1" style="min-height:70px;"></div>';
        for (var d = 1; d <= daysIn; d++) {
            var ds = dstr(viewY, viewM, d);
            var s = daySum[ds];
            var inner = '<div class="fw-semibold">' + d + '</div>';
            if (s) {
                if (s.income) inner += '<div class="small text-success">+' + fmt(s.income) + '</div>';
                if (s.expense) inner += '<div class="small text-danger">-' + fmt(s.expense) + '</div>';
            }
            var hl = (selDate === ds) ? ' border-primary border-2' : '';
            cells += '<div class="col border rounded p-1 fc-cell' + hl + '" data-date="' + ds + '" style="min-height:70px;cursor:pointer;">' + inner + '</div>';
        }
        fcGrid.innerHTML = '<div class="row g-1">' + cells + '</div>';

        fcGrid.querySelectorAll('.fc-cell').forEach(function (cell) {
            cell.addEventListener('click', function () {
                selDate = cell.getAttribute('data-date');
                fcDate.value = selDate;
                renderCal(); renderDay();
            });
        });

        // month summary
        var prefix = viewY + '-' + pad(viewM + 1);
        var mi = 0, mo = 0;
        entries.forEach(function (e) {
            if (e.date.indexOf(prefix) === 0) {
                if (e.type === 'income') mi += e.amount; else mo += e.amount;
            }
        });
        fcMonthIn.textContent = fmt(mi);
        fcMonthOut.textContent = fmt(mo);
        var net = mi - mo;
        fcMonthNet.textContent = fmt(net);
        fcMonthNet.className = 'fw-bold ' + (net >= 0 ? 'text-success' : 'text-danger');

        // forecast
        var todayDs = dstr(now.getFullYear(), now.getMonth(), now.getDate());
        var isCur = (viewY === now.getFullYear() && viewM === now.getMonth());
        if (isCur && mi + mo > 0) {
            var elapsed = Math.max(1, now.getDate());
            var projOut = mo / elapsed * daysIn;
            var burn = projOut - mi;
            fcForecast.textContent = burn > 0
                ? 'Forecast: at this pace your total spending this month will reach ' + fmt(projOut) + ' — ' + fmt(burn) + ' more than your income. Control your spending.'
                : 'Forecast: at this pace you will stay within budget this month (saving ' + fmt(-burn) + ').';
        } else {
            fcForecast.textContent = '';
        }
    }

    function renderDay() {
        var entries = load();
        if (!selDate) {
            fcSelDay.textContent = '-';
            fcDayList.innerHTML = '<div class="list-group-item text-muted">Click any day on the calendar.</div>';
            return;
        }
        fcSelDay.textContent = selDate;
        var list = entries.filter(function (e) { return e.date === selDate; });
        if (!list.length) {
            fcDayList.innerHTML = '<div class="list-group-item text-muted">No entries for this day.</div>';
            return;
        }
        fcDayList.innerHTML = '';
        list.forEach(function (e) {
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            var badge = e.type === 'income' ? 'bg-success' : 'bg-danger';
            item.innerHTML = '<span><span class="badge ' + badge + ' me-2">' + (e.type === 'income' ? 'Income' : 'Expense') + '</span>' + esc(e.note || '-') + '</span>' +
                '<span class="fw-semibold">' + fmt(e.amount) + '</span>';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger ms-2';
            del.textContent = '×';
            del.addEventListener('click', function () {
                var all = load().filter(function (x) { return x.id !== e.id; });
                save(all); renderCal(); renderDay();
            });
            item.appendChild(del);
            fcDayList.appendChild(item);
        });
    }

    fcDate.value = dstr(now.getFullYear(), now.getMonth(), now.getDate());
    selDate = fcDate.value;

    fcPrev.addEventListener('click', function () {
        viewM--; if (viewM < 0) { viewM = 11; viewY--; }
        renderCal();
    });
    fcNext.addEventListener('click', function () {
        viewM++; if (viewM > 11) { viewM = 0; viewY++; }
        renderCal();
    });

    fcAdd.addEventListener('click', function () {
        hideError();
        if (!fcDate.value) { showError('Select a date.'); return; }
        var amt = Number(fcAmt.value);
        if (!amt || amt <= 0) { showError('Enter an amount above 0.'); return; }
        var entries = load();
        entries.push({ id: 'e' + Date.now().toString(36), date: fcDate.value, type: fcType.value, amount: Math.round(amt * 100) / 100, note: fcNote.value.trim() });
        save(entries);
        selDate = fcDate.value;
        fcAmt.value = ''; fcNote.value = '';
        renderCal(); renderDay();
    });

    fcClear.addEventListener('click', function () {
        hideError();
        if (!confirm('Delete all finance entries?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        renderCal(); renderDay();
    });

    renderCal(); renderDay();
})();
</script>
@endsection
