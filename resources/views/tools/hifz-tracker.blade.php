@extends('layouts.app')

@section('title', 'Quran Memorization Tracker - Azlaan Tools')
@section('meta_description', 'Track your Hifz: record of paras, sabaq, sabqi and manzil. Free online Quran memorization tracker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Quran Memorization Tracker</h1>
            <p class="lead text-muted">Track your Hifz — record of new sabaq, sabqi (recent revision) and manzil (old revision). Your data is saved in your browser.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm text-center"><div class="card-body">
                        <div class="h3 mb-0 text-primary" id="statParas">0</div>
                        <div class="small text-muted">Paras (sabaq completed)</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center"><div class="card-body">
                        <div class="h3 mb-0 text-success" id="statEntries">0</div>
                        <div class="small text-muted">Total entries</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center"><div class="card-body">
                        <div class="h3 mb-0 text-warning" id="statStreak">0</div>
                        <div class="small text-muted">Daily streak</div>
                    </div></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h6 mb-0">Hifz progress (30 paras)</h2>
                        <span class="small text-muted" id="paraPct">0%</span>
                    </div>
                    <div class="progress mb-3" style="height: 14px;">
                        <div class="progress-bar bg-success" id="paraBar" role="progressbar" style="width: 0%"></div>
                    </div>

                    <h2 class="h6">Add a new entry</h2>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="eDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="eDate">
                        </div>
                        <div class="col-md-3">
                            <label for="eType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="eType">
                                <option value="Sabaq">Sabaq (new memorization)</option>
                                <option value="Sabqi">Sabqi (recent revision)</option>
                                <option value="Manzil">Manzil (old revision)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="ePara" class="form-label fw-semibold">Para</label>
                            <select class="form-select" id="ePara"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="eSurah" class="form-label fw-semibold">Surah (optional)</label>
                            <input type="text" class="form-control" id="eSurah" placeholder="e.g. Al-Baqarah">
                        </div>
                        <div class="col-md-6">
                            <label for="eAyat" class="form-label fw-semibold">Ayat (optional)</label>
                            <input type="text" class="form-control" id="eAyat" placeholder="e.g. 1 - 20">
                        </div>
                        <div class="col-md-6">
                            <label for="eNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="eNote" placeholder="e.g. recited to teacher">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Add Entry</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4">
                        <h2 class="h6">Record</h2>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Date</th><th>Type</th><th>Para</th><th>Surah / Ayat</th><th>Note</th><th></th></tr></thead>
                                <tbody id="entryBody"></tbody>
                            </table>
                        </div>
                        <p class="small text-muted mb-0" id="emptyMsg">No entries yet. Add your first sabaq above.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the date, type (Sabaq/Sabqi/Manzil), para and ayat.</li>
                <li>Press "Add Entry" — your record will be saved.</li>
                <li>See paras and streak in the stats above.</li>
            </ol>
            <p class="small text-muted">Note: your record is saved only in this browser (local storage). Clearing browser data will delete the record.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'hifzTrackerEntries_v1';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');

    var ePara = document.getElementById('ePara');
    for (var p = 1; p <= 30; p++) {
        var opt = document.createElement('option');
        opt.value = p;
        opt.textContent = 'Para ' + p;
        ePara.appendChild(opt);
    }
    document.getElementById('eDate').value = new Date().toISOString().slice(0, 10);

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }
    function save(entries) {
        try { localStorage.setItem(KEY, JSON.stringify(entries)); } catch (e) { /* storage full */ }
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function fmtDate(iso) {
        var d = new Date(iso + 'T00:00:00');
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function calcStreak(entries) {
        var days = {};
        entries.forEach(function (e) { days[e.date] = true; });
        var streak = 0;
        var d = new Date();
        var todayStr = d.toISOString().slice(0, 10);
        if (!days[todayStr]) d.setDate(d.getDate() - 1); // allow yesterday to keep streak alive
        while (days[d.toISOString().slice(0, 10)]) {
            streak++;
            d.setDate(d.getDate() - 1);
        }
        return streak;
    }

    function render() {
        var entries = load();
        var tbody = document.getElementById('entryBody');
        tbody.innerHTML = '';
        entries.slice().reverse().forEach(function (e) {
            var tr = document.createElement('tr');
            var badgeClass = e.type === 'Sabaq' ? 'bg-primary' : (e.type === 'Sabqi' ? 'bg-success' : 'bg-warning text-dark');
            tr.innerHTML =
                '<td>' + fmtDate(e.date) + '</td>' +
                '<td><span class="badge ' + badgeClass + '">' + e.type + '</span></td>' +
                '<td>' + e.para + '</td>' +
                '<td>' + (e.surah || '') + (e.surah && e.ayat ? ' — ' : '') + (e.ayat || '') + '</td>' +
                '<td>' + (e.note || '') + '</td>';
            var delTd = document.createElement('td');
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = '×';
            delBtn.setAttribute('aria-label', 'Delete entry');
            (function (id) {
                delBtn.addEventListener('click', function () {
                    var cur = load().filter(function (x) { return x.id !== id; });
                    save(cur);
                    render();
                });
            })(e.id);
            delTd.appendChild(delBtn);
            tr.appendChild(delTd);
            tbody.appendChild(tr);
        });
        document.getElementById('emptyMsg').style.display = entries.length ? 'none' : '';

        // stats
        var paras = {};
        entries.forEach(function (e) { if (e.type === 'Sabaq') paras[e.para] = true; });
        var paraCount = Object.keys(paras).length;
        document.getElementById('statParas').textContent = paraCount;
        document.getElementById('statEntries').textContent = entries.length;
        document.getElementById('statStreak').textContent = calcStreak(entries);
        var pct = Math.round(paraCount / 30 * 100);
        document.getElementById('paraBar').style.width = pct + '%';
        document.getElementById('paraPct').textContent = pct + '%';
    }

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var date = document.getElementById('eDate').value;
        var type = document.getElementById('eType').value;
        var para = parseInt(ePara.value, 10);
        var surah = esc(document.getElementById('eSurah').value.trim());
        var ayat = esc(document.getElementById('eAyat').value.trim());
        var note = esc(document.getElementById('eNote').value.trim());
        if (!date) { showError('Please pick a date.'); return; }
        var entries = load();
        entries.push({ id: Date.now(), date: date, type: type, para: para, surah: surah, ayat: ayat, note: note });
        save(entries);
        document.getElementById('eSurah').value = '';
        document.getElementById('eAyat').value = '';
        document.getElementById('eNote').value = '';
        render();
    });

    render();
})();
</script>
@endsection
