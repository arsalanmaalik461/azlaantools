@extends('layouts.app')

@section('title', 'Simple Balance Sheet - Azlaan Tools')
@section('meta_description', 'An at-a-glance view of assets, liabilities and equity — check instantly whether your balance sheet balances.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Simple Balance Sheet</h1>
            <p class="lead text-muted">An at-a-glance view of assets, liabilities and equity — formula: <strong>Assets = Liabilities + Equity</strong>. Your data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Add a new line</h2>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="bsCategory" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="bsCategory">
                                <optgroup label="Assets">
                                    <option value="asset|Cash &amp; Bank">Cash &amp; Bank</option>
                                    <option value="asset|Stock (Inventory)">Stock (Inventory)</option>
                                    <option value="asset|Receivables">Receivables</option>
                                    <option value="asset|Equipment / Property">Equipment / Property</option>
                                    <option value="asset|Other Assets">Other Assets</option>
                                </optgroup>
                                <optgroup label="Liabilities">
                                    <option value="liability|Payables">Payables</option>
                                    <option value="liability|Loans">Loans</option>
                                    <option value="liability|Other Liabilities">Other Liabilities</option>
                                </optgroup>
                                <optgroup label="Equity">
                                    <option value="equity|Owner Capital">Owner Capital</option>
                                    <option value="equity|Retained Earnings">Retained Earnings</option>
                                    <option value="equity|Other Equity">Other Equity</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="bsName" class="form-label fw-semibold">Line name</label>
                            <input type="text" class="form-control" id="bsName" placeholder="e.g. Bank account, shop stock">
                        </div>
                        <div class="col-md-3">
                            <label for="bsAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="bsAmount" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="bsAddBtn">Add Line</button>
                    <div class="alert alert-danger mt-3 d-none" id="bsError" role="alert"></div>
                </div>
            </div>

            <div class="alert d-flex justify-content-between align-items-center" id="bsCheckBox" role="alert">
                <span class="fw-semibold" id="bsCheck">Assets = Liabilities + Equity?</span>
                <span class="fw-bold" id="bsDiff"></span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white fw-semibold">Assets</div>
                        <div class="card-body">
                            <table class="table table-sm align-middle">
                                <tbody id="bsAssetRows"></tbody>
                            </table>
                            <p class="small text-muted" id="bsAssetEmpty">No asset lines yet.</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between fw-bold">
                            <span>Total</span><span id="bsAssetTotal">Rs 0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-warning fw-semibold">Liabilities</div>
                        <div class="card-body">
                            <table class="table table-sm align-middle">
                                <tbody id="bsLiabRows"></tbody>
                            </table>
                            <p class="small text-muted" id="bsLiabEmpty">No liability lines yet.</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between fw-bold">
                            <span>Total</span><span id="bsLiabTotal">Rs 0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white fw-semibold">Equity</div>
                        <div class="card-body">
                            <table class="table table-sm align-middle">
                                <tbody id="bsEqRows"></tbody>
                            </table>
                            <p class="small text-muted" id="bsEqEmpty">No equity lines yet.</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between fw-bold">
                            <span>Total</span><span id="bsEqTotal">Rs 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Balance sheet summary</h2>
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="small text-muted">Assets</div>
                                <div class="fw-bold text-primary" id="bsSumAssets">Rs 0</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="small text-muted">Liabilities</div>
                                <div class="fw-bold text-warning" id="bsSumLiab">Rs 0</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="small text-muted">Equity</div>
                                <div class="fw-bold text-success" id="bsSumEq">Rs 0</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="button" class="btn btn-outline-success" id="bsCsvBtn">CSV Download</button>
                        <button type="button" class="btn btn-outline-danger" id="bsClearBtn">Clear all data</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a category (Assets / Liabilities / Equity), then type the line name and amount.</li>
                <li>In Assets put everything you own: cash, stock, receivables. In Liabilities put what you owe: payables and loans. In Equity put your own invested money.</li>
                <li>In the end <strong>Assets = Liabilities + Equity</strong> must balance — if there is a difference, it will be shown here.</li>
            </ol>
            <p class="small text-muted">Note: your data is saved only in this browser, nothing is uploaded. This is a simplified informational statement — not audit-grade or tax-ready accounts.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_balance_sheet';
    var categoryEl = document.getElementById('bsCategory');
    var nameEl = document.getElementById('bsName');
    var amountEl = document.getElementById('bsAmount');
    var addBtn = document.getElementById('bsAddBtn');
    var errorBox = document.getElementById('bsError');
    var checkBox = document.getElementById('bsCheckBox');
    var checkEl = document.getElementById('bsCheck');
    var diffEl = document.getElementById('bsDiff');

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
                if (parsed && parsed.lines) return parsed;
            }
        } catch (e) {}
        return { lines: [] };
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
        return 'b' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function round2(n) {
        return Math.round(Number(n) * 100) / 100;
    }
    function sectionTotal(lines, section) {
        var t = 0;
        lines.forEach(function (l) {
            if (l.section === section) t += l.amount;
        });
        return round2(t);
    }

    function renderSection(lines, section, rowsId, emptyId) {
        var tbody = document.getElementById(rowsId);
        var emptyEl = document.getElementById(emptyId);
        tbody.innerHTML = '';
        var any = false;
        lines.forEach(function (l) {
            if (l.section !== section) return;
            any = true;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td>' + esc(l.category) + (l.name ? '<br><small class="text-muted">' + esc(l.name) + '</small>' : '') + '</td>' +
                '<td class="text-end text-nowrap">' + fmt(l.amount) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger del-btn" data-id="' + l.id + '" aria-label="Delete">×</button></td>';
            tbody.appendChild(tr);
        });
        emptyEl.style.display = any ? 'none' : '';
        tbody.querySelectorAll('.del-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var d = load();
                var id = btn.getAttribute('data-id');
                d.lines = d.lines.filter(function (l) { return l.id !== id; });
                save(d);
                render();
            });
        });
    }

    function render() {
        hideError();
        var data = load();
        renderSection(data.lines, 'asset', 'bsAssetRows', 'bsAssetEmpty');
        renderSection(data.lines, 'liability', 'bsLiabRows', 'bsLiabEmpty');
        renderSection(data.lines, 'equity', 'bsEqRows', 'bsEqEmpty');

        var a = sectionTotal(data.lines, 'asset');
        var li = sectionTotal(data.lines, 'liability');
        var eq = sectionTotal(data.lines, 'equity');
        var right = round2(li + eq);
        document.getElementById('bsAssetTotal').textContent = fmt(a);
        document.getElementById('bsLiabTotal').textContent = fmt(li);
        document.getElementById('bsEqTotal').textContent = fmt(eq);
        document.getElementById('bsSumAssets').textContent = fmt(a);
        document.getElementById('bsSumLiab').textContent = fmt(li);
        document.getElementById('bsSumEq').textContent = fmt(eq);

        checkBox.classList.remove('alert-success', 'alert-danger', 'alert-secondary');
        if (!data.lines.length) {
            checkBox.classList.add('alert-secondary');
            checkEl.textContent = 'No lines yet — the Assets = Liabilities + Equity check will appear here.';
            diffEl.textContent = '';
        } else {
            var diff = round2(a - right);
            if (diff === 0) {
                checkBox.classList.add('alert-success');
                checkEl.textContent = 'Balanced! Assets = Liabilities + Equity (' + fmt(a) + ').';
                diffEl.textContent = '';
            } else {
                checkBox.classList.add('alert-danger');
                checkEl.textContent = 'Not balanced — Liabilities + Equity differ by ' + fmt(Math.abs(diff)) + '.';
                diffEl.textContent = 'Difference: ' + fmt(Math.abs(diff));
            }
        }
    }

    addBtn.addEventListener('click', function () {
        hideError();
        var parts = categoryEl.value.split('|');
        var section = parts[0];
        var category = categoryEl.options[categoryEl.selectedIndex].textContent.trim();
        var name = nameEl.value.trim();
        var amount = Number(amountEl.value);
        if (!amount || amount <= 0) { showError('Enter an amount greater than 0.'); return; }
        var data = load();
        data.lines.push({
            id: uid(),
            section: section,
            category: category,
            name: name,
            amount: round2(amount)
        });
        save(data);
        nameEl.value = '';
        amountEl.value = '';
        render();
    });

    document.getElementById('bsCsvBtn').addEventListener('click', function () {
        hideError();
        var data = load();
        if (!data.lines.length) { showError('Add a line first to download a CSV.'); return; }
        var rows = ['Section,Category,Line,Amount'];
        data.lines.forEach(function (l) {
            rows.push(l.section + ',"' + l.category.replace(/"/g, '""') + '","' + String(l.name || '').replace(/"/g, '""') + '",' + l.amount);
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'balance-sheet-lite.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    document.getElementById('bsClearBtn').addEventListener('click', function () {
        hideError();
        if (!confirm('Clear all balance sheet data?')) return;
        try { localStorage.removeItem(KEY); } catch (e) {}
        render();
    });

    render();
})();
</script>
@endsection
