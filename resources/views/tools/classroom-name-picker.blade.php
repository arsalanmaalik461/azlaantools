@extends('layouts.app')
@section('title', 'Random Name Picker - Free Online | Azlaan Tools')
@section('meta_description', 'Pick a random name from your class list online for free - fair classroom draws, team selection and giveaways. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Random Name Picker</h1>
            <p class="lead text-muted">Pick a random student from your class — a fair and fun method. Also perfect for team selection, giveaways and games.</p>

            <div class="alert alert-danger d-none" id="alertBox" role="alert"></div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="namesInput" class="form-label fw-semibold">Names list (one per line)</label>
                        <textarea class="form-control" id="namesInput" rows="6" placeholder="Ahmed Khan&#10;Fatima Ali&#10;Bilal Raza&#10;Sara Mahmood&#10;..."></textarea>
                        <div class="form-text">Write one name on each line. <span id="namesCount" class="fw-semibold">0 names</span></div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="removePickedCheck" checked>
                        <label class="form-check-label" for="removePickedCheck">Remove picked name after each draw (no repeats)</label>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-lg flex-fill" id="pickBtn">🎲 Pick a Random Name</button>
                        <button type="button" class="btn btn-outline-secondary" id="resetBtn">Reset</button>
                    </div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div class="display-6 fw-bold text-primary mb-2" id="winnerName" style="min-height: 3.2rem;"></div>
                        <p class="text-muted mb-2" id="drawInfo"></p>
                        <div class="mt-3 text-start">
                            <h3 class="h6">Picked so far</h3>
                            <ul class="list-group" id="pickedList"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Remaining names</h2>
                    <p class="text-muted small" id="remainingInfo">Names will appear here once you add them.</p>
                    <div id="remainingChips" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste or type your names — one name per line.</li>
                <li>Keep <strong>Remove picked name</strong> on for fair draws with no repeats.</li>
                <li>Press <strong>Pick a Random Name</strong> — the winner is drawn with a drum-roll animation.</li>
                <li>Press <strong>Reset</strong> to start a fresh round.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var namesInput = document.getElementById('namesInput');
    var namesCount = document.getElementById('namesCount');
    var removePickedCheck = document.getElementById('removePickedCheck');
    var pickBtn = document.getElementById('pickBtn');
    var resetBtn = document.getElementById('resetBtn');
    var alertBox = document.getElementById('alertBox');
    var results = document.getElementById('results');
    var winnerName = document.getElementById('winnerName');
    var drawInfo = document.getElementById('drawInfo');
    var pickedList = document.getElementById('pickedList');
    var remainingInfo = document.getElementById('remainingInfo');
    var remainingChips = document.getElementById('remainingChips');

    var pool = [];
    var picked = [];
    var totalAtStart = 0;
    var animTimer = null;
    var drawing = false;

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }
    function hideError() {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }
    function parseNames() {
        var lines = namesInput.value.split('\n');
        var out = [];
        for (var i = 0; i < lines.length; i++) {
            var t = lines[i].trim();
            if (t) { out.push(t); }
        }
        return out;
    }
    function refreshChips() {
        pool = parseNames();
        namesCount.textContent = pool.length + (pool.length === 1 ? ' name' : ' names');
        remainingChips.innerHTML = '';
        if (pool.length === 0) {
            remainingInfo.textContent = 'Names will appear here once you add them.';
            return;
        }
        var available = pool.filter(function (nm) {
            return removePickedCheck.checked ? picked.indexOf(nm) === -1 : true;
        });
        remainingInfo.textContent = available.length + ' of ' + pool.length + ' names available for the draw.';
        available.forEach(function (nm) {
            var span = document.createElement('span');
            span.className = 'badge bg-light text-dark border';
            span.textContent = nm;
            remainingChips.appendChild(span);
        });
    }
    function addPickedItem(nm, drawNo) {
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        var s = document.createElement('span');
        s.textContent = nm;
        var b = document.createElement('span');
        b.className = 'badge bg-primary rounded-pill';
        b.textContent = 'Draw #' + drawNo;
        li.appendChild(s);
        li.appendChild(b);
        pickedList.appendChild(li);
    }
    function stopAnimation() {
        if (animTimer) {
            clearInterval(animTimer);
            animTimer = null;
        }
        drawing = false;
        pickBtn.disabled = false;
        pickBtn.textContent = '🎲 Pick a Random Name';
    }

    namesInput.addEventListener('input', refreshChips);
    removePickedCheck.addEventListener('change', refreshChips);

    pickBtn.addEventListener('click', function () {
        hideError();
        if (drawing) { return; }
        pool = parseNames();
        if (pool.length < 2) {
            showError('Please enter at least 2 names (one per line).');
            return;
        }
        var available = pool.filter(function (nm) {
            return removePickedCheck.checked ? picked.indexOf(nm) === -1 : true;
        });
        if (available.length === 0) {
            showError('All names have been picked! Press Reset to start a new round.');
            return;
        }
        if (totalAtStart === 0) { totalAtStart = pool.length; }
        drawing = true;
        pickBtn.disabled = true;
        pickBtn.textContent = 'Drawing...';
        results.classList.remove('d-none');
        // drum-roll animation
        var ticks = 0;
        animTimer = setInterval(function () {
            ticks++;
            winnerName.textContent = available[Math.floor(Math.random() * available.length)];
            if (ticks >= 18) {
                stopAnimation();
                var winner = available[Math.floor(Math.random() * available.length)];
                winnerName.textContent = winner;
                picked.push(winner);
                var drawNo = picked.length;
                addPickedItem(winner, drawNo);
                var left = available.length - (removePickedCheck.checked ? 1 : 0);
                drawInfo.textContent = 'Draw #' + drawNo + ' of ' + totalAtStart + (removePickedCheck.checked && left > 0 ? ' — ' + left + ' name(s) remaining.' : '');
                if (removePickedCheck.checked) { refreshChips(); }
                winnerName.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 90);
    });

    resetBtn.addEventListener('click', function () {
        stopAnimation();
        hideError();
        picked = [];
        totalAtStart = 0;
        pickedList.innerHTML = '';
        winnerName.textContent = '';
        drawInfo.textContent = '';
        results.classList.add('d-none');
        refreshChips();
    });

    refreshChips();
})();
</script>
@endsection
