@extends('layouts.app')

@section('title', 'Study Planner Calculator — Free Online Tool')
@section('meta_description', 'Create a day-by-day study schedule from your exam date, subjects and daily study hours, with revision cycles')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Study Planner Calculator</h1>
            <p class="lead small text-muted">Enter your exam date, subjects (separated by commas) and daily study hours — a day-by-day schedule will be built: first the learning phase, then Revision Cycle 1, and at the end Revision Cycle 2 with mock tests.</p>
<div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="stExam">Exam date</label><input type="date" class="form-control" id="stExam"></div><div class="col-md-4 mb-3"><label class="form-label" for="stStart">Start date</label><input type="date" class="form-control" id="stStart"></div><div class="col-md-4 mb-3"><label class="form-label" for="stHours">Daily study hours</label><input type="number" step="any" class="form-control" id="stHours" placeholder="e.g. 5"></div></div><div class="mb-3"><label class="form-label" for="stSubjects">Subjects (separated by commas, e.g. Maths, Physics, Chemistry, English)</label><input type="text" class="form-control" id="stSubjects" placeholder=""></div><div class="alert alert-secondary mt-3 mb-0" id="stRes">Enter values — the result will appear here live.</div><div class="table-responsive mt-3"><table class="table table-sm table-bordered"><thead><tr><th>Date</th><th>Day</th><th>Phase</th><th>Subject / Task</th><th>Hours</th></tr></thead><tbody id="stTable"><tr><td colspan="5" class="text-muted">Enter details — the schedule will appear here.</td></tr></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>Enter the start date (usually today) and the exam date.</li>
                <li>Write the subjects separated by commas and give the daily hours.</li>
                <li>The schedule table will show each day's subject, phase and hours; the summary will give total hours and each subject's share.</li>
            </ol>
            <p class="small text-muted mb-0">Note: The plan is built in three phases: the first 60% of days are Learning, the next 25% are Revision Cycle 1, the last 15% are Revision Cycle 2 and mock tests. This is a realistic basic plan — adjust it to your own pace.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";

function fmt(n, d) { if (n === null || n === undefined || !isFinite(n)) { return "\u2014"; } var dec = (d === undefined ? 4 : d); return Number(n.toFixed(dec)).toLocaleString("en-US", { maximumFractionDigits: dec }); }
function num(id) { var e = document.getElementById(id); if (!e) { return null; } var v = parseFloat(e.value); return isNaN(v) ? null : v; }
function txt(id) { var e = document.getElementById(id); return e ? e.value : ""; }
function setT(id, t) { var e = document.getElementById(id); if (e) { e.textContent = t; } }
function setH(id, t) { var e = document.getElementById(id); if (e) { e.innerHTML = t; } }
function bind(ids, fn) { ids.forEach(function (id) { var e = document.getElementById(id); if (e) { e.addEventListener("input", fn); e.addEventListener("change", fn); } }); }
function parseList(s) { if (!s) { return []; } var parts = s.split(/[\s,;]+/); var out = []; for (var i = 0; i < parts.length; i++) { if (parts[i] === "") { continue; } var v = parseFloat(parts[i]); if (!isNaN(v) && isFinite(v)) { out.push(v); } } return out; }
function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }


function stCalc() {
    var examS = txt("stExam"), startS = txt("stStart"), hrs = num("stHours");
    var subsRaw = txt("stSubjects");
    var tb = document.getElementById("stTable");
    if (!examS || !startS || hrs === null || hrs <= 0 || !subsRaw.trim()) { setT("stRes", "Please enter the exam date, start date, daily hours and subjects."); return; }
    var start = new Date(startS + "T00:00:00"); var exam = new Date(examS + "T00:00:00");
    var days = Math.round((exam - start) / 86400000);
    if (days <= 0) { setT("stRes", "The exam date must be after the start date."); return; }
    if (days > 365) { setT("stRes", "The plan can be at most 365 days long."); return; }
    var subs = subsRaw.split(",").map(function (s) { return s.trim(); }).filter(function (s) { return s; });
    if (!subs.length) { setT("stRes", "Please write at least one subject."); return; }
    var learnEnd = Math.floor(days * 0.6); var rev1End = learnEnd + Math.floor(days * 0.25);
    var rows = "";
    var dayNames = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    var perSubj = {};
    subs.forEach(function (s) { perSubj[s] = 0; });
    for (var d = 0; d < days; d++) {
        var dt = new Date(start.getTime() + d * 86400000);
        var phase, task;
        if (d < learnEnd) { phase = "Learning"; task = subs[d % subs.length]; perSubj[task] += hrs; }
        else if (d < rev1End) { phase = "Revision Cycle 1"; task = subs[d % subs.length] + " (revision)"; perSubj[subs[d % subs.length]] += hrs; }
        else { phase = "Revision Cycle 2 + Mock"; task = (d % 2 === 0) ? "Mock test + weak topics" : subs[d % subs.length] + " (final revision)"; if (d % 2 !== 0) { perSubj[subs[d % subs.length]] += hrs; } }
        var ds = dt.getFullYear() + "-" + String(dt.getMonth() + 1).padStart(2, "0") + "-" + String(dt.getDate()).padStart(2, "0");
        rows += "<tr><td>" + ds + "</td><td>" + dayNames[dt.getDay()] + "</td><td>" + esc(phase) + "</td><td>" + esc(task) + "</td><td>" + fmt(hrs, 1) + "</td></tr>";
    }
    tb.innerHTML = rows;
    var parts = subs.map(function (s) { return s + ": " + fmt(perSubj[s], 1) + " hrs"; });
    setT("stRes", "Total days: " + days + " | Total study hours: " + fmt(days * hrs, 1) + " | Learning days: " + learnEnd + " | Revision 1 days: " + (rev1End - learnEnd) + " | Revision 2 + mock days: " + (days - rev1End) + " | Subject-wise (mock hours not included): " + parts.join(", "));
}
bind(["stExam","stStart","stHours","stSubjects"], stCalc); stCalc();

})();
</script>
@endsection
