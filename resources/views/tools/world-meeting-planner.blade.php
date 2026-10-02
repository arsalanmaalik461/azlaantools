@extends('layouts.app')

@section('title', 'World Meeting Planner — Free Online Tool')
@section('meta_description', 'Compare working hours across several cities to find a fair meeting time.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">World Meeting Planner</h1>
                    <p class="lead small text-muted">Pick a date and your home city, then scan all 24 hours to see what time it is in six major cities at once — with working hours marked and the fairest slots highlighted.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="wmDate">Meeting date</label><input type="date" class="form-control" id="wmDate"></div>
                        <div class="col-md-4"><label class="form-label" for="wmHome">Your city (rows start from its midnight)</label><select class="form-select" id="wmHome"></select></div>
                        <div class="col-md-4"><label class="form-label" for="wmStart">Work day start (hour, 0 to 23)</label><input type="number" class="form-control" id="wmStart" value="9" min="0" max="23" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="wmEnd">Work day end (hour, 1 to 24)</label><input type="number" class="form-control" id="wmEnd" value="18" min="1" max="24" step="1"></div>
                    </div>
                    <div class="table-responsive mt-3"><table class="table table-sm align-middle mb-0"><thead id="wmHead"></thead><tbody id="wmBody"></tbody></table></div>
                    <div class="alert alert-info mt-3 mb-0" id="wmMsg">Pick a date to build the comparison table.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Pick the meeting date and your home city.</li>
                        <li>Adjust the working hours window if your team works different hours.</li>
                        <li>Choose a highlighted row: the more cities inside working hours, the fairer the slot.</li>
                    </ol>
                    <p class="small text-muted mb-0">A city counts as inside working hours when its local time falls between the start and end hours you set. Times are computed with the built in Intl time zone database including daylight saving for the chosen date.</p>
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
    var zones = [
        { id: "Asia/Karachi", label: "Karachi" }, { id: "Asia/Dubai", label: "Dubai" }, { id: "Asia/Dhaka", label: "Dhaka" }, { id: "Europe/London", label: "London" }, { id: "Europe/Berlin", label: "Berlin" }, { id: "America/New_York", label: "New York" }, { id: "America/Los_Angeles", label: "Los Angeles" }, { id: "Asia/Singapore", label: "Singapore" }
    ];
    var sel = el("wmHome");
    zones.forEach(function (z) { var o = document.createElement("option"); o.value = z.id; o.textContent = z.label; sel.appendChild(o); });
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    el("wmDate").value = todayStr();
    function partsInZone(date, zone) { var fmt = new Intl.DateTimeFormat("en-US", { timeZone: zone, year: "numeric", month: "2-digit", day: "2-digit", hour: "2-digit", minute: "2-digit", second: "2-digit", hourCycle: "h23" }); var parts = fmt.formatToParts(date); var out = {}; parts.forEach(function (p) { out[p.type] = p.value; }); return out; }
    function zonedTimeToUtc(y, mo, d, h, zone) { var guess = Date.UTC(y, mo, d, h, 0, 0); var p = partsInZone(new Date(guess), zone); var asUtc = Date.UTC(parseInt(p.year, 10), parseInt(p.month, 10) - 1, parseInt(p.day, 10), parseInt(p.hour, 10), parseInt(p.minute, 10), parseInt(p.second, 10)); return new Date(guess - (asUtc - guess)); }
    function hourIn(date, zone) { return parseInt(new Intl.DateTimeFormat("en-US", { timeZone: zone, hour: "2-digit", hourCycle: "h23" }).format(date), 10); }
    function labelIn(date, zone) { return new Intl.DateTimeFormat("en-GB", { timeZone: zone, hour: "2-digit", minute: "2-digit", hour12: true, weekday: "short" }).format(date); }
    function calc() {
        var dv = el("wmDate").value;
        var msg = el("wmMsg");
        if (!dv) { msg.textContent = "Please pick a meeting date."; return; }
        var p = dv.split("-");
        var y = parseInt(p[0], 10), mo = parseInt(p[1], 10) - 1, d = parseInt(p[2], 10);
        var home = el("wmHome").value;
        var ws = parseInt(el("wmStart").value, 10), we = parseInt(el("wmEnd").value, 10);
        if (isNaN(ws) || ws < 0 || ws > 23) { ws = 9; }
        if (isNaN(we) || we < 1 || we > 24) { we = 18; }
        var head = "<tr><th>" + zones[0].label + " hour</th>";
        zones.forEach(function (z) { head += "<th>" + z.label + "</th>"; });
        head += "<th>Cities in work hours</th></tr>";
        el("wmHead").innerHTML = head;
        var html = "", best = -1, bestLabel = "";
        for (var h = 0; h < 24; h++) {
            var utc = zonedTimeToUtc(y, mo, d, h, home);
            var score = 0;
            var cells = "";
            zones.forEach(function (z) { var zh = hourIn(utc, z.id); var inside = zh >= ws && zh < we; if (inside) { score++; } cells += "<td" + (inside ? " class=\"fw-semibold\"" : " class=\"text-muted\"") + ">" + labelIn(utc, z.id) + "</td>"; });
            if (score > best) { best = score; bestLabel = new Intl.DateTimeFormat("en-GB", { timeZone: home, hour: "2-digit", minute: "2-digit", hour12: true, weekday: "long" }).format(utc); }
            html += "<tr" + (score >= zones.length - 1 ? " class=\"table-success\"" : "") + "><td><strong>" + (h < 10 ? "0" : "") + h + ":00</strong></td>" + cells + "<td>" + score + " / " + zones.length + "</td></tr>";
        }
        el("wmBody").innerHTML = html;
        msg.innerHTML = "<strong>Fairest slot:</strong> " + bestLabel + " in " + sel.options[sel.selectedIndex].text + " puts " + best + " of " + zones.length + " cities inside working hours. Green rows have all but at most one city inside working hours.";
    }
    ["wmDate", "wmHome", "wmStart", "wmEnd"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
