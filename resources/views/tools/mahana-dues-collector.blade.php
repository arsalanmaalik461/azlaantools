@extends('layouts.app')

@section('title', 'Monthly Dues Collector - Azlaan Tools')
@section('meta_description', 'Free monthly dues collector. Record for committee, monthly fee or collection groups — who paid and who did not.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Monthly Dues Collector</h1>
            <p class="lead text-muted">Record for committee, monthly fee or any collection group — who paid, who did not, how much was collected. Data is saved only in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Create a new group</h2>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="grpName" class="form-label fw-semibold">Group name</label>
                            <input type="text" class="form-control" id="grpName" placeholder="e.g. Mohalla committee, office fee">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="grpAmount" class="form-label fw-semibold">Fee per member (Rs)</label>
                            <input type="number" class="form-control" id="grpAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="grpFreq" class="form-label fw-semibold">Frequency</label>
                            <select class="form-select" id="grpFreq">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="one-time">One-time</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="addGrpBtn">Create Group</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="groupCards"></div>
            <p class="small text-muted" id="grpEmpty">No groups yet. Create one from above.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Past cycles history</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Group</th><th>Cycle</th><th class="text-end">Expected</th><th class="text-end">Collected</th><th class="text-end">Remaining</th></tr>
                            </thead>
                            <tbody id="histRows"></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0" id="histEmpty">No cycle closed yet.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li><strong>Create a group</strong> — enter a name, fee per member and select the frequency.</li>
                <li>In the group card, <strong>add members</strong> — mark <strong>Paid</strong> for whoever paid the fee.</li>
                <li>When the cycle ends, <strong>start a new cycle</strong> — the old cycle will be saved in history.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser, nothing is uploaded. Clearing your browser data will delete this record.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_mahana_groups';
    var grpName = document.getElementById('grpName');
    var grpAmount = document.getElementById('grpAmount');
    var grpFreq = document.getElementById('grpFreq');
    var addGrpBtn = document.getElementById('addGrpBtn');
    var errorBox = document.getElementById('errorBox');
    var groupCards = document.getElementById('groupCards');
    var grpEmpty = document.getElementById('grpEmpty');
    var histRows = document.getElementById('histRows');
    var histEmpty = document.getElementById('histEmpty');

    var data = { groups: [], history: [] };
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.groups) data = parsed;
            if (!data.history) data.history = [];
        }
    } catch (e) { data = { groups: [], history: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid(p) {
        return p + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function freqLabel(f) {
        if (f === 'weekly') return 'Weekly';
        if (f === 'one-time') return 'One-time';
        return 'Monthly';
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function todayYmd() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function getGroup(id) {
        for (var i = 0; i < data.groups.length; i++) {
            if (data.groups[i].id === id) return data.groups[i];
        }
        return null;
    }

    function renderGroups() {
        groupCards.innerHTML = '';
        if (!data.groups.length) { grpEmpty.style.display = ''; return; }
        grpEmpty.style.display = 'none';
        data.groups.forEach(function (g) {
            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3';
            var body = document.createElement('div');
            body.className = 'card-body';

            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-start flex-wrap gap-2 mb-3';
            var ht = document.createElement('div');
            var paidCount = g.members.filter(function (m) { return m.paid; }).length;
            var total = g.members.length;
            var expected = total * g.amount;
            var collected = paidCount * g.amount;
            var pct = total ? Math.round(paidCount / total * 100) : 0;
            ht.innerHTML = '<h3 class="h5 mb-1">' + esc(g.name) + '</h3>' +
                '<div class="small text-muted">' + freqLabel(g.frequency) + ' — ' + fmt(g.amount) + ' per member</div>';
            head.appendChild(ht);

            var hb = document.createElement('div');
            hb.className = 'd-flex gap-2 flex-wrap';
            var cycBtn = document.createElement('button');
            cycBtn.type = 'button'; cycBtn.className = 'btn btn-sm btn-outline-primary';
            cycBtn.textContent = 'Start new cycle';
            cycBtn.addEventListener('click', function () {
                if (!confirm('Close the cycle and start a new one? This cycle will be saved in history.')) return;
                data.history.unshift({
                    date: todayYmd(),
                    group: g.name,
                    cycle: freqLabel(g.frequency) + ' cycle #' + (g.cycle || 1),
                    expected: expected,
                    collected: collected
                });
                g.cycle = (g.cycle || 1) + 1;
                g.members.forEach(function (m) { m.paid = false; });
                save(); renderAll();
            });
            var delBtn = document.createElement('button');
            delBtn.type = 'button'; delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Group delete';
            delBtn.addEventListener('click', function () {
                if (!confirm(g.name + ' — delete this group? Members and data will be removed.')) return;
                data.groups = data.groups.filter(function (x) { return x.id !== g.id; });
                save(); renderAll();
            });
            hb.appendChild(cycBtn); hb.appendChild(delBtn);
            head.appendChild(hb);
            body.appendChild(head);

            var prog = document.createElement('div');
            prog.className = 'progress mb-2';
            prog.setAttribute('role', 'progressbar');
            prog.setAttribute('aria-valuenow', pct);
            prog.setAttribute('aria-valuemin', '0');
            prog.setAttribute('aria-valuemax', '100');
            var bar = document.createElement('div');
            bar.className = 'progress-bar' + (pct === 100 ? ' bg-success' : '');
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
            prog.appendChild(bar);
            body.appendChild(prog);

            var stats = document.createElement('div');
            stats.className = 'small text-muted mb-3';
            stats.textContent = paidCount + ' / ' + total + ' members paid — Collected: ' + fmt(collected) + ' / ' + fmt(expected);
            body.appendChild(stats);

            var addRow = document.createElement('div');
            addRow.className = 'input-group mb-2';
            var inp = document.createElement('input');
            inp.type = 'text'; inp.className = 'form-control';
            inp.placeholder = 'Member name';
            var ab = document.createElement('button');
            ab.type = 'button'; ab.className = 'btn btn-outline-primary'; ab.textContent = 'Add member';
            ab.addEventListener('click', function () {
                hideError();
                var nm = inp.value.trim();
                if (!nm) { showError('Enter the member name.'); return; }
                g.members.push({ id: uid('m'), name: nm, paid: false });
                save(); renderAll();
            });
            addRow.appendChild(inp); addRow.appendChild(ab);
            body.appendChild(addRow);

            if (g.members.length) {
                var list = document.createElement('div');
                list.className = 'list-group';
                g.members.forEach(function (m) {
                    var item = document.createElement('div');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    var nmSpan = document.createElement('span');
                    nmSpan.innerHTML = esc(m.name) + ' <small class="text-muted">(' + fmt(g.amount) + ')</small>';
                    var right = document.createElement('div');
                    right.className = 'd-flex gap-2 align-items-center';
                    var tg = document.createElement('button');
                    tg.type = 'button';
                    tg.className = 'btn btn-sm ' + (m.paid ? 'btn-success' : 'btn-outline-secondary');
                    tg.textContent = m.paid ? 'Paid ✓' : 'Unpaid';
                    tg.addEventListener('click', function () {
                        m.paid = !m.paid;
                        save(); renderAll();
                    });
                    var x = document.createElement('button');
                    x.type = 'button'; x.className = 'btn btn-sm btn-outline-danger';
                    x.textContent = '×';
                    x.setAttribute('aria-label', 'Delete member');
                    x.addEventListener('click', function () {
                        g.members = g.members.filter(function (mm) { return mm.id !== m.id; });
                        save(); renderAll();
                    });
                    right.appendChild(tg); right.appendChild(x);
                    item.appendChild(nmSpan); item.appendChild(right);
                    list.appendChild(item);
                });
                body.appendChild(list);
            } else {
                var none = document.createElement('p');
                none.className = 'small text-muted mb-0';
                none.textContent = 'No members yet. Add them from above.';
                body.appendChild(none);
            }

            card.appendChild(body);
            groupCards.appendChild(card);
        });
    }

    function renderHistory() {
        histRows.innerHTML = '';
        if (!data.history.length) { histEmpty.style.display = ''; return; }
        histEmpty.style.display = 'none';
        data.history.forEach(function (h) {
            var tr = document.createElement('tr');
            var tdD = document.createElement('td'); tdD.textContent = h.date;
            var tdG = document.createElement('td'); tdG.textContent = h.group;
            var tdC = document.createElement('td'); tdC.textContent = h.cycle;
            var tdE = document.createElement('td'); tdE.className = 'text-end'; tdE.textContent = fmt(h.expected);
            var tdCol = document.createElement('td'); tdCol.className = 'text-end text-success fw-bold'; tdCol.textContent = fmt(h.collected);
            var tdR = document.createElement('td'); tdR.className = 'text-end text-danger'; tdR.textContent = fmt(h.expected - h.collected);
            tr.appendChild(tdD); tr.appendChild(tdG); tr.appendChild(tdC);
            tr.appendChild(tdE); tr.appendChild(tdCol); tr.appendChild(tdR);
            histRows.appendChild(tr);
        });
    }

    function renderAll() {
        renderGroups();
        renderHistory();
    }

    addGrpBtn.addEventListener('click', function () {
        hideError();
        var name = grpName.value.trim();
        var amt = Number(grpAmount.value);
        if (!name) { showError('Enter the group name.'); return; }
        if (!amt || amt <= 0) { showError('Enter a correct fee per member (more than 0).'); return; }
        data.groups.push({
            id: uid('g'),
            name: name,
            amount: Math.round(amt * 100) / 100,
            frequency: grpFreq.value,
            cycle: 1,
            members: []
        });
        save();
        grpName.value = ''; grpAmount.value = '';
        renderAll();
    });

    renderAll();
})();
</script>
@endsection
