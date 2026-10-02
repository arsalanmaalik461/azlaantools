@extends('layouts.app')

@section('title', 'Prescription Refill Date Calculator - Azlaan Tools')
@section('meta_description', 'Find out when your medicine will run out and when to refill it. Free online prescription refill date calculator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Prescription Refill Date Calculator</h1>
            <p class="lead text-muted">See when your medicine will run out and when to refill it — simple math from your tablet count and daily dose.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="medName" class="form-label fw-semibold">Medicine name (optional)</label>
                        <input type="text" class="form-control" id="medName" placeholder="e.g. Panadol, Arinac...">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="totalUnits" class="form-label fw-semibold">Total tablets / capsules</label>
                            <input type="number" class="form-control" id="totalUnits" placeholder="e.g. 30" min="1" step="1">
                            <div class="form-text">How many tablets are in the pack or box.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dosePerDay" class="form-label fw-semibold">Tablets per day</label>
                            <input type="number" class="form-control" id="dosePerDay" placeholder="e.g. 2" min="0.25" step="0.25">
                            <div class="form-text">How many you take per day (e.g. morning-evening = 2).</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label fw-semibold">Started on</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bufferDays" class="form-label fw-semibold">Refill buffer (days)</label>
                            <input type="number" class="form-control" id="bufferDays" value="3" min="0" max="30" step="1">
                            <div class="form-text">How many days before it runs out you want to refill.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Refill Date</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert" id="statusBox" role="alert"></div>
                        <table class="table table-bordered">
                            <tbody id="resultRows"></tbody>
                        </table>
                        <label class="form-label fw-semibold">Stock used</label>
                        <div class="progress" style="height:22px;">
                            <div class="progress-bar" id="stockBar" role="progressbar" style="width:0%">0%</div>
                        </div>
                        <p class="small text-muted mt-3 mb-0">This is only an estimate, not medical advice. Always take medicine as your doctor says.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter how many tablets you have and how many you take per day.</li>
                <li>Pick the date you started this pack and how many days early you want the refill reminder.</li>
                <li>Click <strong>Calculate Refill Date</strong> — you will see the finish date, the refill-by date, and days left.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var medName = document.getElementById('medName');
    var totalUnits = document.getElementById('totalUnits');
    var dosePerDay = document.getElementById('dosePerDay');
    var startDate = document.getElementById('startDate');
    var bufferDays = document.getElementById('bufferDays');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statusBox = document.getElementById('statusBox');
    var resultRows = document.getElementById('resultRows');
    var stockBar = document.getElementById('stockBar');

    startDate.value = new Date().toISOString().slice(0, 10);
    var DAY = 86400000;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmtDate(d) {
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    function row(label, value) {
        var tr = document.createElement('tr');
        var td1 = document.createElement('td');
        td1.textContent = label;
        var td2 = document.createElement('td');
        td2.className = 'text-end fw-semibold';
        td2.textContent = value;
        tr.appendChild(td1);
        tr.appendChild(td2);
        return tr;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var total = parseFloat(totalUnits.value);
        var dose = parseFloat(dosePerDay.value);
        var buffer = parseInt(bufferDays.value, 10) || 0;
        if (isNaN(total) || total <= 0) { showError('Enter the correct total tablet count.'); return; }
        if (isNaN(dose) || dose <= 0) { showError('Enter the correct daily dose.'); return; }
        if (!startDate.value) { showError('Select the start date.'); return; }

        var start = new Date(startDate.value + 'T00:00:00');
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        if (start > today) { showError('Start date must be today or earlier.'); return; }

        var daysSupply = total / dose;
        var finish = new Date(start.getTime());
        finish.setDate(finish.getDate() + Math.floor(daysSupply));
        var refill = new Date(finish.getTime());
        refill.setDate(refill.getDate() - buffer);
        var daysLeft = Math.ceil((finish - today) / DAY);
        var elapsed = Math.max(0, Math.min(daysSupply, (today - start) / DAY));
        var usedPct = Math.round(elapsed / daysSupply * 100);

        resultRows.innerHTML = '';
        var name = medName.value.trim();
        if (name) resultRows.appendChild(row('Medicine', name));
        resultRows.appendChild(row('Total days of stock', daysSupply.toFixed(1) + ' days (' + total + ' tablets / ' + dose + ' per day)'));
        resultRows.appendChild(row('Finish date', fmtDate(finish)));
        resultRows.appendChild(row('Refill-by date', fmtDate(refill)));
        resultRows.appendChild(row('Days left', daysLeft > 0 ? daysLeft + ' days' : 'Already finished'));

        stockBar.style.width = usedPct + '%';
        stockBar.textContent = usedPct + '%';
        stockBar.className = 'progress-bar ' + (usedPct >= 100 ? 'bg-danger' : usedPct >= 80 ? 'bg-warning' : 'bg-success');

        statusBox.className = 'alert';
        if (daysLeft < 0) {
            statusBox.classList.add('alert-danger');
            statusBox.textContent = 'Medicine is finished — get a refill now.';
        } else if (daysLeft <= buffer) {
            statusBox.classList.add('alert-warning');
            statusBox.textContent = 'Time to refill — ' + daysLeft + ' days of stock left.';
        } else {
            statusBox.classList.add('alert-success');
            statusBox.textContent = 'Stock is fine — ' + daysLeft + ' days of stock left. Refill by ' + fmtDate(refill) + '.';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
