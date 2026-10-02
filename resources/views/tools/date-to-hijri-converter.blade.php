@extends('layouts.app')

@section('title', 'Date to Hijri Converter - Gregorian to Islamic Date | Azlaan Tools')
@section('meta_description', 'Free date to Hijri converter. Convert any Gregorian date to Hijri (Islamic) date and Hijri back to Gregorian instantly. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Date to Hijri Converter</h1>
            <p class="lead text-muted">Convert Gregorian dates to Hijri (Islamic) dates and back — free, instant, and no signup required.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Gregorian to Hijri</h2>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="gregDate">Gregorian Date</label><input type="date" class="form-control" id="gregDate"></div>
                        <div class="col-md-6"><label class="form-label" for="adjust">Adjustment (days)</label><select class="form-select" id="adjust"><option value="-2">-2 days</option><option value="-1">-1 day</option><option value="0" selected>No adjustment</option><option value="1">+1 day</option><option value="2">+2 days</option></select></div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="toHijriBtn">Convert to Hijri</button>
                    <div class="border rounded p-3 bg-light text-center mt-3"><div class="text-muted small">Hijri Date</div><div class="fs-4 fw-bold" id="hijriOut">—</div><div class="small text-muted" id="hijriWeekday"></div></div>
                    <p class="small text-muted mt-2 mb-0">Note: Dates use the Umm al-Qura calendar and may differ by ±1 day from local moon sighting.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Hijri to Gregorian</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="hDay">Hijri Day</label><input type="number" class="form-control" id="hDay" min="1" max="30" placeholder="e.g. 1"></div>
                        <div class="col-md-4"><label class="form-label" for="hMonth">Hijri Month</label><select class="form-select" id="hMonth"></select></div>
                        <div class="col-md-4"><label class="form-label" for="hYear">Hijri Year</label><input type="number" class="form-control" id="hYear" min="1" placeholder="e.g. 1447"></div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="toGregBtn">Convert to Gregorian</button>
                    <div class="alert alert-warning mt-3 d-none" id="revError"></div>
                    <div class="border rounded p-3 bg-light text-center mt-3"><div class="text-muted small">Gregorian Date (approximate)</div><div class="fs-4 fw-bold" id="gregOut">—</div><div class="small text-muted" id="gregWeekday"></div></div>
                </div>
            </div>

            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Pick a Gregorian date and click Convert to Hijri.</li><li>Use the adjustment dropdown if your local calendar differs by a day or two.</li><li>For the reverse, enter Hijri day, month and year.</li><li>Click Convert to Gregorian to see the matching date and weekday.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var months = ['Muharram','Safar','Rabi al-Awwal','Rabi al-Thani','Jumada al-Awwal','Jumada al-Thani','Rajab','Shaban','Ramadan','Shawwal','Dhul-Qadah','Dhul-Hijjah'];
    var weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var sel = document.getElementById('hMonth');
    months.forEach(function (m, i) { var o = document.createElement('option'); o.value = i + 1; o.textContent = (i + 1) + ' - ' + m; sel.appendChild(o); });
    function hijriParts(date) {
        try {
            var fmt = new Intl.DateTimeFormat('en-u-ca-islamic', { day: 'numeric', month: 'numeric', year: 'numeric' });
            var parts = fmt.formatToParts(date); var d = 0, m = 0, y = 0;
            parts.forEach(function (p) { if (p.type === 'day') d = parseInt(p.value, 10); if (p.type === 'month') m = parseInt(p.value, 10); if (p.type === 'year') y = parseInt(p.value, 10); });
            if (d && m && y) return { d: d, m: m, y: y };
        } catch (e) {}
        return kuwaitiFromGreg(date);
    }
    function kuwaitiFromGreg(date) {
        var jd = Math.floor(date.getTime() / 86400000) + 2440588;
        var l = jd - 1948440 + 10632; var n = Math.floor((l - 1) / 10631); l = l - 10631 * n + 354;
        var j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719) + Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
        l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50) - Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;
        var m = Math.floor((24 * l) / 709); var d = l - Math.floor((709 * m) / 24); var y = 30 * n + j - 30;
        return { d: d, m: m, y: y };
    }
    function hijriToGreg(hd, hm, hy) {
        // Kuwaiti algorithm: Hijri to Julian Day Number, then to Gregorian
        var jd = Math.floor((11 * hy + 3) / 30) + 354 * hy + 30 * hm - Math.floor((hm - 1) / 2) + hd + 1948440 - 385;
        var date = new Date((jd - 2440588) * 86400000);
        return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
    }
    document.getElementById('gregDate').valueAsDate = new Date();
    function convertForward() {
        var v = document.getElementById('gregDate').value; if (!v) return;
        var parts = v.split('-'); var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        var adj = parseInt(document.getElementById('adjust').value, 10) || 0;
        date.setDate(date.getDate() + adj);
        var h = hijriParts(date);
        document.getElementById('hijriOut').textContent = h.d + ' ' + months[h.m - 1] + ' ' + h.y + ' AH';
        document.getElementById('hijriWeekday').textContent = 'Weekday: ' + weekdays[date.getDay()] + ' | Gregorian: ' + date.toDateString();
    }
    document.getElementById('toHijriBtn').addEventListener('click', convertForward);
    document.getElementById('adjust').addEventListener('change', convertForward);
    document.getElementById('gregDate').addEventListener('change', convertForward);
    document.getElementById('toGregBtn').addEventListener('click', function () {
        var hd = parseInt(document.getElementById('hDay').value, 10), hm = parseInt(sel.value, 10), hy = parseInt(document.getElementById('hYear').value, 10);
        var err = document.getElementById('revError');
        if (!hd || hd < 1 || hd > 30 || !hy || hy < 1) { err.textContent = 'Please enter a valid Hijri day (1-30) and year.'; err.classList.remove('d-none'); return; }
        err.classList.add('d-none');
        var g = hijriToGreg(hd, hm, hy);
        document.getElementById('gregOut').textContent = g.toDateString();
        document.getElementById('gregWeekday').textContent = 'Weekday: ' + weekdays[g.getDay()];
    });
    convertForward();
})();
</script>
@endsection
