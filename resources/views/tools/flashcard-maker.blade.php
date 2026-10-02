@extends('layouts.app')

@section('title', 'Flashcard Maker - Azlaan Tools')
@section('meta_description', 'Make your own flashcards and study them with flip cards, shuffle and self-quiz - free online study tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Flashcard Maker</h1>
            <p class="lead text-muted">Make your own flashcards and memorize them — best for MDCAT, CSS and exams. Flip cards, shuffle, and quiz yourself.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="setupPane">
                        <div class="mb-3">
                            <label for="cardsInput" class="form-label fw-semibold">Write your flashcards (one card per line)</label>
                            <textarea class="form-control" id="cardsInput" rows="7" placeholder="Mitochondria | Powerhouse of the cell&#10;Formula of H2O | Water&#10;..."></textarea>
                            <div class="form-text">Format: <code>Front | Back</code> — for example <code>Question | Answer</code>. You can also use <code>::</code>.</div>
                        </div>
                        <div class="mb-3">
                            <label for="deckName" class="form-label fw-semibold">Deck name <span class="text-muted fw-normal">(optional - to save)</span></label>
                            <input type="text" class="form-control" id="deckName" placeholder="example: Biology Chapter 1" maxlength="60">
                        </div>
                        <button type="button" class="btn btn-primary w-100 mb-2" id="goBtn">Make Cards</button>
                        <button type="button" class="btn btn-outline-secondary w-100 d-none" id="loadSavedBtn">Load Saved Deck</button>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    </div>

                    <div id="studyPane" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" id="deckTitle">Study Session</h5>
                            <span class="badge bg-primary" id="counterBadge">1 / 1</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" id="studyProgress" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="card mb-3 border-primary" id="flipCard" style="min-height: 220px; cursor: pointer;">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                <small class="text-muted mb-2" id="sideLabel">FRONT — click to see the answer</small>
                                <h4 id="cardText" class="mb-0"></h4>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><button type="button" class="btn btn-outline-danger w-100" id="dontBtn">Do Not Know Yet</button></div>
                            <div class="col-6"><button type="button" class="btn btn-outline-success w-100" id="knowBtn">I Know It</button></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 btn-sm" id="shuffleBtn">Shuffle</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 btn-sm" id="exportBtn">Export</button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 btn-sm" id="newDeckBtn">New Deck</button></div>
                        </div>
                        <div id="scoreBox" class="alert alert-info mt-3 d-none"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write cards in <code>Front | Back</code> format, one per line.</li>
                <li>Press <strong>Make Cards</strong> — study mode will open.</li>
                <li>Click a card to see the answer, then press <strong>I Know It</strong> or <strong>Do Not Know Yet</strong>.</li>
                <li>You get a score at the end. You can also save the deck by giving it a name.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var cardsInput = document.getElementById('cardsInput');
    var deckName = document.getElementById('deckName');
    var goBtn = document.getElementById('goBtn');
    var loadSavedBtn = document.getElementById('loadSavedBtn');
    var errorBox = document.getElementById('errorBox');
    var setupPane = document.getElementById('setupPane');
    var studyPane = document.getElementById('studyPane');
    var deckTitle = document.getElementById('deckTitle');
    var counterBadge = document.getElementById('counterBadge');
    var studyProgress = document.getElementById('studyProgress');
    var flipCard = document.getElementById('flipCard');
    var sideLabel = document.getElementById('sideLabel');
    var cardText = document.getElementById('cardText');
    var dontBtn = document.getElementById('dontBtn');
    var knowBtn = document.getElementById('knowBtn');
    var shuffleBtn = document.getElementById('shuffleBtn');
    var exportBtn = document.getElementById('exportBtn');
    var newDeckBtn = document.getElementById('newDeckBtn');
    var scoreBox = document.getElementById('scoreBox');

    var SAVE_KEY = 'azlaan_flashcard_deck';
    var deck = [];
    var order = [];
    var pos = 0;
    var showingFront = true;
    var known = 0;
    var answered = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function parseCards(text) {
        var out = [];
        text.split('\n').forEach(function (line) {
            var m = line.match(/^(.*?)\s*(?:\|\||\||::)\s*(.+)$/);
            if (m && m[1].trim() && m[2].trim()) {
                out.push({ front: m[1].trim(), back: m[2].trim() });
            }
        });
        return out;
    }
    function renderCard() {
        if (!order.length) { return; }
        var card = deck[order[pos]];
        showingFront = true;
        sideLabel.textContent = 'FRONT — click to see the answer';
        cardText.textContent = card.front;
        counterBadge.textContent = (pos + 1) + ' / ' + order.length;
        studyProgress.style.width = Math.round((answered / order.length) * 100) + '%';
    }
    function startStudy(name) {
        deckTitle.textContent = name || 'Study Session';
        order = deck.map(function (_, i) { return i; });
        pos = 0; known = 0; answered = 0;
        scoreBox.classList.add('d-none');
        setupPane.classList.add('d-none');
        studyPane.classList.remove('d-none');
        renderCard();
    }
    function nextCard(knewIt) {
        if (knewIt) { known++; }
        answered++;
        pos++;
        if (pos >= order.length) {
            var pct = Math.round(known / order.length * 100);
            scoreBox.classList.remove('d-none');
            var msg = 'Session complete! Score: ' + known + '/' + order.length + ' (' + pct + '%). ';
            if (pct === 100) { msg += 'Amazing — you remember everything!'; }
            else if (pct >= 70) { msg += 'Very good — practice a little more.'; }
            else { msg += 'Do not give up — shuffle and practice again.'; }
            scoreBox.textContent = msg;
            pos = 0; answered = 0; known = 0;
        }
        renderCard();
    }

    goBtn.addEventListener('click', function () {
        hideError();
        deck = parseCards(cardsInput.value);
        if (deck.length < 2) {
            showError('Write at least 2 cards (each line: Front | Back).');
            return;
        }
        var name = deckName.value.trim();
        if (name) {
            try { localStorage.setItem(SAVE_KEY, JSON.stringify({ name: name, deck: deck })); } catch (e) {}
        }
        startStudy(name || 'Study Session');
    });

    try {
        var saved = localStorage.getItem(SAVE_KEY);
        if (saved) {
            var obj = JSON.parse(saved);
            if (obj && obj.deck && obj.deck.length >= 2) {
                loadSavedBtn.classList.remove('d-none');
                loadSavedBtn.textContent = 'Load Saved Deck: ' + obj.name + ' (' + obj.deck.length + ' cards)';
                loadSavedBtn.addEventListener('click', function () {
                    hideError();
                    deck = obj.deck;
                    deckName.value = obj.name;
                    startStudy(obj.name);
                });
            }
        }
    } catch (e) {}

    flipCard.addEventListener('click', function () {
        if (!order.length) { return; }
        var card = deck[order[pos]];
        showingFront = !showingFront;
        if (showingFront) {
            sideLabel.textContent = 'FRONT — click to see the answer';
            cardText.textContent = card.front;
        } else {
            sideLabel.textContent = 'BACK — click to go back to the front';
            cardText.textContent = card.back;
        }
    });
    knowBtn.addEventListener('click', function () { nextCard(true); });
    dontBtn.addEventListener('click', function () { nextCard(false); });
    shuffleBtn.addEventListener('click', function () {
        for (var i = order.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = order[i]; order[i] = order[j]; order[j] = t;
        }
        pos = 0; answered = 0; known = 0;
        scoreBox.classList.add('d-none');
        renderCard();
    });
    exportBtn.addEventListener('click', function () {
        var text = deck.map(function (c) { return c.front + ' | ' + c.back; }).join('\n');
        var blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'flashcards.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 1000);
    });
    newDeckBtn.addEventListener('click', function () {
        studyPane.classList.add('d-none');
        setupPane.classList.remove('d-none');
    });
})();
</script>
@endsection
