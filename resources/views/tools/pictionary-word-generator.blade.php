@extends('layouts.app')

@section('title', 'Pictionary Word Generator - Azlaan Tools')
@section('meta_description', 'Random drawing words by difficulty for game night. Free pictionary word generator online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Pictionary Word Generator</h1>
            <p class="lead text-muted">Random drawing words for game night — choose a difficulty, set a timer and draw!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="diffSelect" class="form-label fw-semibold">Difficulty</label>
                            <select class="form-select" id="diffSelect">
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                                <option value="mixed">Mixed</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="catSelect" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="catSelect">
                                <option value="all">All categories</option>
                                <option value="objects">Objects</option>
                                <option value="animals">Animals</option>
                                <option value="actions">Actions</option>
                                <option value="food">Food</option>
                                <option value="places">Places</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="timerSelect" class="form-label fw-semibold">Timer</label>
                            <select class="form-select" id="timerSelect">
                                <option value="0">Timer off</option>
                                <option value="30">30 seconds</option>
                                <option value="60" selected>60 seconds</option>
                                <option value="90">90 seconds</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">New Word</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div class="card bg-light">
                            <div class="card-body py-4">
                                <div class="text-muted small mb-1" id="wordMeta"></div>
                                <div class="display-4 fw-bold mb-2" id="wordDisplay">?</div>
                                <div class="display-6 text-primary fw-bold d-none" id="timerDisplay"></div>
                                <div class="progress mt-3 d-none" style="height:8px" id="timerBarWrap">
                                    <div class="progress-bar bg-primary" id="timerBar" style="width:100%"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2">Words left: <span id="leftCount">0</span> | Used: <span id="usedCount">0</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="resetBtn">Start again</button>
                        </p>
                        <div id="historyBox" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose a difficulty and category, set the timer.</li>
                <li>Press "New Word" — one player draws the word while the others guess.</li>
                <li>Each word comes only once until all are finished.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var diffSelect = document.getElementById('diffSelect');
    var catSelect = document.getElementById('catSelect');
    var timerSelect = document.getElementById('timerSelect');
    var goBtn = document.getElementById('goBtn');
    var resetBtn = document.getElementById('resetBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var wordDisplay = document.getElementById('wordDisplay');
    var wordMeta = document.getElementById('wordMeta');
    var timerDisplay = document.getElementById('timerDisplay');
    var timerBar = document.getElementById('timerBar');
    var timerBarWrap = document.getElementById('timerBarWrap');
    var leftCount = document.getElementById('leftCount');
    var usedCount = document.getElementById('usedCount');
    var historyBox = document.getElementById('historyBox');

    var WORDS = [
        ['apple','easy','food'],['banana','easy','food'],['mango','easy','food'],['pizza','easy','food'],['cake','easy','food'],['egg','easy','food'],['roti','easy','food'],['ice cream','easy','food'],['burger','easy','food'],['grapes','easy','food'],
        ['ball','easy','objects'],['book','easy','objects'],['chair','easy','objects'],['table','easy','objects'],['cup','easy','objects'],['hat','easy','objects'],['shoe','easy','objects'],['key','easy','objects'],['door','easy','objects'],['clock','easy','objects'],['phone','easy','objects'],['lamp','easy','objects'],['pen','easy','objects'],['umbrella','easy','objects'],['glasses','easy','objects'],['flag','easy','objects'],['kite','easy','objects'],['balloon','easy','objects'],['drum','easy','objects'],['guitar','easy','objects'],
        ['cat','easy','animals'],['dog','easy','animals'],['fish','easy','animals'],['bird','easy','animals'],['spider','easy','animals'],['bee','easy','animals'],['butterfly','easy','animals'],['frog','easy','animals'],['lion','easy','animals'],['elephant','easy','animals'],['horse','easy','animals'],['rabbit','easy','animals'],['duck','easy','animals'],['hen','easy','animals'],['goat','easy','animals'],['cow','easy','animals'],
        ['sun','easy','places'],['moon','easy','places'],['star','easy','places'],['tree','easy','places'],['house','easy','places'],['flower','easy','places'],
        ['car','easy','objects'],['bicycle','easy','objects'],['boat','easy','objects'],['train','easy','objects'],['plane','easy','objects'],['bus','easy','objects'],
        ['dentist','medium','places'],['camera','medium','objects'],['fireworks','medium','objects'],['waterfall','medium','places'],['volcano','medium','places'],['astronaut','medium','places'],['pirate','medium','actions'],['robot','medium','objects'],['castle','medium','places'],['bridge','medium','places'],['lighthouse','medium','places'],['windmill','medium','places'],['tent','medium','places'],['campfire','medium','places'],['suitcase','medium','objects'],['telescope','medium','objects'],['ladder','medium','objects'],['painting','medium','objects'],['statue','medium','objects'],['fountain','medium','places'],
        ['ghost','medium','actions'],['witch','medium','actions'],['king','medium','actions'],['soldier','medium','actions'],['doctor','medium','actions'],['teacher','medium','actions'],['driver','medium','actions'],['cook','medium','actions'],['farmer','medium','actions'],['swimmer','medium','actions'],['dancer','medium','actions'],['singer','medium','actions'],
        ['rain','medium','places'],['snow','medium','places'],['storm','medium','places'],['rainbow','medium','places'],['sunrise','medium','places'],['sunset','medium','places'],['desert','medium','places'],['island','medium','places'],['beach','medium','places'],['mountain','medium','places'],['forest','medium','places'],
        ['wedding','medium','actions'],['birthday','medium','actions'],['library','medium','places'],['hospital','medium','places'],['mosque','medium','places'],['cricket','medium','actions'],['football','medium','actions'],
        ['dance','medium','actions'],['swim','medium','actions'],['run','medium','actions'],['sleep','medium','actions'],['drive','medium','actions'],['fly','medium','actions'],['climb','medium','actions'],['dig','medium','actions'],
        ['jealousy','hard','actions'],['freedom','hard','actions'],['electricity','hard','objects'],['internet','hard','objects'],['password','hard','objects'],['gravity','hard','places'],['time machine','hard','objects'],['nightmare','hard','actions'],['dream','hard','actions'],['memory','hard','actions'],['silence','hard','actions'],['darkness','hard','places'],['shadow','hard','places'],['reflection','hard','objects'],['eclipse','hard','places'],['galaxy','hard','places'],['black hole','hard','places'],['engine','hard','objects'],['brain surgery','hard','actions'],['heartbreak','hard','actions'],['stage fright','hard','actions'],['peer pressure','hard','actions'],['rumor','hard','actions'],['secret','hard','actions'],['justice','hard','actions'],['peace','hard','actions'],['inflation','hard','actions'],['job interview','hard','actions'],['deadline','hard','actions'],['homesick','hard','actions'],['photosynthesis','hard','places'],['orbit','hard','places'],['mirage','hard','places'],['whisper','hard','actions'],['echo','hard','places'],['compass','hard','objects'],['anchor','hard','objects'],['parachute','hard','objects'],['thermometer','hard','objects'],['microscope','hard','objects'],['satellite','hard','objects'],['tornado','hard','places'],['avalanche','hard','places']
    ];

    var bag = [];
    var used = [];
    var timerId = null;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function shuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = arr[i]; arr[i] = arr[j]; arr[j] = t;
        }
        return arr;
    }
    function buildBag() {
        var d = diffSelect.value, c = catSelect.value;
        bag = WORDS.filter(function (w) {
            var dok = (d === 'mixed') || (w[1] === d);
            var cok = (c === 'all') || (w[2] === c);
            return dok && cok;
        }).map(function (w) { return { word: w[0], diff: w[1], cat: w[2] }; });
        shuffle(bag);
        used = [];
        historyBox.innerHTML = '';
        updateCounts();
    }
    function updateCounts() {
        leftCount.textContent = bag.length;
        usedCount.textContent = used.length;
    }
    function stopTimer() {
        if (timerId) { clearInterval(timerId); timerId = null; }
        timerDisplay.classList.add('d-none');
        timerBarWrap.classList.add('d-none');
    }
    function startTimer() {
        stopTimer();
        var secs = parseInt(timerSelect.value, 10);
        if (!secs) { return; }
        var left = secs;
        timerDisplay.classList.remove('d-none');
        timerBarWrap.classList.remove('d-none');
        function tick() {
            timerDisplay.textContent = left + 's';
            timerBar.style.width = Math.round((left / secs) * 100) + '%';
            timerBar.className = 'progress-bar ' + (left <= 10 ? 'bg-danger' : 'bg-primary');
            if (left <= 0) { stopTimer(); timerDisplay.textContent = 'Time is up!'; timerDisplay.classList.remove('d-none'); beep(); return; }
            left--;
        }
        tick();
        timerId = setInterval(tick, 1000);
    }
    function beep() {
        try {
            var actx = new (window.AudioContext || window.webkitAudioContext)();
            var o = actx.createOscillator();
            var g = actx.createGain();
            o.connect(g); g.connect(actx.destination);
            o.frequency.value = 880; o.type = 'sine';
            g.gain.setValueAtTime(0.2, actx.currentTime);
            o.start(); o.stop(actx.currentTime + 0.4);
        } catch (e) { /* audio not available */ }
    }
    function catLabel(c) {
        return { objects: 'Objects', animals: 'Animals', actions: 'Actions', food: 'Food', places: 'Places' }[c] || c;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!bag.length && !used.length) { buildBag(); }
        if (!bag.length) {
            if (!used.length) { showError('No words for this combination. Change the difficulty or category.'); return; }
            var again = used.map(function (w) { return { word: w.word, diff: w.diff, cat: w.cat }; });
            shuffle(again);
            bag = again;
            used = [];
            historyBox.innerHTML = '';
        }
        var item = bag.pop();
        used.push(item);
        wordDisplay.textContent = item.word;
        wordMeta.textContent = item.diff.toUpperCase() + ' • ' + catLabel(item.cat);
        var badge = document.createElement('span');
        badge.className = 'badge bg-secondary me-1 mb-1';
        badge.textContent = item.word;
        historyBox.appendChild(badge);
        updateCounts();
        results.classList.remove('d-none');
        startTimer();
    });

    resetBtn.addEventListener('click', function () {
        stopTimer();
        buildBag();
        results.classList.add('d-none');
    });
    diffSelect.addEventListener('change', buildBag);
    catSelect.addEventListener('change', buildBag);
    buildBag();
})();
</script>
@endsection
