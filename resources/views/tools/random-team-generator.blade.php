@extends('layouts.app')

@section('title', 'Random Team Generator - Azlaan Tools')
@section('meta_description', 'Split names into fair random teams in seconds. Free online random team generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Random Team Generator</h1>
            <p class="lead text-muted">Split a list of names into fair random teams with one click — for cricket, games or class activities.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="namesInput" class="form-label fw-semibold">Names (one name per line)</label>
                        <textarea class="form-control" id="namesInput" rows="6" placeholder="Ali&#10;Ahmed&#10;Sara&#10;Fatima&#10;Usman&#10;Ayesha"></textarea>
                        <div class="form-text"><span id="nameCount">0</span> names entered.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">How to split</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="modeTeams" value="teams" checked>
                                <label class="form-check-label" for="modeTeams">Number of teams</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="modeSize" value="size">
                                <label class="form-check-label" for="modeSize">Players per team</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="numInput" class="form-label fw-semibold" id="numLabel">How many teams?</label>
                            <input type="number" class="form-control" id="numInput" value="2" min="2" max="50">
                        </div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-6"><button type="button" class="btn btn-primary w-100" id="goBtn">Make Teams</button></div>
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary w-100" id="shuffleBtn">Shuffle Again</button></div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Teams</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copyBtn">Copy Teams</button>
                        </div>
                        <div id="teamList" class="row g-3"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write one name per line (players or students).</li>
                <li>Choose: how many teams to make, or how many players each team should have.</li>
                <li>Press "Make Teams" — you will get fair random teams. Press "Shuffle Again" for a new split.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var shuffleBtn = document.getElementById('shuffleBtn');
    var copyBtn = document.getElementById('copyBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var namesInput = document.getElementById('namesInput');
    var nameCount = document.getElementById('nameCount');
    var numInput = document.getElementById('numInput');
    var numLabel = document.getElementById('numLabel');
    var lastTeams = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function getNames() {
        return namesInput.value.split('\n').map(function (n) { return n.trim(); }).filter(function (n) { return n.length > 0; });
    }

    function updateCount() {
        nameCount.textContent = getNames().length;
    }
    namesInput.addEventListener('input', updateCount);

    var modes = document.querySelectorAll('input[name="mode"]');
    for (var m = 0; m < modes.length; m++) {
        modes[m].addEventListener('change', function () {
            if (document.getElementById('modeTeams').checked) {
                numLabel.textContent = 'How many teams?';
                numInput.value = 2;
            } else {
                numLabel.textContent = 'Players per team?';
                numInput.value = 3;
            }
        });
    }

    function shuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = arr[i]; arr[i] = arr[j]; arr[j] = tmp;
        }
        return arr;
    }

    function makeTeams() {
        hideError();
        var names = shuffle(getNames());
        if (names.length < 2) { showError('Please enter at least 2 names.'); return; }
        var mode = document.getElementById('modeTeams').checked ? 'teams' : 'size';
        var n = parseInt(numInput.value, 10);
        if (isNaN(n) || n < 2) { showError('The number must be at least 2.'); return; }

        var teamCount;
        if (mode === 'teams') {
            teamCount = Math.min(n, names.length);
        } else {
            teamCount = Math.ceil(names.length / n);
        }
        if (teamCount < 2) { showError('Cannot make 2 teams from these names.'); return; }

        var teams = [];
        for (var t = 0; t < teamCount; t++) { teams.push([]); }
        names.forEach(function (name, idx) {
            teams[idx % teamCount].push(name);
        });
        lastTeams = teams;
        renderTeams(teams);
        results.classList.remove('d-none');
    }

    var teamNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    function renderTeams(teams) {
        var html = '';
        teams.forEach(function (team, i) {
            var label = i < teamNames.length ? 'Team ' + teamNames[i] : 'Team ' + (i + 1);
            html += '<div class="col-12 col-md-6"><div class="card"><div class="card-header fw-semibold">' + esc(label) +
                ' <span class="badge bg-secondary">' + team.length + '</span></div>';
            html += '<ul class="list-group list-group-flush">';
            team.forEach(function (name) { html += '<li class="list-group-item">' + esc(name) + '</li>'; });
            html += '</ul></div></div>';
        });
        document.getElementById('teamList').innerHTML = html;
    }

    goBtn.addEventListener('click', makeTeams);
    shuffleBtn.addEventListener('click', function () {
        if (!getNames().length) { showError('Enter names first, then shuffle.'); return; }
        makeTeams();
    });

    copyBtn.addEventListener('click', function () {
        hideError();
        if (!lastTeams.length) { showError('Make teams first.'); return; }
        var text = lastTeams.map(function (team, i) {
            return 'Team ' + (i + 1) + ': ' + team.join(', ');
        }).join('\n');
        var done = function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy Teams'; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });

    updateCount();
})();
</script>
@endsection
