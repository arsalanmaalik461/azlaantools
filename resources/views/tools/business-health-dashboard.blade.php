@extends('layouts.app')

@section('title', 'Business Health Dashboard - Azlaan Tools')
@section('meta_description', 'Sales, receivables, cash and stock health score on one screen — with red, amber, green indicators.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Business Health Dashboard</h1>
            <p class="lead text-muted">Sales, receivables, cash and stock health score on one screen. Save a monthly snapshot and compare it with past months. Your data is saved only in your browser — it is never uploaded.</p>

            <div id="invoiceNote" class="alert alert-info d-none"></div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <div class="display-6 fw-bold" id="scoreBig">—</div>
                        <div class="small text-muted">Health Score / 100</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <div class="fw-bold fs-5" id="indCash">—</div>
                        <div class="small text-muted">Cash Coverage</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <div class="fw-bold fs-5" id="indRecv">—</div>
                        <div class="small text-muted">Receivables Control</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <div class="fw-bold fs-5" id="indStock">—</div>
                        <div class="small text-muted">Stock Level</div>
                    </div></div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Month snapshot</h5>
                            <div class="mb-3">
                                <label for="hsMonth" class="form-label fw-semibold">Month</label>
                                <input type="month" class="form-control" id="hsMonth">
                            </div>
                            <div class="mb-3">
                                <label for="hsSales" class="form-label fw-semibold">Monthly sales (Rs)</label>
                                <input type="number" class="form-control" id="hsSales" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="hsRecv" class="form-label fw-semibold">Total receivables — money customers owe you (Rs)</label>
                                <input type="number" class="form-control" id="hsRecv" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="hsPay" class="form-label fw-semibold">Total payables — money you owe suppliers (Rs)</label>
                                <input type="number" class="form-control" id="hsPay" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="hsCash" class="form-label fw-semibold">Cash in hand / bank (Rs)</label>
                                <input type="number" class="form-control" id="hsCash" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="hsStock" class="form-label fw-semibold">Stock value (Rs)</label>
                                <input type="number" class="form-control" id="hsStock" placeholder="0" min="0" step="0.01">
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="saveSnapBtn">Save Snapshot</button>
                            <div class="alert alert-danger mt-3 d-none" id="hsError" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Health indicators</h5>
                            <div id="indList"></div>
                            <p class="small text-muted mb-0" id="indEmpty">Save a snapshot — indicators will appear here.</p>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Snapshot history</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead class="table-light">
                                        <tr><th>Month</th><th class="text-end">Sales</th><th class="text-end">Recv.</th><th class="text-end">Pay.</th><th class="text-end">Score</th><th></th></tr>
                                    </thead>
                                    <tbody id="histRows"></tbody>
                                </table>
                            </div>
                            <p class="small text-muted mb-0" id="histEmpty">No snapshot yet.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Choose the month and enter 5 numbers (sales, receivables, payables, cash, stock), then click <strong>Save Snapshot</strong>.</li>
                <li>The health score and red/amber/green indicators are made automatically.</li>
                <li>Save a new snapshot every month — you will see the score trend in history.</li>
            </ol>
            <p class="text-muted small">This is a simple guide, not professional accounting or tax advice. Your data is saved only in your browser.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_biz_health';
    var hsMonth = document.getElementById('hsMonth');
    var hsSales = document.getElementById('hsSales');
    var hsRecv = document.getElementById('hsRecv');
    var hsPay = document.getElementById('hsPay');
    var hsCash = document.getElementById('hsCash');
    var hsStock = document.getElementById('hsStock');
    var saveSnapBtn = document.getElementById('saveSnapBtn');
    var hsError = document.getElementById('hsError');
    var scoreBig = document.getElementById('scoreBig');
    var indCash = document.getElementById('indCash');
    var indRecv = document.getElementById('indRecv');
    var indStock = document.getElementById('indStock');
    var indList = document.getElementById('indList');
    var indEmpty = document.getElementById('indEmpty');
    var histRows = document.getElementById('histRows');
    var histEmpty = document.getElementById('histEmpty');
    var invoiceNote = document.getElementById('invoiceNote');

    var snaps = [];
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) { var p = JSON.parse(raw); if (Array.isArray(p)) snaps = p; }
    } catch (e) { snaps = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(snaps)); } catch (e) {}
    }
    function showError(msg) {
        hsError.textContent = msg;
        hsError.classList.remove('d-none');
    }
    function hideError() {
        hsError.classList.add('d-none');
        hsError.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }
    function num(v) {
        var n = parseFloat(v);
        return isNaN(n) || n < 0 ? 0 : Math.round(n * 100) / 100;
    }
    function ragClass(score) {
        if (score >= 70) return 'text-success';
        if (score >= 40) return 'text-warning';
        return 'text-danger';
    }
    function ragBadge(score) {
        if (score >= 70) return 'bg-success';
        if (score >= 40) return 'bg-warning text-dark';
        return 'bg-danger';
    }

    function compute(snap, prevSnap) {
        var sales = snap.sales, recv = snap.receivables, pay = snap.payables, cash = snap.cash, stock = snap.stock;
        var cashCov = pay > 0 ? Math.min(cash / pay, 1) : 1;
        var recvCtl = sales > 0 ? Math.max(0, 1 - recv / (sales * 2)) : 0.5;
        var stockM = sales > 0 ? stock / sales : 0;
        var stockLvl = sales > 0 ? (stockM <= 2 ? 1 : Math.max(0, 1 - (stockM - 2) * 0.25)) : 0.5;
        var netWorth = cash + recv + stock - pay;
        var netPos = sales > 0 ? Math.max(0, Math.min(1, 0.5 + netWorth / (sales * 4))) : 0.5;
        var momentum;
        if (prevSnap && prevSnap.sales > 0) {
            var ch = (sales - prevSnap.sales) / prevSnap.sales;
            momentum = ch >= 0.1 ? 1 : (ch >= 0 ? 0.65 : (ch >= -0.15 ? 0.4 : 0.2));
        } else {
            momentum = 0.6;
        }
        var parts = [
            { key: 'cash', label: 'Cash coverage', score: cashCov, tip: cashCov >= 0.7 ? 'Cash is fine compared to payables.' : 'Cash is low — you may have trouble clearing payables.' },
            { key: 'recv', label: 'Receivables control', score: recvCtl, tip: recvCtl >= 0.7 ? 'Credit is under control compared to sales.' : 'Collect faster from customers — too much money is stuck in credit.' },
            { key: 'stock', label: 'Stock level', score: stockLvl, tip: stockLvl >= 0.7 ? 'Stock is reasonable compared to sales.' : 'Too much money is stuck in stock — think of a discount on slow items.' },
            { key: 'net', label: 'Net position', score: netPos, tip: netPos >= 0.6 ? 'Assets are better than liabilities.' : 'Payables are high — keep an eye on cash flow.' },
            { key: 'mom', label: 'Sales momentum', score: momentum, tip: prevSnap ? (momentum >= 0.65 ? 'Sales are growing or stable.' : 'Sales are falling — find the reason.') : 'No previous snapshot found — you will see a trend from the second month.' }
        ];
        var weights = { cash: 0.25, recv: 0.25, stock: 0.15, net: 0.2, mom: 0.15 };
        var total = 0;
        parts.forEach(function (pt) { total += pt.score * 100 * weights[pt.key]; });
        return { parts: parts, score: Math.round(total), netWorth: netWorth };
    }

    function sorted() {
        return snaps.slice().sort(function (a, b) { return a.month < b.month ? 1 : -1; });
    }

    function render() {
        hideError();
        var list = sorted();
        histRows.innerHTML = '';
        histEmpty.style.display = list.length ? 'none' : '';
        list.forEach(function (s) {
            var idx = list.indexOf(s);
            var prev = idx + 1 < list.length ? list[idx + 1] : null;
            var c = compute(s, prev);
            var tr = document.createElement('tr');
            var cells = [
                esc(s.month),
                fmt(s.sales),
                fmt(s.receivables),
                fmt(s.payables),
                ''
            ];
            var tdM = document.createElement('td'); tdM.innerHTML = cells[0];
            var tdS = document.createElement('td'); tdS.className = 'text-end'; tdS.textContent = cells[1];
            var tdR = document.createElement('td'); tdR.className = 'text-end'; tdR.textContent = cells[2];
            var tdP = document.createElement('td'); tdP.className = 'text-end'; tdP.textContent = cells[3];
            var tdSc = document.createElement('td'); tdSc.className = 'text-end';
            var badge = document.createElement('span');
            badge.className = 'badge ' + ragBadge(c.score);
            badge.textContent = c.score;
            tdSc.appendChild(badge);
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete snapshot');
            del.addEventListener('click', function () {
                if (!confirm('Delete the snapshot of ' + s.month + '?')) return;
                snaps = snaps.filter(function (x) { return x.month !== s.month; });
                save(); render();
            });
            tdX.appendChild(del);
            tr.appendChild(tdM); tr.appendChild(tdS); tr.appendChild(tdR); tr.appendChild(tdP); tr.appendChild(tdSc); tr.appendChild(tdX);
            histRows.appendChild(tr);
        });

        indList.innerHTML = '';
        if (!list.length) {
            indEmpty.style.display = '';
            scoreBig.textContent = '—';
            scoreBig.className = 'display-6 fw-bold';
            indCash.textContent = '—'; indRecv.textContent = '—'; indStock.textContent = '—';
            return;
        }
        indEmpty.style.display = 'none';
        var latest = list[0];
        var prev = list.length > 1 ? list[1] : null;
        var res = compute(latest, prev);
        scoreBig.textContent = res.score;
        scoreBig.className = 'display-6 fw-bold ' + ragClass(res.score);
        res.parts.forEach(function (pt) {
            if (pt.key === 'cash') { indCash.textContent = Math.round(pt.score * 100); indCash.className = 'fw-bold fs-5 ' + ragClass(pt.score * 100); }
            if (pt.key === 'recv') { indRecv.textContent = Math.round(pt.score * 100); indRecv.className = 'fw-bold fs-5 ' + ragClass(pt.score * 100); }
            if (pt.key === 'stock') { indStock.textContent = Math.round(pt.score * 100); indStock.className = 'fw-bold fs-5 ' + ragClass(pt.score * 100); }
            var box = document.createElement('div');
            box.className = 'd-flex justify-content-between align-items-start border rounded p-2 mb-2';
            var left = document.createElement('div');
            left.innerHTML = '<strong>' + esc(pt.label) + '</strong><br><small class="text-muted">' + esc(pt.tip) + '</small>';
            var b = document.createElement('span');
            b.className = 'badge ' + ragBadge(pt.score * 100);
            b.textContent = Math.round(pt.score * 100);
            box.appendChild(left);
            box.appendChild(b);
            indList.appendChild(box);
        });
        var netBox = document.createElement('div');
        netBox.className = 'alert ' + (res.netWorth >= 0 ? 'alert-success' : 'alert-danger') + ' mb-0';
        netBox.innerHTML = 'Net position (cash + receivables + stock − payables): <strong>' + fmt(res.netWorth) + '</strong>';
        indList.appendChild(netBox);
    }

    function checkInvoices() {
        try {
            var raw = localStorage.getItem('azlaan_invoices');
            if (!raw) return;
            var inv = JSON.parse(raw);
            var arr = Array.isArray(inv) ? inv : (inv && Array.isArray(inv.invoices) ? inv.invoices : null);
            if (!arr || !arr.length) return;
            var total = 0;
            arr.forEach(function (x) {
                var t = parseFloat(x.total || x.amount || x.grandTotal || 0);
                if (!isNaN(t)) total += t;
            });
            if (total > 0) {
                invoiceNote.classList.remove('d-none');
                invoiceNote.innerHTML = 'Found <strong>' + arr.length + ' invoices</strong> in the invoice tool (total ' + fmt(total) + '). You can use this amount in receivables: ';
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-info ms-2';
                btn.textContent = 'Copy into receivables';
                btn.addEventListener('click', function () {
                    hsRecv.value = Math.round(total * 100) / 100;
                });
                invoiceNote.appendChild(btn);
            }
        } catch (e) {}
    }

    function currentMonth() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        return d.getFullYear() + '-' + m;
    }

    saveSnapBtn.addEventListener('click', function () {
        hideError();
        var month = hsMonth.value || currentMonth();
        var snap = {
            month: month,
            sales: num(hsSales.value),
            receivables: num(hsRecv.value),
            payables: num(hsPay.value),
            cash: num(hsCash.value),
            stock: num(hsStock.value)
        };
        var existing = null;
        snaps.forEach(function (s) { if (s.month === month) existing = s; });
        if (existing) {
            if (!confirm(month + ' already has a snapshot — overwrite it?')) return;
            snaps = snaps.filter(function (s) { return s.month !== month; });
        }
        snaps.push(snap);
        save();
        hsSales.value = ''; hsRecv.value = ''; hsPay.value = ''; hsCash.value = ''; hsStock.value = '';
        render();
    });

    hsMonth.value = currentMonth();
    checkInvoices();
    render();
})();
</script>
@endsection
