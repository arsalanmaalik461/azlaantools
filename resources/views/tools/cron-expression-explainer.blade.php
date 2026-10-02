@extends('layouts.app')

@section('title', 'Cron Expression Explainer - Azlaan Tools')
@section('meta_description', 'Paste any cron expression and get a plain English explanation plus the next scheduled run times. Free online cron parser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Cron Expression Explainer</h1>
            <p class="lead text-muted">Paste any cron expression — you will get a plain English explanation and the next run times. Both 5-field (standard) and 6-field (with seconds) are supported.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="cronInput" class="form-label fw-semibold">Cron expression</label>
                        <input type="text" class="form-control font-monospace" id="cronInput" placeholder="e.g. */15 9-17 * * 1-5" value="*/15 9-17 * * 1-5">
                        <div class="form-text">Format: minute hour day-of-month month day-of-week &nbsp;|&nbsp; seconds can also come first (Quartz style).</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Explain</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h5">Plain English</h2>
                        <div class="alert alert-success" id="plainEnglish"></div>
                        <h2 class="h5">Field breakdown</h2>
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Field</th><th>Expression</th><th>Meaning</th></tr></thead>
                            <tbody id="fieldRows"></tbody>
                        </table>
                        <h2 class="h5">Next 5 run times</h2>
                        <ul class="list-group" id="nextRuns"></ul>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste your cron expression into the box above (5 fields, or 6 fields starting with seconds).</li>
                <li>Click <strong>Explain</strong>.</li>
                <li>Read the plain-English summary, the per-field breakdown, and the next five times the schedule will run.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var cronInput = document.getElementById('cronInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var plainEnglish = document.getElementById('plainEnglish');
    var fieldRows = document.getElementById('fieldRows');
    var nextRuns = document.getElementById('nextRuns');

    var MONTH_NAMES = { jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, oct: 10, nov: 11, dec: 12 };
    var DOW_NAMES = { sun: 0, mon: 1, tue: 2, wed: 3, thu: 4, fri: 5, sat: 6 };
    var MONTH_FULL = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var DOW_FULL = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function replaceNames(token, map) {
        return token.toLowerCase().replace(/[a-z]+/g, function (w) {
            return (w in map) ? String(map[w]) : w;
        });
    }
    function parseField(token, min, max, nameMap) {
        token = replaceNames(token.trim(), nameMap || {});
        if (token === '*' || token === '?') return { all: true, values: null };
        var values = {};
        var parts = token.split(',');
        for (var p = 0; p < parts.length; p++) {
            var part = parts[p];
            var step = 1;
            var base = part;
            if (part.indexOf('/') !== -1) {
                var sp = part.split('/');
                base = sp[0];
                step = parseInt(sp[1], 10);
                if (isNaN(step) || step < 1) throw new Error('Invalid step in "' + part + '"');
            }
            var lo, hi;
            if (base === '*' || base === '') { lo = min; hi = max; }
            else if (base.indexOf('-') !== -1) {
                var r = base.split('-');
                lo = parseInt(r[0], 10); hi = parseInt(r[1], 10);
            } else {
                lo = parseInt(base, 10); hi = lo;
            }
            if (isNaN(lo) || isNaN(hi) || lo < min || hi > max || lo > hi) {
                throw new Error('Value out of range in "' + part + '" (allowed ' + min + '-' + max + ')');
            }
            for (var n = lo; n <= hi; n += step) values[n] = true;
        }
        return { all: false, values: values, stepped: token.indexOf('/') !== -1 };
    }
    function sortedKeys(values) {
        return Object.keys(values).map(Number).sort(function (a, b) { return a - b; });
    }
    function isContiguous(keys) {
        for (var i = 1; i < keys.length; i++) if (keys[i] !== keys[i - 1] + 1) return false;
        return true;
    }
    function listOr(vals, fmt) {
        if (vals.length === 1) return fmt(vals[0]);
        if (vals.length === 2) return fmt(vals[0]) + ' and ' + fmt(vals[1]);
        return vals.slice(0, -1).map(fmt).join(', ') + ' and ' + fmt(vals[vals.length - 1]);
    }
    function describe(f, unitS, unitP, fmt) {
        fmt = fmt || function (x) { return String(x); };
        if (f.all) return 'every ' + unitP;
        var keys = sortedKeys(f.values);
        if (keys.length === 1) return 'at ' + fmt(keys[0]) + ' ' + unitS;
        if (f.stepped && keys[0] === 0 || f.stepped) {
            var step = keys[1] - keys[0];
            var even = keys.every(function (k, i) { return k === keys[0] + i * step; });
            if (even) return 'every ' + step + ' ' + unitP;
        }
        if (isContiguous(keys)) return 'every ' + unitS + ' from ' + fmt(keys[0]) + ' to ' + fmt(keys[keys.length - 1]);
        return 'at ' + listOr(keys, fmt) + ' ' + unitP;
    }
    function pad(n) { return (n < 10 ? '0' : '') + n; }

    goBtn.addEventListener('click', function () {
        hideError();
        var expr = cronInput.value.trim().replace(/\s+/g, ' ');
        if (!expr) { showError('Please enter a cron expression first.'); return; }
        var tokens = expr.split(' ');
        var fields, hasSec;
        if (tokens.length === 5) { hasSec = false; fields = tokens; }
        else if (tokens.length === 6) { hasSec = true; fields = tokens; }
        else { showError('The expression must have 5 or 6 fields (found: ' + tokens.length + ').'); return; }

        try {
            var specs = hasSec
                ? [{ n: 'Second', t: fields[0], min: 0, max: 59 }, { n: 'Minute', t: fields[1], min: 0, max: 59 }, { n: 'Hour', t: fields[2], min: 0, max: 23 }, { n: 'Day of month', t: fields[3], min: 1, max: 31 }, { n: 'Month', t: fields[4], min: 1, max: 12, map: MONTH_NAMES }, { n: 'Day of week', t: fields[5], min: 0, max: 7, map: DOW_NAMES }]
                : [{ n: 'Minute', t: fields[0], min: 0, max: 59 }, { n: 'Hour', t: fields[1], min: 0, max: 23 }, { n: 'Day of month', t: fields[2], min: 1, max: 31 }, { n: 'Month', t: fields[3], min: 1, max: 12, map: MONTH_NAMES }, { n: 'Day of week', t: fields[4], min: 0, max: 7, map: DOW_NAMES }];
            var parsed = specs.map(function (s) {
                var p = parseField(s.t, s.min, s.max, s.map);
                if (p.values && p.values[7] !== undefined && s.n === 'Day of week') { delete p.values[7]; p.values[0] = true; }
                return { name: s.name, token: s.t, f: p };
            });
            var P = {};
            parsed.forEach(function (x) { P[x.name] = x.f; });

            var sentence = 'Runs ';
            var mKeys = P.Minute.all ? null : sortedKeys(P.Minute.values);
            var hKeys = P.Hour.all ? null : sortedKeys(P.Hour.values);
            if (mKeys && hKeys && mKeys.length === 1 && hKeys.length === 1) {
                sentence += 'at ' + pad(hKeys[0]) + ':' + pad(mKeys[0]);
            } else if (hKeys && hKeys.length === 1 && !mKeys) {
                sentence += 'every minute during ' + pad(hKeys[0]) + ':00 hour';
            } else if (!hKeys && mKeys && mKeys.length === 1) {
                sentence += 'at minute ' + mKeys[0] + ' of every hour';
            } else if (!hKeys && !mKeys) {
                sentence += 'every minute';
            } else {
                sentence += describe(P.Minute, 'minute', 'minutes') + ', ' + describe(P.Hour, 'hour', 'hours');
            }

            var domAll = P['Day of month'].all, dowAll = P['Day of week'].all;
            var domKeys = domAll ? null : sortedKeys(P['Day of month'].values);
            var dowKeys = dowAll ? null : sortedKeys(P['Day of week'].values).map(function (d) { return d % 7; });
            if (domAll && dowAll) sentence += ', every day';
            else if (!domAll && dowAll) sentence += ', on day ' + listOr(domKeys, String) + ' of the month';
            else if (domAll && !dowAll) sentence += ', on ' + listOr(dowKeys, function (d) { return DOW_FULL[d]; });
            else sentence += ', on day ' + listOr(domKeys, String) + ' of the month or on ' + listOr(dowKeys, function (d) { return DOW_FULL[d]; });

            if (!P.Month.all) sentence += ', in ' + listOr(sortedKeys(P.Month.values), function (m) { return MONTH_FULL[m]; });
            sentence += '.';

            plainEnglish.textContent = sentence;

            fieldRows.innerHTML = '';
            var units = { 'Second': ['second', 'seconds'], 'Minute': ['minute', 'minutes'], 'Hour': ['hour', 'hours'], 'Day of month': ['day of month', 'days of month'], 'Month': ['month', 'months'], 'Day of week': ['day of week', 'days of week'] };
            var fmts = {
                'Month': function (m) { return MONTH_FULL[m]; },
                'Day of week': function (d) { return DOW_FULL[d % 7]; }
            };
            parsed.forEach(function (x) {
                var tr = document.createElement('tr');
                [x.name, x.token, describe(x.f, units[x.name][0], units[x.name][1], fmts[x.name])].forEach(function (txt) {
                    var td = document.createElement('td');
                    td.textContent = txt;
                    tr.appendChild(td);
                });
                fieldRows.appendChild(tr);
            });

            var now = new Date();
            var cursor = new Date(now.getTime());
            if (hasSec) { cursor.setMilliseconds(0); cursor.setSeconds(cursor.getSeconds() + 1); }
            else { cursor.setSeconds(0, 0); cursor.setMinutes(cursor.getMinutes() + 1); }
            function match(date) {
                var mi = date.getMinutes(), hr = date.getHours(), dm = date.getDate(), mo = date.getMonth() + 1, dw = date.getDay();
                if (hasSec && !P.Second.all && !P.Second.values[date.getSeconds()]) return false;
                if (!P.Minute.all && !P.Minute.values[mi]) return false;
                if (!P.Hour.all && !P.Hour.values[hr]) return false;
                if (!P.Month.all && !P.Month.values[mo]) return false;
                var domOk = domAll || !!P['Day of month'].values[dm];
                var dowOk = dowAll || !!P['Day of week'].values[dw] || !!P['Day of week'].values[7] && dw === 0;
                var dayOk = (domAll && dowAll) ? true : domAll ? dowOk : dowAll ? domOk : (domOk || dowOk);
                return dayOk;
            }
            nextRuns.innerHTML = '';
            var found = 0;
            var limit = hasSec ? 366 * 24 * 3600 : 366 * 24 * 60;
            for (var i = 0; i < limit && found < 5; i++) {
                if (match(cursor)) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item font-monospace';
                    li.textContent = cursor.toLocaleString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    nextRuns.appendChild(li);
                    found++;
                }
                if (hasSec) cursor.setSeconds(cursor.getSeconds() + 1);
                else cursor.setMinutes(cursor.getMinutes() + 1);
            }
            if (found === 0) {
                var li2 = document.createElement('li');
                li2.className = 'list-group-item';
                li2.textContent = 'No runs found in the next year — the expression may never match (e.g. 31 Feb).';
                nextRuns.appendChild(li2);
            }
            results.classList.remove('d-none');
        } catch (err) {
            showError('Could not understand the expression: ' + err.message);
        }
    });
})();
</script>
@endsection
