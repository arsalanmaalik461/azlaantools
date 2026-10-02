@extends('layouts.app')

@section('title', 'Staff Leave Balance Tracker - Azlaan Tools')
@section('meta_description', 'Track leave for every employee — casual, sick and annual leave: entitlement vs used balance tracker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Staff Leave Balance Tracker</h1>
            <p class="lead text-muted">Track leave entitlement vs used for every employee — auto balance for casual, sick and annual leave. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Add staff</h5>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="staffName" placeholder="Staff name">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-4">
                                    <label for="entCasual" class="form-label small mb-1">Casual</label>
                                    <input type="number" class="form-control form-control-sm" id="entCasual" value="12" min="0">
                                </div>
                                <div class="col-4">
                                    <label for="entSick" class="form-label small mb-1">Sick</label>
                                    <input type="number" class="form-control form-control-sm" id="entSick" value="10" min="0">
                                </div>
                                <div class="col-4">
                                    <label for="entAnnual" class="form-label small mb-1">Annual</label>
                                    <input type="number" class="form-control form-control-sm" id="entAnnual" value="14" min="0">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="addStaffBtn">Add Staff</button>
                            <p class="text-muted small mt-2 mb-0">Yearly entitlement is set by default; you can also change the entitlement later on each staff card.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Log leave</h5>
                            <div class="row g-2 mb-3">
                                <div class="col-12 col-sm-4">
                                    <select class="form-select" id="leaveStaff"></select>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <input type="date" class="form-control" id="leaveDate">
                                </div>
                                <div class="col-6 col-sm-3">
                                    <select class="form-select" id="leaveType">
                                        <option value="casual">Casual leave</option>
                                        <option value="sick">Sick leave</option>
                                        <option value="annual">Annual leave</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <button type="button" class="btn btn-primary w-100" id="logLeaveBtn">Log</button>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="leaveNote" placeholder="Note (e.g. sick, home work)">
                                </div>
                            </div>
                            <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                            <div id="staffCards"></div>
                            <p class="text-muted small mt-3 mb-0" id="noStaff">No staff yet. Add staff from the left side.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the staff name and yearly leave entitlement, then press <strong>Add Staff</strong>.</li>
                <li>Select the staff, date and leave type for the leave day, then press <strong>Log</strong>.</li>
                <li>Each card will automatically show entitlement, used and <strong>remaining balance</strong>.</li>
            </ol>
            <p class="text-muted small">Note: data stays safe in this browser. If you clear browser data or use another device, this record will not appear.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_leaves';
    var staffName = document.getElementById('staffName');
    var entCasual = document.getElementById('entCasual');
    var entSick = document.getElementById('entSick');
    var entAnnual = document.getElementById('entAnnual');
    var addStaffBtn = document.getElementById('addStaffBtn');
    var leaveStaff = document.getElementById('leaveStaff');
    var leaveDate = document.getElementById('leaveDate');
    var leaveType = document.getElementById('leaveType');
    var leaveNote = document.getElementById('leaveNote');
    var logLeaveBtn = document.getElementById('logLeaveBtn');
    var errorBox = document.getElementById('errorBox');
    var staffCards = document.getElementById('staffCards');
    var noStaff = document.getElementById('noStaff');

    var TYPES = [
        { id: 'casual', label: 'Casual' },
        { id: 'sick', label: 'Sick' },
        { id: 'annual', label: 'Annual' }
    ];

    var data = { staff: [] };

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.staff) data = parsed;
        }
    } catch (e) { data = { staff: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 'l' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function getStaff(id) {
        for (var i = 0; i < data.staff.length; i++) {
            if (data.staff[i].id === id) return data.staff[i];
        }
        return null;
    }
    function todayStr() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function usedOf(s, type) {
        var c = 0;
        for (var i = 0; i < s.leaves.length; i++) {
            if (s.leaves[i].type === type) c++;
        }
        return c;
    }

    function renderStaffSelect() {
        leaveStaff.innerHTML = '';
        data.staff.forEach(function (s) {
            var o = document.createElement('option');
            o.value = s.id;
            o.textContent = s.name;
            leaveStaff.appendChild(o);
        });
    }

    function renderCards() {
        staffCards.innerHTML = '';
        noStaff.style.display = data.staff.length ? 'none' : '';
        data.staff.forEach(function (s) {
            var card = document.createElement('div');
            card.className = 'card mb-3';

            var head = document.createElement('div');
            head.className = 'card-header d-flex justify-content-between align-items-center';
            var hName = document.createElement('strong');
            hName.textContent = s.name;
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Delete';
            delBtn.addEventListener('click', function () {
                if (!confirm('Delete the leave record of ' + s.name + '?')) return;
                data.staff = data.staff.filter(function (x) { return x.id !== s.id; });
                save();
                renderAll();
            });
            head.appendChild(hName);
            head.appendChild(delBtn);
            card.appendChild(head);

            var body = document.createElement('div');
            body.className = 'card-body';

            var tbl = document.createElement('table');
            tbl.className = 'table table-sm table-bordered mb-3';
            var thead = document.createElement('thead');
            thead.className = 'table-light';
            thead.innerHTML = '<tr><th>Leave type</th><th class="text-end">Entitlement</th><th class="text-end">Used</th><th class="text-end">Left</th></tr>';
            tbl.appendChild(thead);
            var tb = document.createElement('tbody');
            TYPES.forEach(function (t) {
                var ent = Number(s.entitlement[t.id]) || 0;
                var used = usedOf(s, t.id);
                var rem = ent - used;
                var tr = document.createElement('tr');
                tr.innerHTML = '<td>' + t.label + '</td><td class="text-end">' + ent + '</td><td class="text-end">' + used + '</td>' +
                    '<td class="text-end fw-bold ' + (rem < 0 ? 'text-danger' : 'text-success') + '">' + rem + '</td>';
                tb.appendChild(tr);
            });
            tbl.appendChild(tb);
            body.appendChild(tbl);

            var entRow = document.createElement('div');
            entRow.className = 'row g-2 mb-3';
            TYPES.forEach(function (t) {
                var col = document.createElement('div');
                col.className = 'col-4';
                var lbl = document.createElement('label');
                lbl.className = 'form-label small mb-1';
                lbl.textContent = t.label + ' entitlement';
                var inp = document.createElement('input');
                inp.type = 'number';
                inp.min = '0';
                inp.className = 'form-control form-control-sm ent-input';
                inp.value = s.entitlement[t.id];
                inp.setAttribute('data-staff', s.id);
                inp.setAttribute('data-type', t.id);
                col.appendChild(lbl);
                col.appendChild(inp);
                entRow.appendChild(col);
            });
            body.appendChild(entRow);

            var h6 = document.createElement('h6');
            h6.className = 'text-muted';
            h6.textContent = 'Logged leaves (' + s.leaves.length + ')';
            body.appendChild(h6);

            if (s.leaves.length) {
                var ltbl = document.createElement('div');
                ltbl.className = 'table-responsive';
                var lt = document.createElement('table');
                lt.className = 'table table-sm table-striped align-middle mb-0';
                lt.innerHTML = '<thead class="table-light"><tr><th>Date</th><th>Type</th><th>Note</th><th></th></tr></thead>';
                var ltb = document.createElement('tbody');
                var sorted = s.leaves.slice().sort(function (a, b) { return a.date < b.date ? 1 : -1; });
                sorted.forEach(function (lv) {
                    var tr = document.createElement('tr');
                    var tdD = document.createElement('td'); tdD.textContent = lv.date;
                    var tdT = document.createElement('td');
                    tdT.innerHTML = '<span class="badge bg-secondary">' + esc(leaveLabel(lv.type)) + '</span>';
                    var tdN = document.createElement('td'); tdN.innerHTML = esc(lv.note || '—');
                    var tdX = document.createElement('td');
                    tdX.className = 'text-end';
                    var del = document.createElement('button');
                    del.type = 'button';
                    del.className = 'btn btn-sm btn-outline-danger';
                    del.textContent = '×';
                    del.addEventListener('click', function () {
                        s.leaves = s.leaves.filter(function (x) { return x.id !== lv.id; });
                        save();
                        renderAll();
                    });
                    tdX.appendChild(del);
                    tr.appendChild(tdD); tr.appendChild(tdT); tr.appendChild(tdN); tr.appendChild(tdX);
                    ltb.appendChild(tr);
                });
                lt.appendChild(ltb);
                ltbl.appendChild(lt);
                body.appendChild(ltbl);
            } else {
                var none = document.createElement('p');
                none.className = 'text-muted small mb-0';
                none.textContent = 'No leave logged yet.';
                body.appendChild(none);
            }

            card.appendChild(body);
            staffCards.appendChild(card);
        });

        var ents = staffCards.querySelectorAll('.ent-input');
        ents.forEach(function (inp) {
            inp.addEventListener('change', function () {
                var s = getStaff(inp.getAttribute('data-staff'));
                if (!s) return;
                s.entitlement[inp.getAttribute('data-type')] = Math.max(0, Number(inp.value) || 0);
                save();
                renderAll();
            });
        });
    }

    function leaveLabel(id) {
        for (var i = 0; i < TYPES.length; i++) {
            if (TYPES[i].id === id) return TYPES[i].label;
        }
        return id;
    }

    function renderAll() {
        hideError();
        renderStaffSelect();
        renderCards();
    }

    addStaffBtn.addEventListener('click', function () {
        hideError();
        var name = staffName.value.trim();
        if (!name) { showError('Enter the staff name.'); return; }
        data.staff.push({
            id: uid(),
            name: name,
            entitlement: {
                casual: Math.max(0, Number(entCasual.value) || 0),
                sick: Math.max(0, Number(entSick.value) || 0),
                annual: Math.max(0, Number(entAnnual.value) || 0)
            },
            leaves: []
        });
        save();
        staffName.value = '';
        renderAll();
    });

    logLeaveBtn.addEventListener('click', function () {
        hideError();
        var s = getStaff(leaveStaff.value);
        if (!s) { showError('Add staff first.'); return; }
        var date = leaveDate.value || todayStr();
        var type = leaveType.value;
        var rem = Number(s.entitlement[type]) - usedOf(s, type);
        if (rem <= 0 && !confirm(leaveLabel(type) + ' balance is finished — log anyway?')) return;
        s.leaves.push({
            id: uid(),
            date: date,
            type: type,
            note: leaveNote.value.trim()
        });
        save();
        leaveNote.value = '';
        renderAll();
    });

    leaveDate.value = todayStr();
    renderAll();
})();
</script>
@endsection
