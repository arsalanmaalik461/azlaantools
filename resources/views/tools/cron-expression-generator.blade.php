@extends('layouts.app')

@section('title', 'Cron Expression Generator — Free Online Tool')
@section('meta_description', 'Build cron schedules visually and see the next run times')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Cron Expression Generator</h1>
            <p class="lead small text-muted">Build a five-field cron expression (minute, hour, day of month, month, day of week), read it in plain words, and see the next five run times computed from your own clock.</p>
            <label class="form-label" for="crPreset">Preset</label>
            <select class="form-select" id="crPreset">
                <option value="* * * * *">Every minute</option>
                <option value="*/5 * * * *">Every 5 minutes</option>
                <option value="0 * * * *">Every hour</option>
                <option value="0 0 * * *">Daily at midnight</option>
                <option value="0 9 * * 1-5" selected>Weekdays at 09:00</option>
                <option value="0 0 * * 0">Weekly on Sunday</option>
                <option value="0 0 1 * *">Monthly on the 1st</option>
                <option value="30 2 * * *">Daily at 02:30</option>
            </select>
            <div class="row g-2 mt-1">
                <div class="col"><label class="form-label" for="crMin">Minute</label><input type="text" class="form-control font-monospace cr-f" id="crMin" value="0"></div>
                <div class="col"><label class="form-label" for="crHour">Hour</label><input type="text" class="form-control font-monospace cr-f" id="crHour" value="9"></div>
                <div class="col"><label class="form-label" for="crDom">Day of month</label><input type="text" class="form-control font-monospace cr-f" id="crDom" value="*"></div>
                <div class="col"><label class="form-label" for="crMon">Month</label><input type="text" class="form-control font-monospace cr-f" id="crMon" value="*"></div>
                <div class="col"><label class="form-label" for="crDow">Day of week</label><input type="text" class="form-control font-monospace cr-f" id="crDow" value="1-5"></div>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="crErr"></div>
            <label class="form-label mt-2" for="crOut">Cron expression</label>
            <input type="text" class="form-control font-monospace" id="crOut" readonly>
            <p class="mt-2 mb-1"><strong>Meaning:</strong> <span id="crDesc">—</span></p>
            <p class="mb-1 fw-semibold">Next 5 run times (your local time)</p>
            <ul class="mb-0" id="crNext"></ul>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Pick a preset or type the five fields yourself using star, lists (1,2), ranges (1-5) and steps (*/5).</li><li>Read the plain-words meaning to confirm the schedule.</li><li>Check the next five run times before pasting the expression into crontab.</li></ol>
            <p class="small text-muted mb-0">Note: Day of week uses 0 or 7 for Sunday through 6 for Saturday. When both day-of-month and day-of-week are restricted, standard cron runs when either matches — this tool follows that rule.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function parseField(str, min, max) {
        var set = {}, parts = String(str).split(","), i, j;
        for (i = 0; i < parts.length; i++) {
            var p = parts[i].trim(); if (!p) throw new Error("Empty field part");
            var step = 1, range = p;
            if (p.indexOf("/") >= 0) { var sp = p.split("/"); range = sp[0]; step = parseInt(sp[1], 10); if (!step || step < 1) throw new Error("Bad step in " + p); }
            var lo, hi;
            if (range === "*") { lo = min; hi = max; }
            else if (range.indexOf("-") >= 0) { var rp = range.split("-"); lo = parseInt(rp[0], 10); hi = parseInt(rp[1], 10); }
            else { lo = parseInt(range, 10); hi = (p.indexOf("/") >= 0) ? max : lo; }
            if (isNaN(lo) || isNaN(hi) || lo < min || hi > max || lo > hi) throw new Error("Value out of range in " + p);
            for (j = lo; j <= hi; j += step) { set[j] = true; }
        }
        return set;
    }
    function describe() {
        var m = el("crMin").value.trim(), h = el("crHour").value.trim(), dom = el("crDom").value.trim(), mon = el("crMon").value.trim(), dow = el("crDow").value.trim();
        if (/^\d+$/.test(m) && /^\d+$/.test(h)) {
            var t = "At " + String(h).padStart(2, "0") + ":" + String(m).padStart(2, "0");
            if (dom === "*" && mon === "*" && dow === "*") return t + " every day.";
            if (dow === "1-5" && dom === "*") return t + " on weekdays (Mon to Fri).";
            if (dow === "0" || dow === "7") return t + " every Sunday.";
            return t + ", day of month " + dom + ", month " + mon + ", day of week " + dow + ".";
        }
        if (m === "*") return "Every minute.";
        if (m.indexOf("*/") === 0) return "Every " + m.slice(2) + " minutes.";
        return "Minute " + m + ", hour " + h + ", day of month " + dom + ", month " + mon + ", day of week " + dow + ".";
    }
    function calc() {
        var fields = [el("crMin").value.trim(), el("crHour").value.trim(), el("crDom").value.trim(), el("crMon").value.trim(), el("crDow").value.trim()];
        el("crOut").value = fields.join(" ");
        var err = el("crErr"), list = el("crNext"); list.innerHTML = "";
        try {
            var mins = parseField(fields[0], 0, 59), hrs = parseField(fields[1], 0, 23), doms = parseField(fields[2], 1, 31), mons = parseField(fields[3], 1, 12), dows = parseField(fields[4], 0, 7);
            if (dows[7]) dows[0] = true;
            var domStar = fields[2] === "*", dowStar = fields[4] === "*";
            el("crDesc").textContent = describe();
            err.classList.add("d-none");
            var t = new Date(); t.setSeconds(0, 0); t.setMinutes(t.getMinutes() + 1);
            var found = 0, guard = 0;
            while (found < 5 && guard < 600000) {
                guard++;
                var dayOk;
                var inDom = !!doms[t.getDate()], inDow = !!dows[t.getDay()];
                if (domStar && dowStar) dayOk = true; else if (domStar) dayOk = inDow; else if (dowStar) dayOk = inDom; else dayOk = inDom || inDow;
                if (mins[t.getMinutes()] && hrs[t.getHours()] && mons[t.getMonth() + 1] && dayOk) { var li = document.createElement("li"); li.textContent = t.toLocaleString(); list.appendChild(li); found++; }
                t.setMinutes(t.getMinutes() + 1);
            }
            if (!found) { var li2 = document.createElement("li"); li2.textContent = "No run time found in the next year."; list.appendChild(li2); }
        } catch (e) { err.textContent = "Invalid cron field: " + e.message; err.classList.remove("d-none"); el("crDesc").textContent = "—"; }
    }
    document.querySelectorAll(".cr-f").forEach(function (f) { f.addEventListener("input", calc); });
    el("crPreset").addEventListener("change", function () { var p = el("crPreset").value.split(" "); el("crMin").value = p[0]; el("crHour").value = p[1]; el("crDom").value = p[2]; el("crMon").value = p[3]; el("crDow").value = p[4]; calc(); });
    calc();
})();
</script>
@endsection
