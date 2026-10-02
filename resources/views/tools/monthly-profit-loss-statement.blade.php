@extends('layouts.app')

@section('title', 'Monthly P&L Statement - Azlaan Tools')
@section('meta_description', 'Monthly profit and loss statement — revenue, cost and expenses with margins, printable P&L statement.')

@section('content')
<style>
@@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
}
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Monthly P&L Statement</h1>
            <p class="lead text-muted no-print">Monthly profit and loss — enter revenue, cost (COGS) and expenses, with gross and net profit margins. Data is saved only in your browser, it is never uploaded.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="pnlMonth" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control" id="pnlMonth">
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-0">Each month's data is saved separately. Change the month to see a previous month's P&L.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add new line</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="pnlKind" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="pnlKind">
                                <option value="revenue">Revenue (Income)</option>
                                <option value="cogs">COGS (Cost of goods)</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pnlName" class="form-label fw-semibold">Details</label>
                            <input type="text" class="form-control" id="pnlName" placeholder="e.g. Shop sales, stock purchase, electricity bill">
                        </div>
                        <div class="col-md-3">
                            <label for="pnlAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="pnlAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="pnlAddBtn">Add Line</button>
                    <div class="alert alert-danger mt-3 d-none" id="pnlError" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Profit &amp; Loss — <span id="pnlMonthLabel"></span></h2>
                        <div class="no-print d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="pnlPrintBtn">Print / PDF</button>
                        </div>
                    </div>

                    <h3 class="h6 text-success">Revenue</h3>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm align-middle">
                            <tbody id="pnlRevenueRows"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-3">
                        <span>Total Revenue</span><span class="text-success" id="pnlRevTotal">Rs 0</span>
                    </div>

                    <h3 class="h6 text-secondary">COGS — Cost of goods sold</h3>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm align-middle">
                            <tbody id="pnlCogsRows"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-3">
                        <span>Total COGS</span><span id="pnlCogsTotal">Rs 0</span>
                    </div>

                    <h3 class="h6 text-danger">Expenses</h3>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm align-middle">
                            <tbody id="pnlExpRows"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-3">
                        <span>Total Expenses</span><span class="text-danger" id="pnlExpTotal">Rs 0</span>
                    </div>

                    <h3 class="h6">Result</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Gross Profit <span class="text-muted small">(Revenue − COGS)</span></td>
                                    <td class="text-end fw-bold" id="pnlGross">Rs 0</td>
                                    <td class="text-end" id="pnlGrossMargin">—</td>
                                </tr>
                                <tr>
                                    <td>Net Profit <span class="text-muted small">(Gross − Expenses)</span></td>
                                    <td class="text-end fw-bold fs-5" id="pnlNet">Rs 0</td>
                                    <td class="text-end fw-bold" id="pnlNetMargin">—</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="no-print d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-success" id="pnlCsvBtn">Download CSV</button>
                        <button type="button" class="btn btn-outline-danger" id="pnlClearBtn">Clear this month's data</button>
                    </div>
                </div>
            </div>

            <div class="no-print">
                <h2>How to use</h2>
                <ol>
                    <li>Select the month, then add <strong>Revenue</strong> (sales, services), <strong>COGS</strong> (cost of sold stock) and <strong>Expenses</strong> (rent, electricity, salaries) lines.</li>
                    <li><strong>Gross Profit = Revenue − COGS</strong>, <strong>Net Profit = Gross − Expenses</strong> will be calculated automatically, with margin %.</li>
                    <li>Use <strong>Print / PDF</strong> to print the statement or make a PDF.</li>
                </ol>
                <p class="small text-muted">Note: data is saved only in this browser, it is never uploaded. This is a simplified informational statement — not audit-grade or tax-ready accounts.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_pnl';
    var monthEl = document.getElementById('pnlMonth');
    var kindEl = document.getElementById('pnlKind');
    var nameEl = document.getElementById('pnlName');
    var amountEl = document.getElementById('pnlAmount');
    var addBtn = document.getElementById('pnlAddBtn');
    var errorBox = document.getElementById('pnlError');
    var monthLabel = document.getElementById('pnlMonthLabel');

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
                if (parsed && parsed.months) return parsed;
            }
        } catch (e) {}
        return { months: {} };
    }
    function save(data) {
        try { localStorage.setItem(KEY, JSON.stringify(data)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function lines() {
        var data = load();
        if (!data.months[monthEl.value]) data.months[monthEl.value] = [];
        return data.months[monthEl.value];
    }
    function saveLines(list) {
        var data = load();
        data.months[monthEl.value] = list;
        save(data);
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function uid() {
        return 'l' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function round2(n) {
        return Math.round(Number(n) * 100) / 100;
    }
    function kindTotal(list, kind) {
        var t = 0;
        list.forEach(function (l) {
            if (l.kind === kind) t += l.amount;
        });
        return round2(t);
    }
    function monthName(m) {
        var parts = m.split('-');
        var names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var idx = Number(parts[1]) - 1;
        return (names[idx] || '') + ' ' + parts[0];
    }
    function marginPct(part, whole) {
        if (!whole) return '—';
        return (round2(part / whole * 10000) / 100).toFixed(1) + '%';
    }

    function renderKind(list, kind, rowsId) {
        var tbody = document.getElementById(rowsId);
        tbody.innerHTML = '';
        var any = false;
        list.forEach(function (l) {
            if (l.kind !== kind) return;
            any = true;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(l.name || '—') + '</td>' +
                '<td class="text-end text-nowrap">' + fmt(l.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn no-print" data-id="' + l.id + '" aria-label="Delete">×</button></td>';
            tbody.appendChild(tr);
        });
        if (!any) {
            var tr0 = document.createElement('tr');
            tr0.innerHTML = '<td colspan="3" class="text-muted small">No lines yet.</td>';
            tbody.appendChild(tr0);
        }
        tbody.querySelectorAll('.del-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-id');
                var list = lines().filter(function (l) { return l.id !== id; });
                saveLines(list);
                render();
            });
        });
    }

    function render() {
        hideError();
        if (!monthEl.value) {
            monthEl.value = currentMonth();
        }
        monthLabel.textContent = monthName(monthEl.value);
        var list = lines();
        renderKind(list, 'revenue', 'pnlRevenueRows');
        renderKind(list, 'cogs', 'pnlCogsRows');
        renderKind(list, 'expense', 'pnlExpRows');

        var rev = kindTotal(list, 'revenue');
        var cogs = kindTotal(list, 'cogs');
        var exp = kindTotal(list, 'expense');
        var gross = round2(rev - cogs);
        var net = round2(gross - exp);

        document.getElementById('pnlRevTotal').textContent = fmt(rev);
        document.getElementById('pnlCogsTotal').textContent = fmt(cogs);
        document.getElementById('pnlExpTotal').textContent = fmt(exp);

        var grossEl = document.getElementById('pnlGross');
        grossEl.textContent = fmt(gross);
        grossEl.classList.remove('text-success', 'text-danger');
        grossEl.classList.add(gross >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('pnlGrossMargin').textContent = marginPct(gross, rev);

        var netEl = document.getElementById('pnlNet');
        netEl.textContent = fmt(net);
        netEl.classList.remove('text-success', 'text-danger');
        netEl.classList.add(net >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('pnlNetMargin').textContent = marginPct(net, rev);
    }

    function currentMonth() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        return d.getFullYear() + '-' + m;
    }

    addBtn.addEventListener('click', function () {
        hideError();
        if (!monthEl.value) { showError('Select the month first.'); return; }
        var name = nameEl.value.trim();
        var amount = Number(amountEl.value);
        if (!name) { showError('Enter a description.'); return; }
        if (!amount || amount <= 0) { showError('Enter an amount greater than 0.'); return; }
        var list = lines();
        list.push({
            id: uid(),
            kind: kindEl.value,
            name: name,
            amount: round2(amount)
        });
        saveLines(list);
        nameEl.value = '';
        amountEl.value = '';
        render();
    });

    monthEl.addEventListener('change', function () {
        hideError();
        render();
    });

    document.getElementById('pnlPrintBtn').addEventListener('click', function () {
        window.print();
    });

    document.getElementById('pnlCsvBtn').addEventListener('click', function () {
        hideError();
        var list = lines();
        if (!list.length) { showError('Add a line first for the CSV.'); return; }
        var rows = ['Month,Type,Description,Amount'];
        list.forEach(function (l) {
            rows.push(monthEl.value + ',' + l.kind + ',"' + String(l.name || '').replace(/"/g, '""') + '",' + l.amount);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'pnl-' + monthEl.value + '.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.getElementById('pnlClearBtn').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all P&L data for this month?')) return;
        saveLines([]);
        render();
    });

    monthEl.value = currentMonth();
    render();
})();
</script>
@endsection
