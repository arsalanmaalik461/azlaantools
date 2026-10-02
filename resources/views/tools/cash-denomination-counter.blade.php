@extends('layouts.app')

@section('title', 'Cash Denomination Counter - Azlaan Tools')
@section('meta_description', 'Enter counts of notes and coins to get the total cash and a note-wise breakdown. Free cash denomination counter.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Cash Denomination Counter</h1>
            <p class="lead text-muted">Enter how many notes and coins you have — the grand total and the note-wise breakdown are made automatically. Your data is saved only in your browser, it is never uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Cash Drawer / Till Count</h5>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="resetBtn">Reset All</button>
                            <button type="button" class="btn btn-success btn-sm" id="saveBtn">Save Count</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr><th>Note / Coin</th><th style="width:130px">Count</th><th class="text-end">Subtotal (Rs)</th></tr>
                            </thead>
                            <tbody id="denomRows"></tbody>
                            <tfoot>
                                <tr>
                                    <th>Total notes/coins: <span id="totalPieces">0</span></th>
                                    <th></th>
                                    <th class="text-end fs-5 text-primary">Rs <span id="grandTotal">0</span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="alert alert-danger d-none mt-2" id="errorBox" role="alert"></div>
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <button type="button" class="btn btn-outline-primary" id="copyBtn">Copy Summary</button>
                        <button type="button" class="btn btn-outline-success" id="shareBtn">Share (WhatsApp/SMS)</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Saved count sessions</h5>
                    <div id="sessionList"></div>
                    <p class="small text-muted mb-0" id="sessionEmpty">No saved counts yet. Enter counts above and press "Save Count".</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the <strong>count</strong> (how many notes) next to each note — the total and subtotal update instantly.</li>
                <li>With <strong>Copy Summary</strong> you can paste the breakdown into any message.</li>
                <li>At the end of the day press <strong>Save Count</strong> — it stays saved over time.</li>
            </ol>
            <p class="text-muted small">Note: this data stays only in your browser, it is never uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_cash_counts';
    var DENOMS = [5000, 1000, 500, 100, 50, 20, 10, 5, 1];
    var rowsEl = document.getElementById('denomRows');
    var totalPiecesEl = document.getElementById('totalPieces');
    var grandTotalEl = document.getElementById('grandTotal');
    var resetBtn = document.getElementById('resetBtn');
    var saveBtn = document.getElementById('saveBtn');
    var copyBtn = document.getElementById('copyBtn');
    var shareBtn = document.getElementById('shareBtn');
    var errorBox = document.getElementById('errorBox');
    var sessionList = document.getElementById('sessionList');
    var sessionEmpty = document.getElementById('sessionEmpty');
    var inputs = {};

    function fmt(n) {
        return Number(n || 0).toLocaleString('en-PK');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    DENOMS.forEach(function (d) {
        var tr = document.createElement('tr');
        var tdL = document.createElement('td');
        tdL.innerHTML = '<span class="badge bg-secondary me-2">Rs ' + fmt(d) + '</span>';
        tdL.innerHTML += d >= 50 ? 'note' : 'coin';
        var tdI = document.createElement('td');
        var inp = document.createElement('input');
        inp.type = 'number';
        inp.min = '0';
        inp.step = '1';
        inp.value = '';
        inp.placeholder = '0';
        inp.className = 'form-control form-control-sm';
        inp.setAttribute('aria-label', 'Rs ' + d + ' count');
        inp.id = 'd' + d;
        inp.addEventListener('input', recalc);
        inputs[d] = inp;
        tdI.appendChild(inp);
        var tdS = document.createElement('td');
        tdS.className = 'text-end';
        tdS.id = 's' + d;
        tdS.textContent = '0';
        tr.appendChild(tdL);
        tr.appendChild(tdI);
        tr.appendChild(tdS);
        rowsEl.appendChild(tr);
    });

    function counts() {
        var c = {};
        DENOMS.forEach(function (d) {
            var v = parseInt(inputs[d].value, 10);
            c[d] = isNaN(v) || v < 0 ? 0 : v;
        });
        return c;
    }
    function totalOf(c) {
        var t = 0;
        DENOMS.forEach(function (d) { t += (c[d] || 0) * d; });
        return t;
    }
    function recalc() {
        hideError();
        var c = counts();
        var pieces = 0, total = 0;
        DENOMS.forEach(function (d) {
            var sub = (c[d] || 0) * d;
            document.getElementById('s' + d).textContent = fmt(sub);
            pieces += (c[d] || 0);
            total += sub;
        });
        totalPiecesEl.textContent = fmt(pieces);
        grandTotalEl.textContent = fmt(total);
    }

    function summaryText(c) {
        c = c || counts();
        var lines = ['Cash Count — Azlaan Tools'];
        DENOMS.forEach(function (d) {
            if ((c[d] || 0) > 0) {
                lines.push('Rs ' + fmt(d) + ' x ' + c[d] + ' = Rs ' + fmt(c[d] * d));
            }
        });
        lines.push('Total: Rs ' + fmt(totalOf(c)));
        lines.push(new Date().toLocaleString('en-PK'));
        return lines.join('\n');
    }

    function copyText(txt, okMsg) {
        hideError();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt).then(function () {
                copyBtn.textContent = okMsg || 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy Summary'; }, 2000);
            }, function () { fallbackCopy(txt); });
        } else {
            fallbackCopy(txt);
        }
    }
    function fallbackCopy(txt) {
        var ta = document.createElement('textarea');
        ta.value = txt;
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy Summary'; }, 2000);
        } catch (e) {
            showError('Could not copy - please select the text manually.');
        }
        document.body.removeChild(ta);
    }

    copyBtn.addEventListener('click', function () {
        if (totalOf(counts()) === 0) { showError('Enter a count first.'); return; }
        copyText(summaryText());
    });
    shareBtn.addEventListener('click', function () {
        if (totalOf(counts()) === 0) { showError('Enter a count first.'); return; }
        var url = 'https://wa.me/?text=' + encodeURIComponent(summaryText());
        window.open(url, '_blank');
    });
    resetBtn.addEventListener('click', function () {
        DENOMS.forEach(function (d) { inputs[d].value = ''; });
        recalc();
    });

    function loadSessions() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function saveSessions(s) {
        try { localStorage.setItem(KEY, JSON.stringify(s)); }
        catch (e) { showError('Could not save - browser storage is blocked.'); }
    }
    function uid() {
        return 'cc' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }

    saveBtn.addEventListener('click', function () {
        hideError();
        var c = counts();
        if (totalOf(c) === 0) { showError('Enter a count first.'); return; }
        var sessions = loadSessions();
        sessions.unshift({
            id: uid(),
            ts: new Date().toLocaleString('en-PK'),
            counts: c,
            total: totalOf(c)
        });
        if (sessions.length > 60) sessions.length = 60;
        saveSessions(sessions);
        renderSessions();
        saveBtn.textContent = 'Saved!';
        setTimeout(function () { saveBtn.textContent = 'Save Count'; }, 2000);
    });

    function renderSessions() {
        var sessions = loadSessions();
        sessionList.innerHTML = '';
        sessionEmpty.style.display = sessions.length ? 'none' : '';
        sessions.forEach(function (s) {
            var div = document.createElement('div');
            div.className = 'border rounded p-2 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2';
            var left = document.createElement('div');
            var parts = [];
            DENOMS.forEach(function (d) {
                if (s.counts && (s.counts[d] || 0) > 0) parts.push('Rs' + d + '×' + s.counts[d]);
            });
            left.innerHTML = '<strong>Rs ' + fmt(s.total) + '</strong> <small class="text-muted">' +
                s.ts + '</small><br><small>' + parts.join(' · ') + '</small>';
            var right = document.createElement('div');
            var loadB = document.createElement('button');
            loadB.type = 'button';
            loadB.className = 'btn btn-sm btn-outline-primary me-2';
            loadB.textContent = 'Load';
            loadB.addEventListener('click', function () {
                DENOMS.forEach(function (d) {
                    inputs[d].value = (s.counts && s.counts[d]) ? s.counts[d] : '';
                });
                recalc();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            var delB = document.createElement('button');
            delB.type = 'button';
            delB.className = 'btn btn-sm btn-outline-danger';
            delB.textContent = 'Delete';
            delB.addEventListener('click', function () {
                if (!confirm('Delete this saved count?')) return;
                saveSessions(loadSessions().filter(function (x) { return x.id !== s.id; }));
                renderSessions();
            });
            right.appendChild(loadB);
            right.appendChild(delB);
            div.appendChild(left);
            div.appendChild(right);
            sessionList.appendChild(div);
        });
    }

    recalc();
    renderSessions();
})();
</script>
@endsection
