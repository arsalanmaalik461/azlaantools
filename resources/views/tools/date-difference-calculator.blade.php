@extends('layouts.app')

@section('title', 'Date Difference Calculator - Azlaan Tools')
@section('meta_description', 'Free date difference calculator. Find the number of days, weeks, months and years between two dates, and add or subtract days from any date.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Date Difference Calculator</h1>
            <p class="lead text-muted">Calculate the exact difference between two dates, or add and subtract days from a date to find a future or past date.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">Difference Between Two Dates</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="diffBtn">Calculate Difference</button>
                    <div class="alert alert-danger mt-3 d-none" id="diffError" role="alert"></div>
                    <div id="diffResults" class="d-none mt-4">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="diffDays">0</div><div class="text-muted small">Days</div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="diffWeeks">0</div><div class="text-muted small">Weeks</div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="diffMonths">0</div><div class="text-muted small">Months (approx.)</div></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100"><div class="fs-4 fw-bold" id="diffYears">0</div><div class="text-muted small">Years (approx.)</div></div>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3 mb-0 text-center" id="diffBreakdown">-</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">Add / Subtract Days</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="baseDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="baseDate">
                        </div>
                        <div class="col-md-3">
                            <label for="daysAmount" class="form-label fw-semibold">Days</label>
                            <input type="number" class="form-control" id="daysAmount" value="30" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="operation" class="form-label fw-semibold">Operation</label>
                            <select class="form-select" id="operation">
                                <option value="add">Add days</option>
                                <option value="subtract">Subtract days</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success w-100 mt-3" id="addSubBtn">Calculate Date</button>
                    <div class="alert alert-danger mt-3 d-none" id="addSubError" role="alert"></div>
                    <div class="alert alert-info mt-3 mb-0 d-none text-center fw-semibold" id="addSubResult"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>For the difference tool, pick a start date and an end date, then click <strong>Calculate Difference</strong>.</li>
                <li>Read the gap in days, weeks, months and years, plus the years / months / days breakdown.</li>
                <li>For the mini-tool, pick a date, enter a number of days, choose Add or Subtract, then click <strong>Calculate Date</strong> to see the resulting date.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function parseDate(val) {
        if (!val) return null;
        var p = val.split('-');
        return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    }
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    document.getElementById('startDate').value = todayStr();
    document.getElementById('endDate').value = todayStr();
    document.getElementById('baseDate').value = todayStr();

    document.getElementById('diffBtn').addEventListener('click', function () {
        var err = document.getElementById('diffError');
        var res = document.getElementById('diffResults');
        err.classList.add('d-none');
        var start = parseDate(document.getElementById('startDate').value);
        var end = parseDate(document.getElementById('endDate').value);
        if (!start || !end) { err.textContent = 'Please select both dates.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        if (end < start) { var tmp = start; start = end; end = tmp; }
        var totalDays = Math.round((end - start) / 86400000);
        var years = end.getFullYear() - start.getFullYear();
        var months = end.getMonth() - start.getMonth();
        var days = end.getDate() - start.getDate();
        var borrowCursor = new Date(end.getFullYear(), end.getMonth(), 1);
        while (days < 0) { months--; borrowCursor.setMonth(borrowCursor.getMonth() - 1); days += new Date(borrowCursor.getFullYear(), borrowCursor.getMonth() + 1, 0).getDate(); }
        if (months < 0) { years--; months += 12; }
        document.getElementById('diffDays').textContent = totalDays.toLocaleString();
        document.getElementById('diffWeeks').textContent = (totalDays / 7).toFixed(2);
        document.getElementById('diffMonths').textContent = (totalDays / 30.4375).toFixed(2);
        document.getElementById('diffYears').textContent = (totalDays / 365.25).toFixed(2);
        document.getElementById('diffBreakdown').textContent = years + ' years, ' + months + ' months, ' + days + ' days';
        res.classList.remove('d-none');
    });

    document.getElementById('addSubBtn').addEventListener('click', function () {
        var err = document.getElementById('addSubError');
        var out = document.getElementById('addSubResult');
        err.classList.add('d-none'); out.classList.add('d-none');
        var base = parseDate(document.getElementById('baseDate').value);
        var amount = parseInt(document.getElementById('daysAmount').value, 10);
        if (!base || isNaN(amount)) { err.textContent = 'Please select a date and enter a valid number of days.'; err.classList.remove('d-none'); return; }
        var op = document.getElementById('operation').value;
        var result = new Date(base.getTime());
        result.setDate(result.getDate() + (op === 'add' ? amount : -amount));
        out.textContent = 'Result date: ' + result.toDateString();
        out.classList.remove('d-none');
    });
})();
</script>
@endsection
