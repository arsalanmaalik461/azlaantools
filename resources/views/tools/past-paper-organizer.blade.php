@extends('layouts.app')

@section('title', 'Past Paper Organizer - Azlaan Tools')
@section('meta_description', 'Track which past papers you solved by subject and year, free online study organizer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Past Paper Organizer</h1>
            <p class="lead text-muted">Track which past papers you have solved — keep a record by subject and year, and see your progress.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="boardSel" class="form-label fw-semibold">Board</label>
                            <select class="form-select" id="boardSel">
                                <option value="lahore">BISE Lahore</option>
                                <option value="gujranwala">BISE Gujranwala</option>
                                <option value="multan">BISE Multan</option>
                                <option value="faisalabad">BISE Faisalabad</option>
                                <option value="rawalpindi">BISE Rawalpindi</option>
                                <option value="sargodha">BISE Sargodha</option>
                                <option value="federal">Federal Board (FBISE)</option>
                                <option value="karachi">BIE Karachi</option>
                                <option value="peshawar">BISE Peshawar</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="levelSel" class="form-label fw-semibold">Class</label>
                            <select class="form-select" id="levelSel">
                                <option value="9">9th Class</option>
                                <option value="10">10th Class</option>
                                <option value="11">11th Class (FSc/ICS)</option>
                                <option value="12">12th Class (FSc/ICS)</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="subjIn" placeholder="Add subject, e.g. Physics">
                        <button type="button" class="btn btn-outline-primary" id="addSubjBtn">Add Subject</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="overallBox" class="mb-2"></div>
                    <div id="subjList"></div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-success flex-fill" id="printBtn">Print Summary</button>
                        <button type="button" class="btn btn-outline-danger flex-fill" id="resetBtn">Reset This Board/Class</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your board and class.</li>
                <li>Add subjects — year chips will appear under each subject.</li>
                <li>Click the chip of each paper you solved. The progress bar updates itself and the record stays saved.</li>
            </ol>
            <p class="text-muted small">This is a record of your preparation — get the past papers themselves from your board website or book.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var boardSel = document.getElementById('boardSel');
    var levelSel = document.getElementById('levelSel');
    var subjIn = document.getElementById('subjIn');
    var addSubjBtn = document.getElementById('addSubjBtn');
    var errorBox = document.getElementById('errorBox');
    var overallBox = document.getElementById('overallBox');
    var subjList = document.getElementById('subjList');
    var printBtn = document.getElementById('printBtn');
    var resetBtn = document.getElementById('resetBtn');

    var YEARS = [];
    for (var y = 2026; y >= 2016; y--) YEARS.push(y);
    var SESSIONS = ['Annual', 'Supp'];

    var PRESET_SUBJECTS = {
        '9': ['English', 'Urdu', 'Math', 'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Islamiat', 'Pak Studies', 'Tarjuma-tul-Quran'],
        '10': ['English', 'Urdu', 'Math', 'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Islamiat', 'Pak Studies'],
        '11': ['English', 'Urdu', 'Physics', 'Chemistry', 'Biology', 'Math', 'Computer Science', 'Islamiat'],
        '12': ['English', 'Urdu', 'Physics', 'Chemistry', 'Biology', 'Math', 'Computer Science', 'Pak Studies']
    };

    function lsKey() { return 'azlaan_pastpapers_' + boardSel.value + '_' + levelSel.value; }

    function loadData() {
        try {
            var raw = localStorage.getItem(lsKey());
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return { subjects: PRESET_SUBJECTS[levelSel.value].slice(), solved: {} };
    }
    function saveData(d) {
        try { localStorage.setItem(lsKey(), JSON.stringify(d)); } catch (e) {}
    }

    var data = loadData();

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function cellKey(subj, yr, sess) { return subj + '|' + yr + '|' + sess; }

    function render() {
        subjList.innerHTML = '';
        var totalCells = 0, totalSolved = 0;

        data.subjects.forEach(function (subj) {
            var card = document.createElement('div');
            card.className = 'border rounded p-3 mb-3';

            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-2';
            var h = document.createElement('h3');
            h.className = 'h6 mb-0';
            h.textContent = subj;
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Remove';
            del.addEventListener('click', function () {
                data.subjects = data.subjects.filter(function (s) { return s !== subj; });
                Object.keys(data.solved).forEach(function (k) {
                    if (k.indexOf(subj + '|') === 0) delete data.solved[k];
                });
                saveData(data); render();
            });
            head.appendChild(h); head.appendChild(del);
            card.appendChild(head);

            var grid = document.createElement('div');
            grid.className = 'd-flex flex-wrap gap-1';
            var subjTotal = 0, subjSolved = 0;

            YEARS.forEach(function (yr) {
                SESSIONS.forEach(function (sess) {
                    subjTotal++; totalCells++;
                    var key = cellKey(subj, yr, sess);
                    var chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'btn btn-sm ' + (data.solved[key] ? 'btn-success' : 'btn-outline-secondary');
                    chip.textContent = yr + ' ' + (sess === 'Annual' ? 'A' : 'S');
                    chip.title = subj + ' ' + yr + ' ' + sess;
                    chip.addEventListener('click', function () {
                        if (data.solved[key]) delete data.solved[key]; else data.solved[key] = 1;
                        saveData(data); render();
                    });
                    grid.appendChild(chip);
                    if (data.solved[key]) { subjSolved++; totalSolved++; }
                });
            });
            card.appendChild(grid);

            var pct = subjTotal ? Math.round(subjSolved / subjTotal * 100) : 0;
            var prog = document.createElement('div');
            prog.className = 'progress mt-2';
            prog.innerHTML = '<div class="progress-bar" role="progressbar" style="width:' + pct + '%">' + pct + '%</div>';
            card.appendChild(prog);
            var lbl = document.createElement('div');
            lbl.className = 'small text-muted mt-1';
            lbl.textContent = subjSolved + ' / ' + subjTotal + ' papers solved';
            card.appendChild(lbl);

            subjList.appendChild(card);
        });

        var opct = totalCells ? Math.round(totalSolved / totalCells * 100) : 0;
        overallBox.innerHTML = '<div class="d-flex justify-content-between small mb-1"><span class="fw-semibold">Overall progress</span><span>' +
            totalSolved + ' / ' + totalCells + ' (' + opct + '%)</span></div>' +
            '<div class="progress"><div class="progress-bar bg-success" style="width:' + opct + '%"></div></div>';
    }

    addSubjBtn.addEventListener('click', function () {
        hideError();
        var s = subjIn.value.trim();
        if (!s) { showError('Please enter a subject name.'); return; }
        if (data.subjects.indexOf(s) !== -1) { showError('This subject is already in the list.'); return; }
        data.subjects.push(s);
        subjIn.value = '';
        saveData(data); render();
    });
    subjIn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addSubjBtn.click(); }
    });

    function reload() { data = loadData(); render(); }
    boardSel.addEventListener('change', reload);
    levelSel.addEventListener('change', reload);

    resetBtn.addEventListener('click', function () {
        if (!confirm('Delete all records for this board and class?')) return;
        try { localStorage.removeItem(lsKey()); } catch (e) {}
        reload();
    });

    printBtn.addEventListener('click', function () {
        var board = boardSel.options[boardSel.selectedIndex].text;
        var level = levelSel.options[levelSel.selectedIndex].text;
        var rows = data.subjects.map(function (subj) {
            var solved = YEARS.map(function (yr) {
                return SESSIONS.map(function (sess) {
                    return data.solved[cellKey(subj, yr, sess)] ? yr + ' ' + sess : null;
                });
            }).reduce(function (a, b) { return a.concat(b); }, []).filter(Boolean);
            return '<tr><td>' + esc(subj) + '</td><td>' + (solved.length ? esc(solved.join(', ')) : '-') + '</td></tr>';
        }).join('');
        var w = window.open('', '_blank');
        w.document.write('<!doctype html><html><head><title>Past Paper Record</title>' +
            '<style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #333;padding:8px}</style>' +
            '</head><body><h2>Past Paper Record — ' + esc(board) + ', ' + esc(level) + '</h2>' +
            '<table><tr><th>Subject</th><th>Solved Papers</th></tr>' + rows + '</table></body></html>');
        w.document.close();
        w.focus();
        w.print();
    });

    render();
})();
</script>
@endsection
