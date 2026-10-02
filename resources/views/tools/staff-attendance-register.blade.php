@extends('layouts.app')

@section('title', 'Staff Attendance Register - Azlaan Tools')
@section('meta_description', 'Monthly staff attendance register — track present, absent and leave, estimate wages and export CSV, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Staff Attendance Register</h1>
            <p class="lead text-muted">Monthly staff attendance — track present, absent and leave, estimate wages and export CSV. Your data stays in your browser.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="empName" class="form-label fw-semibold">Employee name</label>
                            <input type="text" class="form-control" id="empName" placeholder="e.g. Ahmed Khan">
                        </div>
                        <div class="col-md-3">
                            <label for="empWage" class="form-label fw-semibold">Daily wage (Rs)</label>
                            <input type="number" class="form-control" id="empWage" placeholder="1000" min="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="addEmpBtn">Add Employee</button>
                        </div>
                        <div class="col-md-2">
                            <label for="monthPick" class="form-label fw-semibold">Month</label>
                            <input type="month" class="form-control" id="monthPick">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-3">
                        <p class="small text-muted mb-2">Click a cell to change its status: <span class="badge bg-success">P</span> present · <span class="badge bg-danger">A</span> absent · <span class="badge bg-warning text-dark">L</span> leave · blank = not marked</p>
                        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                            <table class="table table-bordered table-sm align-middle text-center" id="attTable">
                                <thead class="table-light position-sticky top-0">
                                    <tr id="attHeadRow"><th class="text-start">Employee</th></tr>
                                </thead>
                                <tbody id="attBody"></tbody>
                            </table>
                        </div>
                        <h6 class="mt-4">Monthly summary</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="sumTable">
                                <thead class="table-light">
                                    <tr><th>Employee</th><th>P</th><th>A</th><th>L</th><th>Attendance %</th><th>Wage (Rs)</th><th></th></tr>
                                </thead>
                                <tbody id="sumBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="btn btn-outline-primary" id="csvBtn">Export CSV</button>
                            <button type="button" class="btn btn-outline-secondary" id="printBtn">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter an employee name and daily wage, then press <strong>Add</strong> (you can add more than one).</li>
                <li>Select the month, then click each date to mark P / A / L.</li>
                <li>Check attendance % and the wage estimate in the summary — download a CSV or print.</li>
            </ol>
            <p class="small text-muted">This is only an estimate — confirm the final wage with your own records.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var empName = document.getElementById('empName');
    var empWage = document.getElementById('empWage');
    var monthPick = document.getElementById('monthPick');

    var STORE_KEY = 'azlaan_attendance_v1';
    var STATUS = ['', 'P', 'A', 'L']; // cycle order
    var data = loadData();

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function loadData() {
        try { return JSON.parse(localStorage.getItem(STORE_KEY)) || { employees: [], marks: {} }; }
        catch (e) { return { employees: [], marks: {} }; }
    }
    function saveData() {
        try { localStorage.setItem(STORE_KEY, JSON.stringify(data)); } catch (e) {}
    }
    function monthKey() { return monthPick.value || new Date().toISOString().slice(0, 7); }
    function daysInMonth() {
        var parts = monthKey().split('-');
        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10), 0).getDate();
    }
    function markKey(empId, day) { return monthKey() + '|' + empId + '|' + day; }

    var now = new Date();
    monthPick.value = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');

    document.getElementById('addEmpBtn').addEventListener('click', function () {
        hideError();
        var name = empName.value.trim();
        if (!name) { showError('Please enter the employee name.'); return; }
        var wage = parseFloat(empWage.value);
        if (isNaN(wage) || wage < 0) wage = 0;
        var id = 'e' + Date.now();
        data.employees.push({ id: id, name: name, wage: wage });
        saveData();
        empName.value = ''; empWage.value = '';
        render();
    });

    monthPick.addEventListener('change', function () { hideError(); render(); });

    function render() {
        hideError();
        var headRow = document.getElementById('attHeadRow');
        var body = document.getElementById('attBody');
        headRow.innerHTML = '<th class="text-start">Employee</th>';
        body.innerHTML = '';
        if (data.employees.length === 0) {
            results.classList.add('d-none');
            showError('No employee added yet — enter a name above and press Add.');
            return;
        }
        results.classList.remove('d-none');
        var days = daysInMonth();
        var d;
        for (d = 1; d <= days; d++) {
            var th = document.createElement('th');
            th.textContent = d;
            th.className = 'px-1';
            headRow.appendChild(th);
        }
        data.employees.forEach(function (emp) {
            var tr = document.createElement('tr');
            var td0 = document.createElement('td');
            td0.className = 'text-start fw-semibold';
            td0.textContent = emp.name;
            tr.appendChild(td0);
            for (d = 1; d <= days; d++) {
                (function (empId, day) {
                    var td = document.createElement('td');
                    td.style.cursor = 'pointer';
                    td.style.minWidth = '34px';
                    var mk = markKey(empId, day);
                    td.textContent = data.marks[mk] || '';
                    paintCell(td, data.marks[mk]);
                    td.addEventListener('click', function () {
                        var cur = data.marks[mk] || '';
                        var next = STATUS[(STATUS.indexOf(cur) + 1) % STATUS.length];
                        if (next === '') delete data.marks[mk]; else data.marks[mk] = next;
                        saveData();
                        td.textContent = next;
                        paintCell(td, next);
                        renderSummary();
                    });
                    tr.appendChild(td);
                })(emp.id, d);
            }
            body.appendChild(tr);
        });
        renderSummary();
    }

    function paintCell(td, st) {
        td.classList.remove('table-success', 'table-danger', 'table-warning', 'fw-bold');
        if (st === 'P') td.classList.add('table-success', 'fw-bold');
        else if (st === 'A') td.classList.add('table-danger', 'fw-bold');
        else if (st === 'L') td.classList.add('table-warning', 'fw-bold');
    }

    function renderSummary() {
        var sumBody = document.getElementById('sumBody');
        sumBody.innerHTML = '';
        var days = daysInMonth();
        data.employees.forEach(function (emp) {
            var p = 0, a = 0, l = 0, d;
            for (d = 1; d <= days; d++) {
                var s = data.marks[markKey(emp.id, d)];
                if (s === 'P') p++;
                else if (s === 'A') a++;
                else if (s === 'L') l++;
            }
            var marked = p + a + l;
            var pct = marked > 0 ? Math.round((p + l * 0.5) / marked * 100) : 0;
            var salary = Math.round((p + l * 0.5) * emp.wage);
            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="fw-semibold">' + escapeHtml(emp.name) + '</td>' +
                '<td class="text-success fw-bold">' + p + '</td>' +
                '<td class="text-danger fw-bold">' + a + '</td>' +
                '<td class="text-warning fw-bold">' + l + '</td>' +
                '<td>' + pct + '%</td>' +
                '<td>' + salary.toLocaleString('en-PK') + '</td>';
            var tdDel = document.createElement('td');
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Remove';
            delBtn.addEventListener('click', function () {
                data.employees = data.employees.filter(function (e) { return e.id !== emp.id; });
                Object.keys(data.marks).forEach(function (k) {
                    if (k.indexOf('|' + emp.id + '|') > -1) delete data.marks[k];
                });
                saveData();
                render();
            });
            tdDel.appendChild(delBtn);
            tr.appendChild(tdDel);
            sumBody.appendChild(tr);
        });
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    document.getElementById('csvBtn').addEventListener('click', function () {
        hideError();
        var days = daysInMonth();
        var rows = [];
        var header = ['Employee', 'Daily Wage'];
        var d;
        for (d = 1; d <= days; d++) header.push(String(d));
        header.push('P', 'A', 'L', 'Salary');
        rows.push(header);
        data.employees.forEach(function (emp) {
            var row = [emp.name, emp.wage];
            var p = 0, a = 0, l = 0;
            for (d = 1; d <= days; d++) {
                var s = data.marks[markKey(emp.id, d)] || '';
                row.push(s);
                if (s === 'P') p++; else if (s === 'A') a++; else if (s === 'L') l++;
            }
            row.push(p, a, l, Math.round((p + l * 0.5) * emp.wage));
            rows.push(row);
        });
        var csv = rows.map(function (r) {
            return r.map(function (c) { return '"' + String(c).replace(/"/g, '""') + '"'; }).join(',');
        }).join('\n');
        var blob = new Blob(["\ufeff" + csv], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'attendance-' + monthKey() + '.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); document.body.removeChild(a); }, 100);
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        hideError();
        window.print();
    });

    render();
})();
</script>
@endsection
