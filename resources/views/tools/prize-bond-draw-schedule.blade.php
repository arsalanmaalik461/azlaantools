@extends('layouts.app')

@section('title', 'Prize Bond Draw Schedule 2026 - Azlaan Tools')
@section('meta_description', 'National Savings prize bond draw schedule 2026 - draw date and city for every denomination. Free lookup, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Prize Bond Draw Schedule 2026</h1>
            <p class="lead text-muted">Dates and cities of National Savings 2026 prize bond draws - view by your denomination and find the next draw.</p>

            <div class="card shadow-sm mb-4 border-success" id="nextCard">
                <div class="card-body">
                    <h5 class="card-title mb-2">Next Draw</h5>
                    <p class="card-text fs-5 mb-1" id="nextDrawText">...</p>
                    <p class="card-text text-muted small mb-0" id="nextDrawSub"></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="denomFilter" class="form-label fw-semibold">Choose denomination</label>
                            <select class="form-select" id="denomFilter">
                                <option value="all">All draws</option>
                                <option value="100">Rs. 100</option>
                                <option value="200">Rs. 200</option>
                                <option value="750">Rs. 750</option>
                                <option value="1500">Rs. 1,500</option>
                                <option value="25000">Rs. 25,000 Premium (Registered)</option>
                                <option value="40000">Rs. 40,000 Premium (Registered)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary w-100" id="goBtn">View Schedule</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle" id="drawTable">
                                <thead class="table-light">
                                    <tr><th>Date</th><th>Denomination</th><th>City</th><th>Status</th></tr>
                                </thead>
                                <tbody id="drawBody"></tbody>
                            </table>
                        </div>
                        <p class="small text-muted">Old Rs. 7,500 and Rs. 15,000 bearer bonds are not in this schedule - these are the current denominations.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your bond denomination or view all.</li>
                <li>Press <strong>View Schedule</strong> - you will see the date, city and status (done / remaining).</li>
                <li>The next draw always shows in the green card above.</li>
            </ol>
            <p class="small text-muted">The schedule follows the National Savings 2026 notification. If a draw day is a holiday, the draw happens on the next working day - rates/dates can change, confirm on the official website (savings.gov.pk). This tool shows the schedule and does <strong>not verify draw results</strong> - always match results with the official list.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    // Official 2026 schedule per Central Directorate of National Savings notification (Oct 2025).
    var DRAWS = [
        { d: '2026-01-15', denom: '750',   label: 'Rs. 750',                     city: 'Peshawar' },
        { d: '2026-02-16', denom: '1500',  label: 'Rs. 1,500',                   city: 'Lahore' },
        { d: '2026-02-16', denom: '100',   label: 'Rs. 100',                     city: 'Karachi' },
        { d: '2026-03-10', denom: '40000', label: 'Rs. 40,000 Premium (Registered)', city: 'Rawalpindi' },
        { d: '2026-03-10', denom: '25000', label: 'Rs. 25,000 Premium (Registered)', city: 'Multan' },
        { d: '2026-03-16', denom: '200',   label: 'Rs. 200',                     city: 'Faisalabad' },
        { d: '2026-04-15', denom: '750',   label: 'Rs. 750',                     city: 'Quetta' },
        { d: '2026-05-15', denom: '1500',  label: 'Rs. 1,500',                   city: 'Sialkot' },
        { d: '2026-05-15', denom: '100',   label: 'Rs. 100',                     city: 'Hyderabad' },
        { d: '2026-06-10', denom: '40000', label: 'Rs. 40,000 Premium (Registered)', city: 'Muzaffarabad' },
        { d: '2026-06-10', denom: '25000', label: 'Rs. 25,000 Premium (Registered)', city: 'Peshawar' },
        { d: '2026-06-15', denom: '200',   label: 'Rs. 200',                     city: 'Karachi' },
        { d: '2026-07-15', denom: '750',   label: 'Rs. 750',                     city: 'Lahore' },
        { d: '2026-08-17', denom: '1500',  label: 'Rs. 1,500',                   city: 'Faisalabad' },
        { d: '2026-08-17', denom: '100',   label: 'Rs. 100',                     city: 'Multan' },
        { d: '2026-09-10', denom: '40000', label: 'Rs. 40,000 Premium (Registered)', city: 'Sialkot' },
        { d: '2026-09-10', denom: '25000', label: 'Rs. 25,000 Premium (Registered)', city: 'Quetta' },
        { d: '2026-09-15', denom: '200',   label: 'Rs. 200',                     city: 'Muzaffarabad' },
        { d: '2026-10-15', denom: '750',   label: 'Rs. 750',                     city: 'Rawalpindi' },
        { d: '2026-11-16', denom: '1500',  label: 'Rs. 1,500',                   city: 'Hyderabad' },
        { d: '2026-11-16', denom: '100',   label: 'Rs. 100',                     city: 'Faisalabad' },
        { d: '2026-12-10', denom: '40000', label: 'Rs. 40,000 Premium (Registered)', city: 'Lahore' },
        { d: '2026-12-10', denom: '25000', label: 'Rs. 25,000 Premium (Registered)', city: 'Karachi' },
        { d: '2026-12-15', denom: '200',   label: 'Rs. 200',                     city: 'Peshawar' }
    ];

    var goBtn = document.getElementById('goBtn');
    var denomFilter = document.getElementById('denomFilter');
    var errorBox = document.getElementById('errorBox');
    var drawBody = document.getElementById('drawBody');
    var results = document.getElementById('results');
    var nextDrawText = document.getElementById('nextDrawText');
    var nextDrawSub = document.getElementById('nextDrawSub');

    var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    function parseDate(s) {
        var p = s.split('-');
        return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    }
    function fmt(s) {
        var dt = parseDate(s);
        return DAYS[dt.getDay()] + ', ' + dt.getDate() + ' ' + MONTHS[dt.getMonth()] + ' ' + dt.getFullYear();
    }
    function todayLocal() {
        var n = new Date();
        return new Date(n.getFullYear(), n.getMonth(), n.getDate());
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function renderNext() {
        var today = todayLocal();
        var upcoming = DRAWS.filter(function (r) { return parseDate(r.d) >= today; });
        if (upcoming.length === 0) {
            nextDrawText.textContent = 'All 2026 draws are over.';
            nextDrawSub.textContent = 'The schedule will update when National Savings issues the 2027 schedule.';
            return;
        }
        var nx = upcoming[0];
        nextDrawText.textContent = fmt(nx.d) + ' - ' + nx.label + ' (' + nx.city + ')';
        nextDrawSub.textContent = 'Total ' + upcoming.length + ' draws remain in 2026.';
    }

    function render() {
        hideError();
        var f = denomFilter.value;
        var today = todayLocal();
        var rows = DRAWS.filter(function (r) { return f === 'all' || r.denom === f; });
        drawBody.innerHTML = '';
        rows.forEach(function (r) {
            var dt = parseDate(r.d);
            var past = dt < today;
            var tr = document.createElement('tr');
            var status = past
                ? '<span class="badge bg-secondary">Done</span>'
                : '<span class="badge bg-success">Remaining</span>';
            tr.innerHTML = '<td>' + fmt(r.d) + '</td><td>' + r.label + '</td><td>' + r.city + '</td><td>' + status + '</td>';
            drawBody.appendChild(tr);
        });
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', render);
    denomFilter.addEventListener('change', render);
    renderNext();
    render();
})();
</script>
@endsection
