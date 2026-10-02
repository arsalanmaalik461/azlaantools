@extends('layouts.app')

@section('title', 'Would You Rather Game - Azlaan Tools')
@section('meta_description', 'Play would you rather with tricky choices — a free online game of fun questions.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Would You Rather Game</h1>
            <p class="lead text-muted">Pick one of two hard choices — <strong>Would You Rather?</strong> Play with friends, a game of fun questions.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <label for="catSel" class="form-label fw-semibold mb-1">Category</label>
                            <select class="form-select" id="catSel" style="width:auto;">
                                <option value="all">All</option>
                                <option value="funny">Funny</option>
                                <option value="tricky">Tricky</option>
                                <option value="kids">Kids</option>
                            </select>
                        </div>
                        <div class="text-muted">Answered: <span class="fw-bold" id="statAnswered">0</span></div>
                    </div>

                    <div class="alert alert-danger d-none" id="errorBox" role="alert"></div>

                    <div id="qCard">
                        <p class="text-center text-muted mb-3" id="qNum">Question 1</p>
                        <h4 class="text-center mb-4">Which would you choose?</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary w-100 p-4 fs-5" id="optA">
                                    <span class="d-block fw-bold text-danger mb-2">A</span>
                                    <span id="qTextA"></span>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-success w-100 p-4 fs-5" id="optB">
                                    <span class="d-block fw-bold text-danger mb-2">B</span>
                                    <span id="qTextB"></span>
                                </button>
                            </div>
                        </div>

                        <div id="voteBox" class="d-none mt-4">
                            <h6>Votes (on this device)</h6>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small"><span class="fw-semibold">Option A</span><span id="pctA"></span></div>
                                <div class="progress" style="height: 14px;"><div class="progress-bar bg-primary" id="barA" style="width:0%"></div></div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small"><span class="fw-semibold">Option B</span><span id="pctB"></span></div>
                                <div class="progress" style="height: 14px;"><div class="progress-bar bg-success" id="barB" style="width:0%"></div></div>
                            </div>
                            <div class="form-text">Votes are saved only on your device.</div>
                        </div>

                        <button type="button" class="btn btn-primary w-100 mt-4 d-none" id="nextBtn">Next Question</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Pick a category (or play all questions).</li>
                <li>Press one of option <strong>A</strong> or <strong>B</strong>.</li>
                <li>See the votes, then press <strong>Next Question</strong>. Ask your friends what they would choose!</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var catSel = document.getElementById('catSel');
    var errorBox = document.getElementById('errorBox');
    var qNum = document.getElementById('qNum');
    var optA = document.getElementById('optA');
    var optB = document.getElementById('optB');
    var qTextA = document.getElementById('qTextA');
    var qTextB = document.getElementById('qTextB');
    var voteBox = document.getElementById('voteBox');
    var barA = document.getElementById('barA');
    var barB = document.getElementById('barB');
    var pctA = document.getElementById('pctA');
    var pctB = document.getElementById('pctB');
    var nextBtn = document.getElementById('nextBtn');
    var statAnswered = document.getElementById('statAnswered');

    var QUESTIONS = [
        { a: 'Always walk backwards', b: 'Always talk while looking back', cat: 'funny' },
        { a: 'Only ever eat soft rice', b: 'Only ever eat lentils with rice', cat: 'funny' },
        { a: 'Become a cat for one day', b: 'Become a parrot for one day', cat: 'funny' },
        { a: 'Always walk while singing', b: 'Always walk while dancing', cat: 'funny' },
        { a: 'Dance in the rain', b: 'Sleep lying in the sun', cat: 'funny' },
        { a: 'Wake up at 4 AM every day', b: 'Go to sleep at 4 AM every night', cat: 'funny' },
        { a: 'Only eat sweet food', b: 'Only eat salty food', cat: 'funny' },
        { a: 'Only whisper for one day', b: 'Only shout for one day', cat: 'funny' },
        { a: 'Fix one past mistake', b: 'See the future and come back', cat: 'tricky' },
        { a: 'Read what other people think', b: 'Travel anywhere instantly', cat: 'tricky' },
        { a: 'Always have to tell the truth', b: 'Always catch a lie instantly', cat: 'tricky' },
        { a: 'Take Rs 1,000,000 now', b: 'Take Rs 10,000,000 in 10 years', cat: 'tricky' },
        { a: 'Know everything in the world', b: 'Speak every language in the world', cat: 'tricky' },
        { a: 'Stop time', b: 'Travel through time', cat: 'tricky' },
        { a: 'Never get sick', b: 'Never feel tired', cat: 'tricky' },
        { a: 'Have one big wish come true', b: 'Have three small wishes come true', cat: 'tricky' },
        { a: 'Know the date of your death', b: 'Know the cause of your death', cat: 'tricky' },
        { a: 'One month without the internet', b: 'One month without a phone', cat: 'tricky' },
        { a: 'Become a superhero', b: 'Become an astronaut', cat: 'kids' },
        { a: 'Befriend a dinosaur', b: 'Befriend a robot', cat: 'kids' },
        { a: 'Chocolate rain', b: 'Ice cream rain', cat: 'kids' },
        { a: 'Learn to fly', b: 'Breathe underwater', cat: 'kids' },
        { a: 'Talk to a cat', b: 'Talk to a dog', cat: 'kids' },
        { a: 'Get one day off from school', b: 'Get one extra day of playtime', cat: 'kids' },
        { a: 'Travel on a magic carpet', b: 'Travel on a magic broom', cat: 'kids' },
        { a: 'Always a sunny day', b: 'Always a rainy day', cat: 'kids' }
    ];

    var deck = [];
    var pos = 0;
    var answered = 0;
    var votes = {};
    try { votes = JSON.parse(localStorage.getItem('wyr_votes') || '{}'); } catch (e) { votes = {}; }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function buildDeck() {
        hideError();
        var f = catSel.value;
        deck = [];
        for (var i = 0; i < QUESTIONS.length; i++) {
            if (f === 'all' || QUESTIONS[i].cat === f) { deck.push(i); }
        }
        if (deck.length === 0) { showError('No questions in this category.'); return; }
        for (var j = deck.length - 1; j > 0; j--) {
            var k = Math.floor(Math.random() * (j + 1));
            var tmp = deck[j];
            deck[j] = deck[k];
            deck[k] = tmp;
        }
        pos = 0;
    }

    function showQ() {
        if (deck.length === 0) { return; }
        if (pos >= deck.length) { buildDeck(); }
        var q = QUESTIONS[deck[pos]];
        qTextA.textContent = q.a;
        qTextB.textContent = q.b;
        qNum.textContent = 'Question ' + (pos + 1) + ' of ' + deck.length;
        optA.disabled = false;
        optB.disabled = false;
        optA.classList.remove('btn-primary');
        optA.classList.add('btn-outline-primary');
        optB.classList.remove('btn-success');
        optB.classList.add('btn-outline-success');
        voteBox.classList.add('d-none');
        nextBtn.classList.add('d-none');
    }

    function choose(side) {
        var qi = deck[pos];
        var key = 'q' + qi;
        if (!votes[key]) { votes[key] = { a: 0, b: 0 }; }
        votes[key][side] = votes[key][side] + 1;
        try { localStorage.setItem('wyr_votes', JSON.stringify(votes)); } catch (e) {}
        answered = answered + 1;
        statAnswered.textContent = answered;

        var a = votes[key].a;
        var b = votes[key].b;
        var tot = a + b;
        var pa = Math.round((a / tot) * 100);
        var pb = 100 - pa;
        barA.style.width = pa + '%';
        barB.style.width = pb + '%';
        pctA.textContent = pa + '% (' + a + ' votes)';
        pctB.textContent = pb + '% (' + b + ' votes)';

        if (side === 'a') {
            optA.classList.remove('btn-outline-primary');
            optA.classList.add('btn-primary');
        } else {
            optB.classList.remove('btn-outline-success');
            optB.classList.add('btn-success');
        }
        optA.disabled = true;
        optB.disabled = true;
        voteBox.classList.remove('d-none');
        nextBtn.classList.remove('d-none');
    }

    optA.addEventListener('click', function () { choose('a'); });
    optB.addEventListener('click', function () { choose('b'); });
    nextBtn.addEventListener('click', function () {
        pos = pos + 1;
        showQ();
    });
    catSel.addEventListener('change', function () {
        buildDeck();
        showQ();
    });

    buildDeck();
    showQ();
})();
</script>
@endsection
