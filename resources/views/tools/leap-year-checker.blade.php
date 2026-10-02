@extends('layouts.app')

@section('title', 'Leap Year Checker — Free Online Tool')
@section('meta_description', 'Check if any year is a leap year and list leap years in a range.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Leap Year Checker</h1>
                    <p class="lead small text-muted">Enter any year to check whether it is a leap year under the exact Gregorian rules, and list all leap years between any two years.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="lyYear">Year to check</label><input type="number" class="form-control" id="lyYear" value="2028" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="lyFrom">Range from</label><input type="number" class="form-control" id="lyFrom" value="2020" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="lyTo">Range to</label><input type="number" class="form-control" id="lyTo" value="2040" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="lyOut">Enter a year to check it.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Type a year to check whether it is a leap year and when the nearest leap years are.</li>
                        <li>Set a from and to year to list every leap year in that range.</li>
                    </ol>
                    <p class="small text-muted mb-0">Gregorian rule: a year is a leap year if it is divisible by 4, except century years, which must be divisible by 400. So 2000 was a leap year but 1900 and 2100 are not.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function isLeap(y) { return (y % 4 === 0 && y % 100 !== 0) || y % 400 === 0; }
    function calc() {
        var y = parseInt(el("lyYear").value, 10);
        var from = parseInt(el("lyFrom").value, 10), to = parseInt(el("lyTo").value, 10);
        var out = el("lyOut");
        if (isNaN(y)) { out.textContent = "Please enter a valid year."; return; }
        var html = "<strong>" + y + (isLeap(y) ? " IS a leap year" : " is NOT a leap year") + "</strong> — February " + y + " has " + (isLeap(y) ? "29" : "28") + " days and the year has " + (isLeap(y) ? "366" : "365") + " days.";
        var prev = y - 1; while (!isLeap(prev)) { prev--; }
        var nxt = y + 1; while (!isLeap(nxt)) { nxt++; }
        html += "<br>Previous leap year: " + prev + " &nbsp; Next leap year: " + nxt + ".";
        if (!isNaN(from) && !isNaN(to) && to >= from && to - from <= 500) { var list = []; for (var i = from; i <= to; i++) { if (isLeap(i)) { list.push(i); } } html += "<br><strong>Leap years from " + from + " to " + to + " (" + list.length + "):</strong> " + (list.length ? list.join(", ") : "none"); }
        out.innerHTML = html;
    }
    ["lyYear", "lyFrom", "lyTo"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
