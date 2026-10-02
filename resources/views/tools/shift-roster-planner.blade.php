@extends('layouts.app')

@section('title', 'Weekly Shift Roster Planner - Azlaan Tools')
@section('meta_description', 'Make a staff-wise duty chart for the whole week of morning, evening and night shifts — free weekly roster planner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Weekly Shift Roster Planner</h1>
            <p class="lead text-muted">Make a staff-wise duty chart for the whole week's morning / evening / night shifts. Data is saved only in your browser — nothing is uploaded.</p>

            <div class="row">
                <div class="col-12 col-md-3 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Staff</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="staffName" placeholder="Staff name">
                                <button type="button" class="btn btn-primary" id="addStaffBtn">Add</button>
                            </div>
                            <div id="staffList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">Add staff, then select the shift for each day.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="weekStart" class="form-label fw-semibold mb-0">Week (from Monday)</label>
                                    <input type="date" class="form-control" id="weekStart" style="width:auto;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="prevWeekBtn">&larr; Previous</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="nextWeekBtn">Next &rarr;</button>
                                    <button type="button" class="btn btn-primary btn-sm" id="printBtn">Print Roster</button>
                                </div>
                            </div>
                            <div class="alert alert-danger mt-2 d-none" id="errorBox" role="alert"></div>
                            <div id="rosterWrap" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="rosterTable">
                                        <thead class="table-light">
                                            <tr id="rosterHead"><th>Staff</th></tr>
                                        </thead>
                                        <tbody id="rosterBody"></tbody>
                                    </table>
                                </div>
                                <div class="row text-center g-2 mb-2">
                                    <div class="col-6 col-sm-3"><div class="border rounded p-2"><div class="small text-muted">Morning shifts</div><div class="fw-bold text-warning" id="cntMorning">0</div></div></div>
                                    <div class="col-6 col-sm-3"><div class="border rounded p-2"><div class="small text-muted">Evening shifts</div><div class="fw-bold text-primary" id="cntEvening">0</div></div></div>
                                    <div class="col-6 col-sm-3"><div class="border rounded p-2"><div class="small text-muted">Night shifts</div><div class="fw-bold text-dark" id="cntNight">0</div></div></div>
                                    <div class="col-6 col-sm-3"><div class="border rounded p-2"><div class="small text-muted">Off days</div><div class="fw-bold text-success" id="cntOff">0</div></div></div>
                                </div>
                                <p class="text-muted small mb-0">Changes are saved as soon as you change a shift from the dropdown.</p>
                            </div>
                            <p class="text-muted small mt-3 mb-0 d-none" id="noStaff">First add staff — then the 7-day duty chart will show here.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click <strong>Add</strong> to add staff from the left side.</li>
                <li>Select the week's <strong>Monday</strong> (default: this week's Monday).</li>
                <li>In each staff row, set each day's shift from the dropdown: <strong>Morning</strong>, <strong>Evening</strong>, <strong>Night</strong> or <strong>Off</strong>.</li>
                <li>Press <strong>Print</strong> and put the roster printout on the wall.</li>
            </ol>
            <p class="text-muted small">Note: the data stays in this browser only. If you clear the browser data or use another device, this roster will not show.</p>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printSheet, #printSheet * { visibility: visible; }
    #printSheet { position: absolute; top: 0; left: 0; width: 100%; }
}
</style>
<div id="printSheet" class="d-none d-print-block"></div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_roster';
    var staffName = document.getElementById('staffName');
    var addStaffBtn = document.getElementById('addStaffBtn');
    var staffList = document.getElementById('staffList');
    var weekStart = document.getElementById('weekStart');
    var prevWeekBtn = document.getElementById('prevWeekBtn');
    var nextWeekBtn = document.getElementById('nextWeekBtn');
    var printBtn = document.getElementById('printBtn');
    var errorBox = document.getElementById('errorBox');
    var rosterWrap = document.getElementById('rosterWrap');
    var rosterHead = document.getElementById('rosterHead');
    var rosterBody = document.getElementById('rosterBody');
    var noStaff = document.getElementById('noStaff');
    var cntMorning = document.getElementById('cntMorning');
    var cntEvening = document.getElementById('cntEvening');
    var cntNight = document.getElementById('cntNight');
    var cntOff = document.getElementById('cntOff');
    var printSheet = document.getElementById('printSheet');

    var SHIFTS = [
        { id: 'off', label: 'Off', cls: 'bg-success' },
        { id: 'morning', label: 'Morning', cls: 'bg-warning text-dark' },
        { id: 'evening', label: 'Evening', cls: 'bg-primary' },
        { id: 'night', label: 'Night', cls: 'bg-dark' }
    ];
    var DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    var data = { staff: [], rosters: {} };
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.staff) data = parsed;
        }
    } catch (e) { data = { staff: [], rosters: {} }; }
    if (!data.rosters) data.rosters = {};

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
    }
    function uid() {
        return 'w' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function shiftLabel(id) {
        for (var i = 0; i < SHIFTS.length; i++) {
            if (SHIFTS[i].id === id) return SHIFTS[i].label;
        }
        return 'Off';
    }
    function shiftCls(id) {
        for (var i = 0; i < SHIFTS.length; i++) {
            if (SHIFTS[i].id === id) return SHIFTS[i].cls;
        }
        return 'bg-success';
    }
    function mondayOf(d) {
        var x = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        var day = (x.getDay() + 6) % 7;
        x.setDate(x.getDate() - day);
        return x;
    }
    function iso(d) {
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    function parseDate(s) {
        var p = String(s).split('-');
        return new Date(Number(p[0]), Number(p[1]) - 1, Number(p[2]));
    }
    function weekKey() {
        return weekStart.value || iso(mondayOf(new Date()));
    }
    function getRoster() {
        var k = weekKey();
        if (!data.rosters[k]) data.rosters[k] = {};
        return data.rosters[k];
    }
    function weekDates() {
        var start = parseDate(weekKey());
        var arr = [];
        for (var i = 0; i < 7; i++) {
            var d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
            arr.push(d);
        }
        return arr;
    }
    function fmtDate(d) {
        return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2);
    }

    function renderStaffList() {
        staffList.innerHTML = '';
        if (!data.staff.length) {
            staffList.innerHTML = '<div class="text-muted small">No staff.</div>';
            return;
        }
        data.staff.forEach(function (s) {
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            var nm = document.createElement('span');
            nm.textContent = s.name;
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = '×';
            del.setAttribute('aria-label', 'Delete staff');
            del.addEventListener('click', function () {
                if (!confirm('Delete ' + s.name + ' from the roster? (Old weeks data will also be removed)')) return;
                data.staff = data.staff.filter(function (x) { return x.id !== s.id; });
                Object.keys(data.rosters).forEach(function (k) {
                    delete data.rosters[k][s.id];
                });
                save();
                renderAll();
            });
            item.appendChild(nm);
            item.appendChild(del);
            staffList.appendChild(item);
        });
    }

    function renderRoster() {
        rosterHead.innerHTML = '';
        rosterBody.innerHTML = '';
        var th0 = document.createElement('th');
        th0.textContent = 'Staff';
        th0.style.minWidth = '120px';
        rosterHead.appendChild(th0);
        var dates = weekDates();
        dates.forEach(function (d, i) {
            var th = document.createElement('th');
            th.className = 'text-center';
            th.innerHTML = '';
            var b = document.createElement('div');
            b.textContent = DAYS[i];
            var sm = document.createElement('small');
            sm.className = 'text-muted';
            sm.textContent = fmtDate(d);
            th.appendChild(b);
            th.appendChild(sm);
            th.style.minWidth = '110px';
            rosterHead.appendChild(th);
        });

        var roster = getRoster();
        var counts = { morning: 0, evening: 0, night: 0, off: 0 };

        data.staff.forEach(function (s) {
            if (!roster[s.id]) roster[s.id] = {};
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.className = 'fw-semibold';
            tdN.textContent = s.name;
            tr.appendChild(tdN);
            for (var i = 0; i < 7; i++) {
                var selId = 'sel_' + s.id + '_' + i;
                var td = document.createElement('td');
                var sel = document.createElement('select');
                sel.className = 'form-select form-select-sm roster-sel';
                sel.id = selId;
                sel.setAttribute('data-staff', s.id);
                sel.setAttribute('data-day', String(i));
                SHIFTS.forEach(function (sh) {
                    var o = document.createElement('option');
                    o.value = sh.id;
                    o.textContent = sh.label;
                    sel.appendChild(o);
                });
                var cur = roster[s.id][String(i)] || 'off';
                sel.value = cur;
                sel.classList.add('text-white');
                sel.addEventListener('change', function () {
                    var sid = sel.getAttribute('data-staff');
                    var day = sel.getAttribute('data-day');
                    getRoster()[sid][day] = sel.value;
                    save();
                    renderRoster();
                });
                td.appendChild(sel);
                tr.appendChild(td);
                counts[cur] = (counts[cur] || 0) + 1;
            }
            rosterBody.appendChild(tr);
        });

        var sels = rosterBody.querySelectorAll('.roster-sel');
        sels.forEach(function (sel) {
            var id = sel.value;
            sel.classList.remove('bg-success', 'bg-warning', 'bg-primary', 'bg-dark', 'text-dark', 'text-white');
            var cls = shiftCls(id).split(' ');
            cls.forEach(function (c) { sel.classList.add(c); });
            if (id === 'morning') { sel.classList.remove('text-white'); sel.classList.add('text-dark'); }
        });

        cntMorning.textContent = counts.morning || 0;
        cntEvening.textContent = counts.evening || 0;
        cntNight.textContent = counts.night || 0;
        cntOff.textContent = counts.off || 0;

        rosterWrap.classList.toggle('d-none', !data.staff.length);
        noStaff.classList.toggle('d-none', !!data.staff.length);
    }

    function renderAll() {
        hideError();
        renderStaffList();
        renderRoster();
    }

    addStaffBtn.addEventListener('click', function () {
        hideError();
        var name = staffName.value.trim();
        if (!name) { showError('Write the staff name.'); return; }
        data.staff.push({ id: uid(), name: name });
        save();
        staffName.value = '';
        renderAll();
    });

    weekStart.addEventListener('change', renderRoster);
    prevWeekBtn.addEventListener('click', function () {
        var d = parseDate(weekKey());
        d.setDate(d.getDate() - 7);
        weekStart.value = iso(d);
        renderRoster();
    });
    nextWeekBtn.addEventListener('click', function () {
        var d = parseDate(weekKey());
        d.setDate(d.getDate() + 7);
        weekStart.value = iso(d);
        renderRoster();
    });

    printBtn.addEventListener('click', function () {
        hideError();
        if (!data.staff.length) { showError('First add staff.'); return; }
        var dates = weekDates();
        var roster = getRoster();
        var html = '<div class="p-3"><h3>Weekly Shift Roster</h3>' +
            '<p class="text-muted">Week: ' + fmtDate(dates[0]) + ' — ' + fmtDate(dates[6]) + '</p>' +
            '<table class="table table-bordered"><thead class="table-light"><tr><th>Staff</th>';
        dates.forEach(function (d, i) {
            html += '<th class="text-center">' + DAYS[i] + '<br><small>' + fmtDate(d) + '</small></th>';
        });
        html += '</tr></thead><tbody>';
        data.staff.forEach(function (s) {
            html += '<tr><td class="fw-bold">' + s.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</td>';
            for (var i = 0; i < 7; i++) {
                var cur = (roster[s.id] && roster[s.id][String(i)]) || 'off';
                html += '<td class="text-center">' + shiftLabel(cur) + '</td>';
            }
            html += '</tr>';
        });
        html += '</tbody></table><p class="text-muted small">Azlaan Tools — Weekly Shift Roster Planner</p></div>';
        printSheet.innerHTML = html;
        printSheet.classList.remove('d-none');
        window.print();
        printSheet.classList.add('d-none');
    });

    weekStart.value = iso(mondayOf(new Date()));
    renderAll();
})();
</script>
@endsection
