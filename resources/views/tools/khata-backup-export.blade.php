@extends('layouts.app')

@section('title', 'Ledger Backup & Restore - Azlaan Tools')
@section('meta_description', 'Free ledger backup and restore. Download a backup of all your tools data or restore it back.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Ledger Backup &amp; Restore</h1>
            <p class="lead text-muted">Download a backup of all your ledger data, and restore it back when you need it. <strong>This is a manual backup — there is no cloud sync.</strong> Your data is saved only in your browser; it is not uploaded anywhere.</p>

            <div class="alert alert-warning">
                <strong>Warning:</strong> Restoring will overwrite your current data. First download a fresh backup below.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Ledger data in your browser</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr><th>Data key</th><th class="text-end">Size</th><th>Type</th></tr>
                            </thead>
                            <tbody id="bkRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="bkEmpty">No ledger data found. When you use the ledger tools, the data will appear here.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Download a backup</h2>
                    <p class="text-muted small">A full backup saves all data in one JSON file. A CSV backup saves each record on one line (opens in Excel).</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="bkJsonBtn">Full JSON Backup Download</button>
                        <button type="button" class="btn btn-outline-success" id="bkCsvBtn">CSV Backup Download</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Restore a backup</h2>
                    <p class="text-muted small">Select the JSON backup file downloaded from this tool. You will be asked to <strong>confirm</strong> before the restore.</p>
                    <div class="mb-3">
                        <label for="bkFile" class="form-label fw-semibold">Backup JSON file</label>
                        <input type="file" class="form-control" id="bkFile" accept=".json,application/json">
                    </div>
                    <button type="button" class="btn btn-outline-danger" id="bkRestoreBtn">Restore (overwrite)</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Check the list above to see which ledger tools have data in your browser.</li>
                <li>Keep taking backups from time to time with <strong>Full JSON Backup Download</strong>.</li>
                <li>If you change your browser or your data is cleared, <strong>restore</strong> from that same JSON file.</li>
            </ol>
            <p class="text-muted small">Note: This backup downloads to your own device — nothing is sent to us or to any server. Keep the file in a safe place.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bkRows = document.getElementById('bkRows');
    var bkEmpty = document.getElementById('bkEmpty');
    var bkJsonBtn = document.getElementById('bkJsonBtn');
    var bkCsvBtn = document.getElementById('bkCsvBtn');
    var bkFile = document.getElementById('bkFile');
    var bkRestoreBtn = document.getElementById('bkRestoreBtn');
    var errorBox = document.getElementById('errorBox');
    var okBox = document.getElementById('okBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        okBox.classList.add('d-none');
    }
    function showOk(msg) {
        okBox.textContent = msg;
        okBox.classList.remove('d-none');
        errorBox.classList.add('d-none');
    }
    function hideMsgs() {
        errorBox.classList.add('d-none');
        okBox.classList.add('d-none');
    }
    function scanKeys() {
        var out = [];
        try {
            for (var i = 0; i < localStorage.length; i++) {
                var k = localStorage.key(i);
                if (k && k.indexOf('azlaan') === 0) {
                    out.push(k);
                }
            }
        } catch (e) {}
        out.sort();
        return out;
    }
    function describe(key) {
        var raw = '';
        try { raw = localStorage.getItem(key) || ''; } catch (e) {}
        var kind = 'text';
        try {
            var v = JSON.parse(raw);
            if (Array.isArray(v)) kind = 'list (' + v.length + ' records)';
            else if (v && typeof v === 'object') kind = 'object data';
        } catch (e) {}
        return { key: key, size: raw.length, kind: kind, raw: raw };
    }
    function fmtBytes(n) {
        if (n < 1024) return n + ' B';
        if (n < 1048576) return (n / 1024).toFixed(1) + ' KB';
        return (n / 1048576).toFixed(2) + ' MB';
    }
    function downloadBlob(blob, filename) {
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    }
    function backupName(ext) {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return 'azlaan-khata-backup-' + d.getFullYear() + m + day + '.' + ext;
    }

    function renderList() {
        var keys = scanKeys();
        bkRows.innerHTML = '';
        if (!keys.length) { bkEmpty.style.display = ''; return; }
        bkEmpty.style.display = 'none';
        keys.forEach(function (k) {
            var d = describe(k);
            var tr = document.createElement('tr');
            var tdK = document.createElement('td');
            tdK.innerHTML = '<code>' + d.key.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</code>';
            var tdS = document.createElement('td'); tdS.className = 'text-end'; tdS.textContent = fmtBytes(d.size);
            var tdT = document.createElement('td'); tdT.textContent = d.kind;
            tr.appendChild(tdK); tr.appendChild(tdS); tr.appendChild(tdT);
            bkRows.appendChild(tr);
        });
    }

    bkJsonBtn.addEventListener('click', function () {
        hideMsgs();
        var keys = scanKeys();
        if (!keys.length) { showError('No ledger data found for backup.'); return; }
        var obj = { app: 'azlaan-tools', exportedAt: new Date().toISOString(), data: {} };
        keys.forEach(function (k) {
            try { obj.data[k] = localStorage.getItem(k); } catch (e) {}
        });
        var blob = new Blob([JSON.stringify(obj)], { type: 'application/json;charset=utf-8' });
        downloadBlob(blob, backupName('json'));
        showOk('Backup of ' + keys.length + ' data keys downloaded.');
    });

    bkCsvBtn.addEventListener('click', function () {
        hideMsgs();
        var keys = scanKeys();
        if (!keys.length) { showError('No ledger data found for CSV.'); return; }
        var lines = ['key,record_index,json'];
        keys.forEach(function (k) {
            var raw = '';
            try { raw = localStorage.getItem(k) || ''; } catch (e) {}
            var parsed = null;
            try { parsed = JSON.parse(raw); } catch (e) {}
            if (Array.isArray(parsed)) {
                parsed.forEach(function (rec, i) {
                    lines.push('"' + k + '",' + i + ',"' + JSON.stringify(rec).replace(/"/g, '""') + '"');
                });
            } else if (parsed && typeof parsed === 'object') {
                Object.keys(parsed).forEach(function (sub, i) {
                    var v = parsed[sub];
                    var recs = Array.isArray(v) ? v : [v];
                    recs.forEach(function (rec, j) {
                        lines.push('"' + k + '.' + sub + '",' + (i + '_' + j) + ',"' + JSON.stringify(rec).replace(/"/g, '""') + '"');
                    });
                });
            } else {
                lines.push('"' + k + '",0,"' + raw.replace(/"/g, '""') + '"');
            }
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
        downloadBlob(blob, backupName('csv'));
        showOk('CSV backup downloaded.');
    });

    bkRestoreBtn.addEventListener('click', function () {
        hideMsgs();
        if (!bkFile.files || !bkFile.files.length) { showError('Select a backup JSON file first.'); return; }
        var file = bkFile.files[0];
        var reader = new FileReader();
        reader.onload = function () {
            var obj;
            try {
                obj = JSON.parse(reader.result);
            } catch (e) {
                showError('The file is not valid JSON. Use only a backup file downloaded from this tool.');
                return;
            }
            if (!obj || typeof obj !== 'object' || !obj.data || typeof obj.data !== 'object') {
                showError('This does not look like a valid backup file (data section not found).');
                return;
            }
            var keys = Object.keys(obj.data);
            if (!keys.length) { showError('No data found in the backup file.'); return; }
            if (!confirm(keys.length + ' data keys will be restored and the CURRENT data will be overwritten. Continue?')) return;
            var done = 0;
            keys.forEach(function (k) {
                try {
                    localStorage.setItem(k, obj.data[k]);
                    done++;
                } catch (e) {}
            });
            renderList();
            showOk(done + ' keys restored. Open the ledger tools to check your data.');
        };
        reader.onerror = function () { showError('Could not read the file. Please try again.'); };
        reader.readAsText(file);
    });

    renderList();
})();
</script>
@endsection
