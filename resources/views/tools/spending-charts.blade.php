@extends('layouts.app')

@section('title', 'Spending Charts & Analytics - Azlaan Tools')
@section('meta_description', 'Spending pie, bar and trend charts — see where your money goes at a glance. Free spending analytics.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Spending Charts &amp; Analytics</h1>
            <p class="lead text-muted">Spending pie, bar and trend charts — see where your money goes at a glance. Data is saved only in your browser, never uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new expense</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="spDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="spDate">
                        </div>
                        <div class="col-md-4">
                            <label for="spCat" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="spCat">
                                <option value="Home expenses">Home expenses</option>
                                <option value="Food">Food</option>
                                <option value="Transport">Transport</option>
                                <option value="Bills">Bills</option>
                                <option value="Health">Health</option>
                                <option value="Education">Education</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Savings">Savings</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="spAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="spAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="spAdd">Add</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="spError" role="alert"></div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-6 col-md-4">
                    <div class="card bg-light"><div class="card-body text-center py-3"><small class="text-muted">Total spending (this month)</small><div class="fw-bold fs-5 text-danger" id="spMonthTotal">Rs 0</div></div></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-light"><div class="card-body text-center py-3"><small class="text-muted">Total spending (all)</small><div class="fw-bold fs-5" id="spAllTotal">Rs 0</div></div></div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card bg-light"><div class="card-body text-center py-3"><small class="text-muted">Entries</small><div class="fw-bold fs-5" id="spCount">0</div></div></div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h2 class="h5">Category Pie — this month</h2>
                            <div class="d-flex justify-content-center my-3">
                                <div id="spPie" style="width:220px;height:220px;border-radius:50%;background:#e9ecef;"></div>
                            </div>
                            <div id="spLegend" class="small"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h2 class="h5">Monthly Bar Chart — last 6 months</h2>
                            <div id="spBars" class="d-flex align-items-end gap-2 mt-3" style="height:220px;"></div>
                            <div id="spBarLabels" class="d-flex gap-2 small text-muted text-center mt-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Entries</h2>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-success me-2" id="spCsv">CSV Download</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="spClear">Clear all</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light"><tr><th>Date</th><th>Category</th><th class="text-end">Amount</th><th></th></tr></thead>
                            <tbody id="spRows"></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0" id="spEmpty">No entries yet — add an expense from above.</p>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Enter the date, category and amount, then press <strong>Add</strong>.</li>
                <li>The <strong>pie chart</strong> will show which category the money went to this month.</li>
                <li>See the trend of the last 6 months in the <strong>bar chart</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_spending';
    var COLORS = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1', '#fd7e14', '#20c997', '#6c757d', '#0dcaf0', '#d63384'];

    var spDate = document.getElementById('spDate');
    var spCat = document.getElementById('spCat');
    var spAmt = document.getElementById('spAmt');
    var spAdd = document.getElementById('spAdd');
    var spError = document.getElementById('spError');
    var spPie = document.getElementById('spPie');
    var spLegend = document.getElementById('spLegend');
    var spBars = document.getElementById('spBars');
    var spBarLabels = document.getElementById('spBarLabels');
    var spRows = document.getElementById('spRows');
    var spEmpty = document.getElementById('spEmpty');
    var spMonthTotal = document.getElementById('spMonthTotal');
    var spAllTotal = document.getElementById('spAllTotal');
    var spCount = document.getElementById('spCount');
    var spCsv = document.getElementById('spCsv');
    var spClear = document.getElementById('spClear');

    function showError(m) { spError.textContent = m; spError.classList.remove('d-none'); }
    function hideError() { spError.classList.add('d-none'); spError.textContent = ''; }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
    }
    function load() {
        try { var r = localStorage.getItem(KEY); if (r) { var p = JSON.parse(r); if (Array.isArray(p)) return p; } } catch (e) {}
        return [];
    }
    function save(arr) { try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {} }
    function monthKey(ym) { return ym.slice(0, 7); }
    function monthName(ym) {
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var parts = ym.split('-');
        return months[Number(parts[1]) - 1] + ' ' + parts[0].slice(2);
    }

    function render() {
        var entries = load();
        var now = new Date();
        var curMonth = now.getFullYear() + '-' + ('0' + (now.getMonth() + 1)).slice(-2);

        // totals
        var monthTotal = 0, allTotal = 0;
        var byCat = {};
        entries.forEach(function (e) {
            allTotal += e.amount;
            if (monthKey(e.date) === curMonth) {
                monthTotal += e.amount;
                byCat[e.category] = (byCat[e.category] || 0) + e.amount;
            }
        });
        spMonthTotal.textContent = fmt(monthTotal);
        spAllTotal.textContent = fmt(allTotal);
        spCount.textContent = entries.length;

        // pie (CSS conic-gradient)
        var cats = Object.keys(byCat).sort(function (a, b) { return byCat[b] - byCat[a]; });
        if (!cats.length) {
            spPie.style.background = '#e9ecef';
            spLegend.innerHTML = '<span class="text-muted">No spending this month — add an entry to build the pie chart.</span>';
        } else {
            var acc = 0, segs = [];
            cats.forEach(function (c, i) {
                var start = acc / monthTotal * 360;
                acc += byCat[c];
                var end = acc / monthTotal * 360;
                segs.push(COLORS[i % COLORS.length] + ' ' + start.toFixed(1) + 'deg ' + end.toFixed(1) + 'deg');
            });
            spPie.style.background = 'conic-gradient(' + segs.join(', ') + ')';
            var leg = '<div class="row g-1">';
            cats.forEach(function (c, i) {
                var pct = (byCat[c] / monthTotal * 100).toFixed(1);
                leg += '<div class="col-6 d-flex align-items-center"><span class="me-1 d-inline-block rounded" style="width:12px;height:12px;background:' + COLORS[i % COLORS.length] + ';"></span><span>' + esc(c) + ' — ' + pct + '%</span></div>';
            });
            spLegend.innerHTML = leg + '</div>';
        }

        // bars: last 6 months
        var months = [];
        for (var k = 5; k >= 0; k--) {
            var d = new Date(now.getFullYear(), now.getMonth() - k, 1);
            months.push(d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2));
        }
        var byMonth = {};
        entries.forEach(function (e) {
            var mk = monthKey(e.date);
            if (byMonth[mk] === undefined) byMonth[mk] = 0;
            byMonth[mk] += e.amount;
        });
        var maxV = 0;
        months.forEach(function (m) { maxV = Math.max(maxV, byMonth[m] || 0); });
        if (maxV === 0) maxV = 1;
        var barsHtml = '', labelsHtml = '';
        months.forEach(function (m) {
            var v = byMonth[m] || 0;
            var h = Math.max(2, v / maxV * 100);
            var col = (m === curMonth) ? '#0d6efd' : '#adb5bd';
            barsHtml += '<div class="flex-fill rounded-top" style="height:' + h.toFixed(1) + '%;background:' + col + ';" title="' + monthName(m) + ': ' + fmt(v) + '"></div>';
            labelsHtml += '<div class="flex-fill">' + monthName(m) + '</div>';
        });
        spBars.innerHTML = barsHtml;
        spBarLabels.innerHTML = labelsHtml;

        // table
        var sorted = entries.slice().sort(function (a, b) { return a.date < b.date ? 1 : -1; });
        spEmpty.style.display = sorted.length ? 'none' : '';
        var rows = '';
        sorted.forEach(function (e, i) {
            rows += '<tr><td>' + esc(e.date) + '</td><td>' + esc(e.category) + '</td>' +
                '<td class="text-end text-danger">' + fmt(e.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn" data-idx="' + i + '">×</button></td></tr>';
        });
        spRows.innerHTML = rows;
        spRows.querySelectorAll('.del-btn').forEach(function (b) {
            b.addEventListener('click', function () {
                var all = load().slice().sort(function (a, z) { return a.date < z.date ? 1 : -1; });
                all.splice(Number(b.getAttribute('data-idx')), 1);
                save(all); render();
            });
        });
    }

    spDate.value = todayStr();

    spAdd.addEventListener('click', function () {
        hideError();
        if (!spDate.value) { showError('Select the date first.'); return; }
        var amt = Number(spAmt.value);
        if (!amt || amt <= 0) { showError('Enter an amount greater than 0.'); return; }
        var entries = load();
        entries.push({ date: spDate.value, category: spCat.value, amount: Math.round(amt * 100) / 100 });
        save(entries);
        spAmt.value = '';
        render();
    });

    spCsv.addEventListener('click', function () {
        hideError();
        var entries = load();
        if (!entries.length) { showError('Add an entry first to download the CSV.'); return; }
        var rows = ['Date,Category,Amount'];
        entries.forEach(function (e) {
            rows.push(e.date + ',"' + String(e.category).replace(/"/g, '""') + '",' + e.amount);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'spending.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
    });

    spClear.addEventListener('click', function () {
        hideError();
        if (!confirm('Delete all spending entries?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    render();
})();
</script>
@endsection
