@extends('layouts.app')

@section('title', 'Time Add and Subtract Calculator — Free Online Tool')
@section('meta_description', 'Add or subtract hours, minutes and seconds from a starting time.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Time Add and Subtract Calculator</h1>
                    <p class="lead small text-muted">Shift work, cooking and travel planning: add or subtract any hours, minutes and seconds from a starting clock time and see the result, including day changes.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="taTime">Start time</label><input type="time" class="form-control" id="taTime" value="09:00" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="taOp">Operation</label><select class="form-select" id="taOp"><option value="add">Add</option><option value="sub">Subtract</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="taH">Hours</label><input type="number" class="form-control" id="taH" value="2" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="taM">Minutes</label><input type="number" class="form-control" id="taM" value="30" min="0" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="taS">Seconds</label><input type="number" class="form-control" id="taS" value="0" min="0" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="taOut">Enter a start time and an amount to add or subtract.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Set the starting clock time.</li>
                        <li>Choose Add or Subtract and enter hours, minutes and seconds.</li>
                        <li>Read the resulting time in 24 hour and 12 hour format, with any day shift noted.</li>
                    </ol>
                    <p class="small text-muted mb-0">Carry is handled exactly like a clock: 60 seconds make a minute and 60 minutes make an hour, and results past midnight roll into the next day (shown as plus 1 day).</p>
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
    function pad(n) { return (n < 10 ? "0" : "") + n; }
    function iv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) || v < 0 ? 0 : v; }
    function calc() {
        var t = el("taTime").value;
        var out = el("taOut");
        if (!t) { out.textContent = "Please set a start time."; return; }
        var p = t.split(":");
        var startSec = parseInt(p[0], 10) * 3600 + parseInt(p[1], 10) * 60 + (p.length > 2 ? parseInt(p[2], 10) : 0);
        var delta = iv("taH") * 3600 + iv("taM") * 60 + iv("taS");
        var total = el("taOp").value === "add" ? startSec + delta : startSec - delta;
        var dayShift = Math.floor(total / 86400);
        var rem = ((total % 86400) + 86400) % 86400;
        var h = Math.floor(rem / 3600), m = Math.floor((rem % 3600) / 60), s = rem % 60;
        var h12 = h % 12 === 0 ? 12 : h % 12;
        var ampm = h < 12 ? "AM" : "PM";
        var shiftText = dayShift === 0 ? "same day" : (dayShift > 0 ? "plus " + dayShift + " day(s)" : "minus " + Math.abs(dayShift) + " day(s)");
        out.innerHTML = "<strong>Result:</strong> " + pad(h) + ":" + pad(m) + ":" + pad(s) + " &nbsp; (" + h12 + ":" + pad(m) + ":" + pad(s) + " " + ampm + ", " + shiftText + ")";
    }
    ["taTime", "taOp", "taH", "taM", "taS"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
