@extends('layouts.app')

@section('title', 'Bulk Defaulter Reminders - Azlaan Tools')
@section('meta_description', 'Generate polite English reminder messages for all your overdue customers at once, with a WhatsApp send link for each party.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Bulk Defaulter Reminders</h1>
            <p class="lead text-muted">Generate <strong>polite reminder messages</strong> for all your overdue customers at once. English text + a WhatsApp send link for every party. <span class="badge bg-info text-dark">Note:</span> messages open in WhatsApp — this app does not send SMS itself.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add parties</h5>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="brName" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="brName" placeholder="Customer name">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="brAmount" class="form-label fw-semibold">Balance (Rs)</label>
                            <input type="number" class="form-control" id="brAmount" placeholder="0" min="1" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="brPhone" class="form-label fw-semibold">Phone (optional)</label>
                            <input type="text" class="form-control" id="brPhone" placeholder="0300xxxxxxx">
                        </div>
                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="brAdd">Add</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="brImport" class="form-label fw-semibold">Or bulk import (paste below)</label>
                        <textarea class="form-control" id="brImport" rows="3" placeholder="One line per entry: name, amount, phone (optional)&#10;Example:&#10;Imran Bhai, 5000, 03001234567&#10;Nasir Store, 12500"></textarea>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="brImportBtn">Import</button>
                        <p class="text-muted small mt-1 mb-0">Format: <strong>name, amount, phone</strong> on every line — phone is optional, separate with commas.</p>
                    </div>
                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Message style</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="brTone" id="tonePolite" value="polite" checked>
                        <label class="form-check-label" for="tonePolite">Polite (soft tone — first reminder)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="brTone" id="toneFirm" value="firm">
                        <label class="form-check-label" for="toneFirm">Firm (strict tone — for repeated delays)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="brTone" id="toneFinal" value="final">
                        <label class="form-check-label" for="toneFinal">Final notice (last warning)</label>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" id="brGen">Generate Reminders</button>
                        <button type="button" class="btn btn-outline-primary" id="brCopyAll">Copy All</button>
                        <button type="button" class="btn btn-outline-danger" id="brClear">Clear List</button>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Generated reminders <span class="badge bg-secondary" id="brCount">0</span></h5>
            <div id="brOut"></div>
            <p class="text-muted small" id="brEmpty">No parties yet. Add parties above and click <strong>Generate Reminders</strong>.</p>

            <h2>How to use</h2>
            <ol>
                <li>Add customers — one by one, or paste them in the text box below for bulk import.</li>
                <li>Choose the tone (polite / firm / final notice).</li>
                <li>Click <strong>Generate Reminders</strong> — every party gets a ready message, and the <strong>WhatsApp</strong> button opens their chat directly.</li>
                <li>Use <strong>Copy All</strong> to copy all text at once and paste it anywhere.</li>
            </ol>
            <p class="text-muted small">Note: Data stays in your browser only, it is not uploaded anywhere. This tool <em>prepares</em> the messages — sending them is up to you.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_bulk_reminders';
    var brName = document.getElementById('brName');
    var brAmount = document.getElementById('brAmount');
    var brPhone = document.getElementById('brPhone');
    var brAdd = document.getElementById('brAdd');
    var brImport = document.getElementById('brImport');
    var brImportBtn = document.getElementById('brImportBtn');
    var brGen = document.getElementById('brGen');
    var brCopyAll = document.getElementById('brCopyAll');
    var brClear = document.getElementById('brClear');
    var brOut = document.getElementById('brOut');
    var brEmpty = document.getElementById('brEmpty');
    var brCount = document.getElementById('brCount');
    var errorBox = document.getElementById('errorBox');

    var parties = [];

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.parties) parties = parsed.parties;
        }
    } catch (e) { parties = []; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify({ parties: parties })); } catch (e) { /* ignore */ }
    }
    function uid() {
        return 'r' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK');
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function normPhone(p) {
        var d = String(p || '').replace(/\D/g, '');
        if (!d) return '';
        if (d.length === 11 && d.charAt(0) === '0') return '92' + d.slice(1);
        if (d.length === 10) return '92' + d;
        return d;
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function tone() {
        var els = document.getElementsByName('brTone');
        for (var i = 0; i < els.length; i++) {
            if (els[i].checked) return els[i].value;
        }
        return 'polite';
    }
    function makeMessage(p, t) {
        var amt = fmt(p.amount);
        var greeting = 'Hello ' + p.name + ',';
        if (t === 'polite') {
            return greeting + ' I hope you are well. This is a friendly request that your account balance of ' + amt + ' is still pending. Please pay it when you get time. Thank you. - Azlaan Store';
        }
        if (t === 'firm') {
            return greeting + ' please note that your account balance of ' + amt + ' is still not paid. Kindly pay it within 2 days so there is no further delay. Thank you. - Azlaan Store';
        }
        return greeting + ' this is the LAST reminder that your balance of ' + amt + ' is still pending. If it is not paid within 48 hours, your account will be closed. - Azlaan Store';
    }
    function waLink(p, msg) {
        var phone = normPhone(p.phone);
        var base = phone ? 'https://wa.me/' + phone : 'https://wa.me/';
        return base + '?text=' + encodeURIComponent(msg);
    }

    function renderList() {
        brCount.textContent = parties.length;
        if (!parties.length) {
            brOut.innerHTML = '';
            brEmpty.style.display = '';
            return;
        }
        brEmpty.style.display = 'none';
    }

    function generate() {
        hideError();
        if (!parties.length) { showError('Please add a party first.'); return; }
        var t = tone();
        brOut.innerHTML = '';
        brEmpty.style.display = 'none';
        parties.forEach(function (p) {
            var msg = makeMessage(p, t);
            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3';
            var body = document.createElement('div');
            body.className = 'card-body';

            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-2';
            var h = document.createElement('h6');
            h.className = 'mb-0';
            h.innerHTML = esc(p.name) + ' — <span class="text-danger">' + fmt(p.amount) + '</span>' + (p.phone ? ' <small class="text-muted">' + esc(p.phone) + '</small>' : '');
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '\u00D7';
            del.setAttribute('aria-label', 'Delete party');
            del.addEventListener('click', function () {
                parties = parties.filter(function (x) { return x.id !== p.id; });
                save();
                renderList();
                generate();
            });
            head.appendChild(h);
            head.appendChild(del);

            var pre = document.createElement('div');
            pre.className = 'border rounded bg-light p-2 mb-2 small';
            pre.style.whiteSpace = 'pre-wrap';
            pre.textContent = msg;

            var btns = document.createElement('div');
            btns.className = 'd-flex gap-2 flex-wrap';
            var wa = document.createElement('a');
            wa.className = 'btn btn-sm btn-success';
            wa.href = waLink(p, msg);
            wa.target = '_blank';
            wa.rel = 'noopener';
            wa.textContent = 'Send on WhatsApp';
            var cp = document.createElement('button');
            cp.type = 'button';
            cp.className = 'btn btn-sm btn-outline-secondary';
            cp.textContent = 'Copy';
            cp.addEventListener('click', function () {
                copyText(msg, cp);
            });
            btns.appendChild(wa);
            btns.appendChild(cp);

            body.appendChild(head);
            body.appendChild(pre);
            body.appendChild(btns);
            card.appendChild(body);
            brOut.appendChild(card);
        });
    }

    function copyText(text, btn) {
        function done(ok) {
            if (btn) {
                var old = btn.textContent;
                btn.textContent = ok ? 'Copied!' : 'Copy failed';
                setTimeout(function () { btn.textContent = old; }, 1500);
            }
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () { done(true); }, function () { done(false); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(true); } catch (e) { done(false); }
            document.body.removeChild(ta);
        }
    }

    function addParty(name, amount, phone) {
        parties.push({ id: uid(), name: name, amount: amount, phone: phone || '' });
    }

    brAdd.addEventListener('click', function () {
        hideError();
        var name = brName.value.trim();
        if (!name) { showError('Please write the name.'); return; }
        var amt = parseFloat(brAmount.value);
        if (isNaN(amt) || amt <= 0) { showError('Please write the correct balance amount.'); return; }
        addParty(name, Math.round(amt * 100) / 100, brPhone.value.trim());
        save();
        brName.value = ''; brAmount.value = ''; brPhone.value = '';
        renderList();
    });

    brImportBtn.addEventListener('click', function () {
        hideError();
        var text = brImport.value.trim();
        if (!text) { showError('Please paste the list first.'); return; }
        var added = 0, skipped = 0;
        text.split('\n').forEach(function (line) {
            var parts = line.split(',').map(function (x) { return x.trim(); });
            if (parts.length < 2 || !parts[0]) { skipped++; return; }
            var amt = parseFloat(parts[1].replace(/[^\d.]/g, ''));
            if (isNaN(amt) || amt <= 0) { skipped++; return; }
            addParty(parts[0], Math.round(amt * 100) / 100, parts[2] || '');
            added++;
        });
        save();
        brImport.value = '';
        renderList();
        if (!added) showError('No correct line found — format: name, amount, phone (optional).');
        else if (skipped) showError(added + ' parties added, ' + skipped + ' lines skipped (wrong format).');
    });

    brGen.addEventListener('click', generate);

    document.getElementById('tonePolite').addEventListener('change', function () { if (parties.length && brOut.children.length) generate(); });
    document.getElementById('toneFirm').addEventListener('change', function () { if (parties.length && brOut.children.length) generate(); });
    document.getElementById('toneFinal').addEventListener('change', function () { if (parties.length && brOut.children.length) generate(); });

    brCopyAll.addEventListener('click', function () {
        hideError();
        if (!parties.length) { showError('Add parties first to copy.'); return; }
        var t = tone();
        var all = parties.map(function (p) { return makeMessage(p, t); }).join('\n\n---\n\n');
        copyText(all, brCopyAll);
    });

    brClear.addEventListener('click', function () {
        hideError();
        if (!parties.length) return;
        if (!confirm('Clear the full party list?')) return;
        parties = [];
        save();
        renderList();
        brOut.innerHTML = '';
        brEmpty.style.display = '';
    });

    renderList();
})();
</script>
@endsection
