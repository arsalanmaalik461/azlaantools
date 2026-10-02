@extends('layouts.app')

@section('title', 'Invoice Payment Tracker - Azlaan Tools')
@section('meta_description', 'Track invoice payments: see which invoice is paid, partial or overdue. Live totals of balance due and receivables. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Invoice Payment Tracker</h1>
            <p class="lead text-muted">See which invoice is paid, partial or overdue — record payments and track balance due. Invoices come from the "Invoice Maker" tool. Your data is saved only in your browser, never uploaded.</p>

            <div class="row text-center g-2 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm bg-light"><div class="card-body py-3">
                        <div class="small text-muted">Total Receivable</div>
                        <div class="fw-bold fs-5" id="sumRecv">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm bg-light"><div class="card-body py-3">
                        <div class="small text-muted">Received</div>
                        <div class="fw-bold fs-5 text-success" id="sumRecd">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm bg-light"><div class="card-body py-3">
                        <div class="small text-muted">Balance Due</div>
                        <div class="fw-bold fs-5 text-danger" id="sumDue">Rs 0</div>
                    </div></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm bg-light"><div class="card-body py-3">
                        <div class="small text-muted">Overdue</div>
                        <div class="fw-bold fs-5 text-danger" id="sumOver">Rs 0</div>
                    </div></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-6 col-md-4">
                            <label for="statusFilter" class="form-label fw-semibold">Status filter</label>
                            <select class="form-select" id="statusFilter">
                                <option value="all">All</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-8">
                            <button type="button" class="btn btn-outline-secondary" id="reloadBtn">Reload invoices</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-light">
                                <tr><th>Invoice</th><th>Client</th><th>Due date</th><th class="text-end">Total</th><th class="text-end">Received</th><th class="text-end">Balance</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody id="invRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="invEmpty">No invoices found. First make an invoice with the "Invoice Maker" tool.</p>

                    <div class="alert alert-danger mt-3 d-none" id="trkErrorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 d-none" id="payCard">
                <div class="card-body">
                    <h2 class="h5 mb-3">Record payment — <span id="payInvNo"></span></h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label for="payAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="payAmount" min="0.01" step="0.01" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="payDate" class="form-label fw-semibold">Payment date</label>
                            <input type="date" class="form-control" id="payDate">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="payNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="payNote" placeholder="e.g. Cash received / bank transfer">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button type="button" class="btn btn-primary" id="paySaveBtn">Save Payment</button>
                        <button type="button" class="btn btn-outline-success" id="payFullBtn">Mark full balance as paid</button>
                        <button type="button" class="btn btn-outline-secondary" id="payCancelBtn">Cancel</button>
                    </div>
                    <h3 class="h6 mt-4 mb-2">Payment history of this invoice</h3>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light"><tr><th>Date</th><th class="text-end">Amount</th><th>Note</th><th></th></tr></thead>
                            <tbody id="payHistRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="payHistEmpty">No payment recorded yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Press <strong>Payment</strong> next to each invoice below.</li>
                <li>Enter the amount and date, then press <strong>Save Payment</strong> — the status becomes paid/partial automatically.</li>
                <li>When the due date passes, an unpaid/partial invoice is flagged <strong>Overdue</strong> by itself.</li>
                <li>The cards above show total receivable, received, balance due and overdue live.</li>
            </ol>
            <p class="small text-muted">This record stays in your own browser only — nothing goes to a server. Figures are for information only, not tax advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var INV_KEY = 'azlaan_invoices';

    var $ = function (id) { return document.getElementById(id); };
    var invRows = $('invRows'), invEmpty = $('invEmpty'),
        statusFilter = $('statusFilter'), errorBox = $('trkErrorBox'),
        payCard = $('payCard'), payInvNo = $('payInvNo'),
        payAmount = $('payAmount'), payDate = $('payDate'), payNote = $('payNote'),
        payHistRows = $('payHistRows'), payHistEmpty = $('payHistEmpty');

    var activeId = null;

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
    function received(inv) {
        var r = 0;
        (inv.payments || []).forEach(function (p) { r += Number(p.amount) || 0; });
        return Math.round(r * 100) / 100;
    }
    function balance(inv) {
        return Math.round(((Number(inv.grandTotal) || 0) - received(inv)) * 100) / 100;
    }
    function isOverdue(inv) {
        if (!inv.dueDate) return false;
        if (balance(inv) <= 0) return false;
        return inv.dueDate < todayStr();
    }
    function effStatus(inv) {
        var b = balance(inv);
        if (b <= 0) return 'paid';
        if (isOverdue(inv)) return 'overdue';
        if (received(inv) > 0) return 'partial';
        return 'unpaid';
    }
    function statusBadge(st) {
        var cls = st === 'paid' ? 'bg-success' : (st === 'partial' ? 'bg-warning text-dark' : (st === 'overdue' ? 'bg-danger' : 'bg-secondary'));
        return '<span class="badge ' + cls + '">' + st + '</span>';
    }

    function render() {
        hideError();
        var invs = loadJson(INV_KEY, []);
        var f = statusFilter.value;
        var recv = 0, recd = 0, due = 0, over = 0;
        invs.forEach(function (inv) {
            recv += Number(inv.grandTotal) || 0;
            recd += received(inv);
            var b = balance(inv);
            due += b;
            if (isOverdue(inv)) over += b;
        });
        $('sumRecv').textContent = fmt(recv);
        $('sumRecd').textContent = fmt(recd);
        $('sumDue').textContent = fmt(due);
        $('sumOver').textContent = fmt(over);

        invRows.innerHTML = '';
        var shown = 0;
        invs.slice().reverse().forEach(function (inv) {
            var st = effStatus(inv);
            if (f !== 'all' && st !== f) return;
            shown++;
            var b = balance(inv);
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="fw-semibold">' + esc(inv.number) + '</td>' +
                '<td>' + esc(inv.client ? (inv.client.name || '—') : '—') + '</td>' +
                '<td>' + esc(inv.dueDate || '—') + (isOverdue(inv) ? ' <span class="badge bg-danger">late</span>' : '') + '</td>' +
                '<td class="text-end">' + fmt(inv.grandTotal) + '</td>' +
                '<td class="text-end text-success">' + fmt(received(inv)) + '</td>' +
                '<td class="text-end fw-semibold ' + (b > 0 ? 'text-danger' : 'text-success') + '">' + fmt(b) + '</td>' +
                '<td>' + statusBadge(st) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary pay-btn">Payment</button></td>';
            tr.querySelector('.pay-btn').addEventListener('click', function () { openPay(inv.id); });
            invRows.appendChild(tr);
        });
        invEmpty.style.display = invs.length ? (shown ? 'none' : '') : '';
        if (invs.length && !shown) invEmpty.textContent = 'No invoices in this filter.';
        else if (!invs.length) invEmpty.textContent = 'No invoices found. First make an invoice with the "Invoice Maker" tool.';
    }

    function findInv(id) {
        var invs = loadJson(INV_KEY, []);
        for (var i = 0; i < invs.length; i++) {
            if (invs[i].id === id) return { inv: invs[i], all: invs };
        }
        return null;
    }

    function openPay(id) {
        hideError();
        activeId = id;
        var found = findInv(id);
        if (!found) { showError('Invoice not found.'); return; }
        payInvNo.textContent = found.inv.number + ' — Balance: ' + fmt(balance(found.inv));
        payDate.value = todayStr();
        payAmount.value = '';
        payNote.value = '';
        payCard.classList.remove('d-none');
        renderPayHist(found.inv);
        payCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function renderPayHist(inv) {
        payHistRows.innerHTML = '';
        var pays = inv.payments || [];
        payHistEmpty.style.display = pays.length ? 'none' : '';
        pays.slice().reverse().forEach(function (p) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = p.date || '—';
            var tdA = document.createElement('td'); tdA.className = 'text-end'; tdA.textContent = fmt(p.amount);
            var tdN = document.createElement('td'); tdN.textContent = p.note || '—';
            var tdX = document.createElement('td'); tdX.className = 'text-end';
            var del = document.createElement('button');
            del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = '×';
            del.setAttribute('aria-label', 'Delete payment');
            del.addEventListener('click', function () {
                if (!confirm('Delete this payment entry?')) return;
                var f2 = findInv(activeId);
                if (!f2) return;
                f2.inv.payments = (f2.inv.payments || []).filter(function (x) { return x.id !== p.id; });
                saveJson(INV_KEY, f2.all);
                render(); renderPayHist(f2.inv);
                payInvNo.textContent = f2.inv.number + ' — Balance: ' + fmt(balance(f2.inv));
            });
            tdX.appendChild(del);
            tr.appendChild(tdD); tr.appendChild(tdA); tr.appendChild(tdN); tr.appendChild(tdX);
            payHistRows.appendChild(tr);
        });
    }

    function savePayment(markFull) {
        hideError();
        var found = findInv(activeId);
        if (!found) { showError('Invoice not found.'); return; }
        var inv = found.inv;
        var b = balance(inv);
        var amt = markFull ? b : (Number(payAmount.value) || 0);
        if (amt <= 0) { showError('Please enter an amount more than 0.'); return; }
        if (amt > b + 0.009) { showError('The amount cannot be more than the balance (' + fmt(b) + ').'); return; }
        if (!payDate.value) { showError('Please enter the payment date.'); return; }
        inv.payments = inv.payments || [];
        inv.payments.push({
            id: 'pay' + Date.now().toString(36) + Math.floor(Math.random() * 1e6).toString(36),
            amount: Math.round(amt * 100) / 100,
            date: payDate.value,
            note: payNote.value.trim()
        });
        var nb = balance(inv);
        inv.status = nb <= 0 ? 'paid' : (received(inv) > 0 ? 'partial' : 'unpaid');
        saveJson(INV_KEY, found.all);
        render(); renderPayHist(inv);
        payInvNo.textContent = inv.number + ' — Balance: ' + fmt(nb);
        payAmount.value = '';
        payNote.value = '';
    }

    $('paySaveBtn').addEventListener('click', function () { savePayment(false); });
    $('payFullBtn').addEventListener('click', function () { savePayment(true); });
    $('payCancelBtn').addEventListener('click', function () {
        payCard.classList.add('d-none');
        activeId = null;
    });
    statusFilter.addEventListener('change', render);
    $('reloadBtn').addEventListener('click', render);

    render();
})();
</script>
@endsection
