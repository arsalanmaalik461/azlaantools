@extends('layouts.app')

@section('title', 'Time Zone Converter - Convert Time Between Cities Free | Azlaan Tools')
@section('meta_description', 'Free time zone converter: convert any date and time between Karachi, Dubai, London, New York and more, plus live world clocks. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-3">Time Zone Converter</h1>
            <p class="lead text-muted">Convert a date and time from one city to the rest of the world instantly — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dtInput" class="form-label fw-semibold">Date &amp; Time</label>
                            <input type="datetime-local" class="form-control" id="dtInput">
                        </div>
                        <div class="col-md-6">
                            <label for="fromZone" class="form-label fw-semibold">From Time Zone</label>
                            <select class="form-select" id="fromZone"></select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="convertBtn">Convert Time</button>
                    <button type="button" class="btn btn-outline-secondary mt-3" id="nowBtn">Use Current Time</button>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped align-middle mb-0">
                            <thead><tr><th>City / Zone</th><th>Converted Time</th><th>Day</th></tr></thead>
                            <tbody id="resultBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Live World Clocks (updating every second)</h2>
                    <div class="row g-2" id="liveClocks"></div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Pick a date and time, or click <strong>Use Current Time</strong>.</li>
                        <li>Select the zone that time belongs to (default: Karachi).</li>
                        <li>Click <strong>Convert Time</strong> to see the same moment in 10 major zones.</li>
                        <li>The live clocks below always show the current time in every zone.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var zones = [
        { id: 'Asia/Karachi', label: 'Karachi (Pakistan)' },
        { id: 'Asia/Dubai', label: 'Dubai (UAE)' },
        { id: 'Asia/Riyadh', label: 'Riyadh (Saudi Arabia)' },
        { id: 'Europe/London', label: 'London (UK)' },
        { id: 'America/New_York', label: 'New York (USA)' },
        { id: 'America/Los_Angeles', label: 'Los Angeles (USA)' },
        { id: 'Asia/Dhaka', label: 'Dhaka (Bangladesh)' },
        { id: 'Asia/Kuala_Lumpur', label: 'Kuala Lumpur (Malaysia)' },
        { id: 'Australia/Sydney', label: 'Sydney (Australia)' },
        { id: 'UTC', label: 'UTC' }
    ];
    var sel = document.getElementById('fromZone');
    zones.forEach(function (z) {
        var opt = document.createElement('option');
        opt.value = z.id; opt.textContent = z.label;
        if (z.id === 'Asia/Karachi') { opt.selected = true; }
        sel.appendChild(opt);
    });
    function fmtInZone(date, zone) {
        return new Intl.DateTimeFormat('en-GB', { timeZone: zone, year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }).format(date);
    }
    function dayInZone(date, zone) {
        return new Intl.DateTimeFormat('en-GB', { timeZone: zone, weekday: 'long' }).format(date);
    }
    function partsInZone(date, zone) {
        var fmt = new Intl.DateTimeFormat('en-US', { timeZone: zone, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23' });
        var parts = fmt.formatToParts(date);
        var out = { };
        parts.forEach(function (p) { out[p.type] = p.value; });
        return out;
    }
    function zonedTimeToUtc(dateVal, zone) {
        // Interpret the wall-clock input as local time in the given zone, then find the UTC instant.
        var naive = new Date(dateVal);
        if (isNaN(naive.getTime())) { return null; }
        var guess = Date.UTC(naive.getFullYear(), naive.getMonth(), naive.getDate(), naive.getHours(), naive.getMinutes(), naive.getSeconds());
        var p = partsInZone(new Date(guess), zone);
        var asUtc = Date.UTC(parseInt(p.year, 10), parseInt(p.month, 10) - 1, parseInt(p.day, 10), parseInt(p.hour, 10), parseInt(p.minute, 10), parseInt(p.second, 10));
        var offset = asUtc - guess;
        return new Date(guess - offset);
    }
    function convert() {
        var val = document.getElementById('dtInput').value;
        if (!val) { alert('Please pick a date and time first.'); return; }
        var utcDate = zonedTimeToUtc(val, sel.value);
        if (!utcDate) { alert('Invalid date and time.'); return; }
        var body = document.getElementById('resultBody');
        body.innerHTML = '';
        zones.forEach(function (z) {
            var tr = document.createElement('tr');
            if (z.id === sel.value) { tr.className = 'table-primary'; }
            var td1 = document.createElement('td'); td1.textContent = z.label;
            var td2 = document.createElement('td'); td2.textContent = fmtInZone(utcDate, z.id);
            var td3 = document.createElement('td'); td3.textContent = dayInZone(utcDate, z.id);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3);
            body.appendChild(tr);
        });
    }
    function renderClocks() {
        var wrap = document.getElementById('liveClocks');
        var now = new Date();
        var html = '';
        zones.forEach(function (z) {
            var t = new Intl.DateTimeFormat('en-GB', { timeZone: z.id, hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }).format(now);
            html += '<div class="col-6 col-md-4"><div class="border rounded p-2 text-center"><div class="small text-muted">' + z.label + '</div><div class="fw-bold">' + t + '</div></div></div>';
        });
        wrap.innerHTML = html;
    }
    document.getElementById('convertBtn').addEventListener('click', convert);
    document.getElementById('nowBtn').addEventListener('click', function () {
        var now = new Date();
        function pad(n) { return n < 10 ? '0' + n : '' + n; }
        document.getElementById('dtInput').value = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        try {
            var localZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            for (var i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value === localZone) { sel.selectedIndex = i; break; }
            }
        } catch (e) { /* keep default */ }
        convert();
    });
    renderClocks();
    setInterval(renderClocks, 1000);
})();
</script>
@endsection
