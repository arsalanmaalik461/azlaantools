@extends('layouts.app')

@section('title', 'Finance Backup & CSV Export - Azlaan Tools')
@section('meta_description', 'Free finance backup tool: export all your saved expense and income data as CSV files or a single JSON backup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Finance Backup &amp; CSV Export</h1>
            <p class="lead text-muted">Download a backup of all your income and expenses as CSV. Data is saved only in your browser — it is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Datasets found in your browser</h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="scanBtn">Scan Again</button>
                    </div>
                    <div id="dsList"></div>
                    <p class="text-muted small mb-0" id="dsEmpty">No finance data found yet. First use a finance tool (budget, wallets, khata…).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Full backup (one file)</h2>
                    <p class="text-muted small">All datasets in one JSON backup file — later bring them back with <strong>Restore</strong> (new browser or device).</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" id="jsonBtn">Download JSON Backup</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="bkError" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="bkOk" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Restore (bring back your backup)</h2>
                    <p class="text-muted small">Choose a JSON backup file — it writes the data back to the same dataset keys. <strong>Your current data will be overwritten.</strong></p>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="file" class="form-control" id="restoreFile" accept=".json,application/json">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning w-100" id="restoreBtn">Restore</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Press <strong>Scan Again</strong> — all finance datasets in your browser will show with their entry counts.</li>
                <li>Press <strong>CSV</strong> on each dataset to download a file you can open in Excel.</li>
                <li>Use <strong>JSON Backup</strong> to save everything in one file, and Restore to bring it back.</li>
            </ol>
            <p class="text-muted small">Note: these files only download to your device — nothing goes to the server.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var DATASETS = [
        { key: 'azlaan7_budget', label: 'Monthly Budget Planner', kind: 'months' },
        { key: 'azlaan7_accounts', label: 'Multiple Wallets & Accounts', kind: 'accounts' },
        { key: 'azlaan7_transfers', label: 'Account Transfers', kind: 'transfers' },
        { key: 'azlaan7_recurring', label: 'Recurring Transactions', kind: 'recurring' },
        { key: 'azlaan7_bill_reminders', label: 'Bill & Subscription Reminders', kind: 'bills' },
        { key: 'azlaan_invoices', label: 'Invoices (azlaan_invoices)', kind: 'generic' }
    ];

    var scanBtn = document.getElementById('scanBtn');
    var dsList = document.getElementById('dsList');
    var dsEmpty = document.getElementById('dsEmpty');
    var jsonBtn = document.getElementById('jsonBtn');
    var restoreFile = document.getElementById('restoreFile');
    var restoreBtn = document.getElementById('restoreBtn');
    var bkError = document.getElementById('bkError');
    var bkOk = document.getElementById('bkOk');

    function readKey(key) {
        try {
            var raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : null;
        } catch (e) { return null; }
    }
    function showError(msg) {
        bkError.textContent = msg;
        bkError.classList.remove('d-none');
        bkOk.classList.add('d-none');
    }
    function showOk(msg) {
        bkOk.textContent = msg;
        bkOk.classList.remove('d-none');
        bkError.classList.add('d-none');
    }
    function hideMsgs() {
        bkError.classList.add('d-none'); bkOk.classList.add('d-none');
        bkError.textContent = ''; bkOk.textContent = '';
    }
    function csvEsc(v) {
        return '"' + String(v === null || v === undefined ? '' : v).replace(/"/g, '""') + '"';
    }
    function download(name, text, mime) {
        var blob = new Blob([text], { type: mime || 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    }
    function fmt(n) { return Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }

    function countEntries(ds, parsed) {
        if (!parsed) return 0;
        switch (ds.kind) {
            case 'months':
                var n = 0;
                if (parsed.months) {
                    Object.keys(parsed.months).forEach(function (m) {
                        n += (parsed.months[m].expenses || []).length;
                    });
                }
                return n;
            case 'accounts':
                return (parsed.txs || []).length;
            case 'transfers':
                return (parsed.transfers || []).length;
            case 'recurring':
                return (parsed.items || []).length + (parsed.done || []).length;
            case 'bills':
                return (parsed.bills || []).length + (parsed.paid || []).length;
            case 'generic':
                if (Array.isArray(parsed)) return parsed.length;
                if (parsed.items) return parsed.items.length;
                return Object.keys(parsed).length;
            default:
                return 0;
        }
    }

    function toCSV(ds, parsed) {
        var lines = [];
        switch (ds.kind) {
            case 'months':
                lines.push('Month,Category,Limit,Note,Amount');
                Object.keys(parsed.months || {}).forEach(function (m) {
                    var md = parsed.months[m];
                    var cats = {};
                    (md.cats || []).forEach(function (c) { cats[c.id] = c; });
                    (md.expenses || []).forEach(function (e) {
                        var c = cats[e.catId] || {};
                        lines.push([csvEsc(m), csvEsc(c.name || ''), csvEsc(c.limit === undefined ? '' : c.limit), csvEsc(e.note || ''), csvEsc(e.amount)].join(','));
                    });
                });
                break;
            case 'accounts':
                lines.push('Date,AccountId,Type,Amount,Note');
                (parsed.txs || []).forEach(function (t) {
                    lines.push([csvEsc(t.date), csvEsc(t.accId), csvEsc(t.type), csvEsc(t.amount), csvEsc(t.note || '')].join(','));
                });
                lines.push('');
                lines.push('Account,Type,Opening');
                (parsed.accounts || []).forEach(function (a) {
                    lines.push([csvEsc(a.name), csvEsc(a.type), csvEsc(a.opening)].join(','));
                });
                break;
            case 'transfers':
                lines.push('Date,From,To,Amount,Note');
                (parsed.transfers || []).forEach(function (t) {
                    lines.push([csvEsc(t.date), csvEsc(t.fromName), csvEsc(t.toName), csvEsc(t.amount), csvEsc(t.note || '')].join(','));
                });
                break;
            case 'recurring':
                lines.push('Name,Type,Amount,Frequency,NextDue');
                (parsed.items || []).forEach(function (i) {
                    lines.push([csvEsc(i.name), csvEsc(i.type), csvEsc(i.amount), csvEsc(i.freq), csvEsc(i.next)].join(','));
                });
                lines.push('');
                lines.push('CompletedDate,Name,Type,Amount');
                (parsed.done || []).forEach(function (e) {
                    lines.push([csvEsc(e.date), csvEsc(e.name), csvEsc(e.type), csvEsc(e.amount)].join(','));
                });
                break;
            case 'bills':
                lines.push('Name,Amount,Due,Repeat,Status');
                (parsed.bills || []).forEach(function (b) {
                    lines.push([csvEsc(b.name), csvEsc(b.amount), csvEsc(b.due), csvEsc(b.repeat), csvEsc(b.paid ? 'paid' : 'pending')].join(','));
                });
                lines.push('');
                lines.push('PaidDate,Name,Amount');
                (parsed.paid || []).forEach(function (p) {
                    lines.push([csvEsc(p.date), csvEsc(p.name), csvEsc(p.amount)].join(','));
                });
                break;
            default:
                lines.push('JSON backup — see JSON export for full structure');
                lines.push(csvEsc(JSON.stringify(parsed).slice(0, 30000)));
                break;
        }
        return lines.join('\n');
    }

    function scan() {
        hideMsgs();
        dsList.innerHTML = '';
        var found = 0;
        DATASETS.forEach(function (ds) {
            var parsed = readKey(ds.key);
            if (!parsed) return;
            found++;
            var n = countEntries(ds, parsed);
            var row = document.createElement('div');
            row.className = 'border rounded p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2';
            var left = document.createElement('div');
            var nm = document.createElement('div');
            nm.className = 'fw-bold';
            nm.textContent = ds.label;
            var meta = document.createElement('div');
            meta.className = 'small text-muted';
            meta.textContent = 'Key: ' + ds.key + ' • ' + n + ' entries';
            left.appendChild(nm);
            left.appendChild(meta);
            var right = document.createElement('div');
            right.className = 'd-flex gap-1';
            var csvBtn = document.createElement('button');
            csvBtn.type = 'button';
            csvBtn.className = 'btn btn-sm btn-outline-success';
            csvBtn.textContent = 'CSV Download';
            csvBtn.addEventListener('click', function () {
                hideMsgs();
                download(ds.key + '-export.csv', toCSV(ds, parsed));
                showOk(ds.label + ' CSV downloaded.');
            });
            var jBtn = document.createElement('button');
            jBtn.type = 'button';
            jBtn.className = 'btn btn-sm btn-outline-secondary';
            jBtn.textContent = 'JSON';
            jBtn.addEventListener('click', function () {
                hideMsgs();
                download(ds.key + '-backup.json', JSON.stringify(parsed, null, 2), 'application/json');
                showOk(ds.label + ' JSON backup downloaded.');
            });
            right.appendChild(csvBtn);
            right.appendChild(jBtn);
            row.appendChild(left);
            row.appendChild(right);
            dsList.appendChild(row);
        });
        dsEmpty.style.display = found ? 'none' : '';
    }

    jsonBtn.addEventListener('click', function () {
        hideMsgs();
        var backup = { app: 'azlaan-tools', exported_at: new Date().toISOString(), datasets: {} };
        var n = 0;
        DATASETS.forEach(function (ds) {
            var parsed = readKey(ds.key);
            if (parsed) {
                backup.datasets[ds.key] = parsed;
                n++;
            }
        });
        if (!n) { showError('No data found for backup.'); return; }
        download('azlaan-finance-backup-' + new Date().toISOString().slice(0, 10) + '.json', JSON.stringify(backup, null, 2), 'application/json');
        showOk('Full JSON backup of ' + n + ' datasets downloaded.');
    });

    restoreBtn.addEventListener('click', function () {
        hideMsgs();
        if (!restoreFile.files || !restoreFile.files.length) { showError('First choose a backup JSON file.'); return; }
        var f = restoreFile.files[0];
        var reader = new FileReader();
        reader.onload = function () {
            try {
                var obj = JSON.parse(reader.result);
                var datasets = obj && obj.datasets ? obj.datasets : obj;
                if (!datasets || typeof datasets !== 'object') { showError('No valid backup data found in this file.'); return; }
                var keys = Object.keys(datasets).filter(function (k) {
                    return k.indexOf('azlaan') === 0;
                });
                if (!keys.length) { showError('No azlaan finance dataset found in this file.'); return; }
                if (!confirm(keys.length + ' datasets will be restored and current data will be OVERWRITTEN. Continue?')) return;
                keys.forEach(function (k) {
                    try { localStorage.setItem(k, JSON.stringify(datasets[k])); } catch (e) {}
                });
                showOk(keys.length + ' datasets restored. Scan again to see them.');
                scan();
            } catch (e) {
                showError('Could not read the file — choose a correct JSON backup file.');
            }
        };
        reader.readAsText(f);
    });

    scanBtn.addEventListener('click', scan);
    scan();
})();
</script>
@endsection
