@extends('layouts.app')

@section('title', 'Age Calculator - Azlaan Tools')
@section('meta_description', 'Free online age calculator. Enter your date of birth to find your exact age in years, months and days, total days lived, and your next birthday countdown.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Age Calculator</h1>
            <p class="lead text-muted">Find your exact age in years, months and days. Enter your date of birth and, optionally, a date to calculate your age on that day.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" class="form-control" id="dob">
                    </div>
                    <div class="mb-3">
                        <label for="ageAtDate" class="form-label fw-semibold">Age at Date (optional)</label>
                        <input type="date" class="form-control" id="ageAtDate">
                        <div class="form-text">Leave as today if you want your current age.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="calcBtn">Calculate Age</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="alert alert-success text-center">
                            <div class="fs-4 fw-bold" id="ageMain">-</div>
                        </div>
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-4 fw-bold" id="totalDays">0</div>
                                    <div class="text-muted small">Total Days</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-4 fw-bold" id="totalMonths">0</div>
                                    <div class="text-muted small">Total Months</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-4 fw-bold" id="totalWeeks">0</div>
                                    <div class="text-muted small">Total Weeks</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="fs-4 fw-bold" id="bornDay">-</div>
                                    <div class="text-muted small">Day of Week Born</div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0 text-center" id="nextBirthdayBox">-</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your date of birth in the first field.</li>
                <li>Optionally change the &quot;Age at Date&quot; field, or leave it set to today.</li>
                <li>Click <strong>Calculate Age</strong>.</li>
                <li>See your age in years, months and days, along with total days, total months, the weekday you were born on, and a countdown to your next birthday.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var dobInput = document.getElementById('dob');
    var atInput = document.getElementById('ageAtDate');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function todayStr() {
        var d = new Date();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + m + '-' + day;
    }
    atInput.value = todayStr();

    function parseDate(val) {
        if (!val) return null;
        var parts = val.split('-');
        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); }

    document.getElementById('calcBtn').addEventListener('click', function () {
        hideError();
        var dob = parseDate(dobInput.value);
        var at = parseDate(atInput.value) || new Date();
        if (!dob) { showError('Please select your date of birth.'); return; }
        if (dob > at) { showError('Date of birth cannot be after the age-at date.'); return; }

        var years = at.getFullYear() - dob.getFullYear();
        var months = at.getMonth() - dob.getMonth();
        var days = at.getDate() - dob.getDate();
        var borrowCursor = new Date(at.getFullYear(), at.getMonth(), 1);
        while (days < 0) {
            months--;
            borrowCursor.setMonth(borrowCursor.getMonth() - 1);
            days += new Date(borrowCursor.getFullYear(), borrowCursor.getMonth() + 1, 0).getDate();
        }
        if (months < 0) { years--; months += 12; }

        var diffMs = at - dob;
        var totalDays = Math.floor(diffMs / 86400000);
        var totalMonths = years * 12 + months;
        var totalWeeks = Math.floor(totalDays / 7);

        var weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var bornDay = weekdays[dob.getDay()];

        document.getElementById('ageMain').textContent = years + ' years, ' + months + ' months, ' + days + ' days';
        document.getElementById('totalDays').textContent = totalDays.toLocaleString();
        document.getElementById('totalMonths').textContent = totalMonths.toLocaleString();
        document.getElementById('totalWeeks').textContent = totalWeeks.toLocaleString();
        document.getElementById('bornDay').textContent = bornDay;

        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var next = new Date(today.getFullYear(), dob.getMonth(), dob.getDate());
        if (next < today) { next = new Date(today.getFullYear() + 1, dob.getMonth(), dob.getDate()); }
        var daysLeft = Math.round((next - today) / 86400000);
        var box = document.getElementById('nextBirthdayBox');
        if (daysLeft === 0) {
            box.textContent = 'Happy Birthday! Your birthday is today.';
        } else {
            box.textContent = 'Next birthday: ' + next.toDateString() + ' — in ' + daysLeft + ' day' + (daysLeft === 1 ? '' : 's') + ' (turning ' + (next.getFullYear() - dob.getFullYear()) + ')';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
