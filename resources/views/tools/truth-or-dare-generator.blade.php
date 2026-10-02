@extends('layouts.app')
@section('title', 'Truth or Dare Generator - Azlaan Tools')
@section('meta_description', 'Get fun truth questions and dares for parties, sleepovers and friends. Family-friendly random generator — free online game.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Truth or Dare Generator</h1>
            <p class="lead text-muted">Play it with friends at a party, sleepover or get-together. Press Truth or Dare — you get a random question or challenge. Everything is family-friendly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <label for="playerName" class="form-label fw-semibold">Player name (optional)</label>
                        <input type="text" class="form-control text-center" id="playerName" placeholder="e.g. Ali" style="max-width: 280px; margin: 0 auto;">
                    </div>
                    <div class="mb-3">
                        <span class="form-label fw-semibold d-block">Difficulty</span>
                        <div class="btn-group" role="group" aria-label="Difficulty">
                            <input type="radio" class="btn-check" name="diff" id="diffEasy" value="easy" checked>
                            <label class="btn btn-outline-success" for="diffEasy">Easy</label>
                            <input type="radio" class="btn-check" name="diff" id="diffMedium" value="medium">
                            <label class="btn btn-outline-warning" for="diffMedium">Medium</label>
                            <input type="radio" class="btn-check" name="diff" id="diffSpicy" value="spicy">
                            <label class="btn btn-outline-danger" for="diffSpicy">Spicy</label>
                        </div>
                    </div>

                    <div id="cardBox" class="border rounded p-4 bg-light mb-3" style="min-height: 170px;">
                        <p class="text-muted mb-0" id="cardHint">Choose Truth or Dare — the question appears here 🎲</p>
                        <div id="cardOut" class="d-none">
                            <span id="cardType" class="badge fs-6 mb-2"></span>
                            <h2 class="h5" id="cardText"></h2>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" id="truthBtn" class="btn btn-primary btn-lg">🤔 Truth</button>
                        <button type="button" id="dareBtn" class="btn btn-danger btn-lg">🔥 Dare</button>
                        <button type="button" id="skipBtn" class="btn btn-outline-secondary btn-lg" disabled>Skip ↻</button>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Rounds played: <span id="roundCount" class="fw-semibold">0</span> &nbsp;|&nbsp; Truths: <span id="truthCount" class="fw-semibold">0</span> &nbsp;|&nbsp; Dares: <span id="dareCount" class="fw-semibold">0</span></p>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-start">
                        <span class="form-label fw-semibold d-block mb-2">History (this session)</span>
                        <ol id="historyList" class="small"></ol>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write a player name (optional) and choose a difficulty.</li>
                <li>Press Truth or Dare — a question or challenge appears on the card.</li>
                <li>If you do not like it, press Skip. History keeps saving below.</li>
            </ol>
            <p class="text-muted small">House rule: any player can say "pass" at any time — the game is for fun, not pressure.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var truthBtn = document.getElementById('truthBtn');
    var dareBtn = document.getElementById('dareBtn');
    var skipBtn = document.getElementById('skipBtn');
    var playerName = document.getElementById('playerName');
    var cardHint = document.getElementById('cardHint');
    var cardOut = document.getElementById('cardOut');
    var cardType = document.getElementById('cardType');
    var cardText = document.getElementById('cardText');
    var roundCount = document.getElementById('roundCount');
    var truthCount = document.getElementById('truthCount');
    var dareCount = document.getElementById('dareCount');
    var results = document.getElementById('results');
    var historyList = document.getElementById('historyList');
    var errorBox = document.getElementById('errorBox');

    var TRUTHS = {
        easy: [
            'What is your favorite food and why?',
            'What is the funniest dream you ever had?',
            'If you could have any superpower for a day, what would it be?',
            'What is your favorite childhood memory?',
            'What song do you secretly love singing in the shower?',
            'If you could visit any country, where would you go?',
            'What is the nicest thing anyone ever did for you?',
            'What talent do you wish you had?',
            'What is your favorite movie of all time?',
            'Describe your perfect weekend in three words.'
        ],
        medium: [
            'What is the most embarrassing thing that happened to you at school?',
            'Have you ever pretended to be sick to skip something? What was it?',
            'What is a secret talent nobody knows about?',
            'What is the biggest lie you told as a kid?',
            'Who was your first crush?',
            'What is something you are scared to try but want to?',
            'Have you ever eavesdropped on someone\u2019s conversation? What did you hear?',
            'What is the weirdest habit you have?',
            'Have you ever blamed someone else for something you did?',
            'What is one thing you would change about yourself if you could?'
        ],
        spicy: [
            'What is the most daring thing you have ever done?',
            'Have you ever snooped through someone\u2019s phone? What did you find?',
            'What is a rumor you once started or spread?',
            'What is the longest you have gone without showering?',
            'Have you ever lied to get out of trouble at work or school?',
            'What is something you have done that you hope your parents never find out?',
            'Have you ever pretended to like a gift you hated?',
            'What is the most trouble you ever got into?',
            'Have you ever broken something and blamed a sibling or friend?',
            'What is a white lie you tell regularly?'
        ]
    };
    var DARES = {
        easy: [
            'Do your best impression of a famous actor for 30 seconds.',
            'Sing the chorus of any song in a funny voice.',
            'Do 10 jumping jacks while counting out loud.',
            'Speak in an accent for the next 3 rounds.',
            'Draw a cat with your eyes closed and show everyone.',
            'Balance a book on your head and walk across the room.',
            'Text a friend "You are awesome" right now.',
            'Do a silly dance for 30 seconds.',
            'Name 5 countries in 10 seconds.',
            'Let someone style your hair however they want.'
        ],
        medium: [
            'Let the group send one text from your phone (they choose the words, you approve).',
            'Eat a spoonful of a condiment chosen by the group.',
            'Do 15 push-ups right now.',
            'Call a family member and tell them you love them on speaker.',
            'Wear your shirt backwards for the next 3 rounds.',
            'Talk without smiling for 2 minutes — if you smile, do it again.',
            'Let someone draw on your hand with a pen.',
            'Do your best animal impression until someone guesses it.',
            'Post a compliment about the person on your left as your status.',
            'Swap one clothing item with the person next to you for 2 rounds.'
        ],
        spicy: [
            'Let the group go through your photo gallery for 30 seconds.',
            'Read your last 3 sent messages out loud.',
            'Do 25 squats while the group counts.',
            'Let someone write anything (clean) on your forehead with a marker.',
            'Call a friend and sing "Happy Birthday" to them for no reason.',
            'Eat a raw onion slice.',
            'Do a dramatic reading of the group\u2019s funniest chat message.',
            'Let the group pick your profile picture for the next hour.',
            'Hold an ice cube in your hand until it melts halfway.',
            'Do your best impression of the person on your right.'
        ]
    };

    var rounds = 0, truths = 0, dares = 0;
    var used = { truth: [], dare: [] };
    var current = null;

    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function getDiff() {
        var el = document.querySelector('input[name="diff"]:checked');
        return el ? el.value : 'easy';
    }

    function pick(type) {
        hideError();
        var diff = getDiff();
        var pool = (type === 'truth' ? TRUTHS : DARES)[diff];
        var key = type;
        var available = pool.filter(function (_, i) { return used[key].indexOf(i) === -1; });
        if (!available.length) { used[key] = []; available = pool.slice(); }
        var text = available[Math.floor(Math.random() * available.length)];
        used[key].push(pool.indexOf(text));
        current = { type: type, text: text, diff: diff };
        showCard();
        rounds++;
        if (type === 'truth') truths++; else dares++;
        roundCount.textContent = rounds;
        truthCount.textContent = truths;
        dareCount.textContent = dares;
        skipBtn.disabled = false;
        addHistory(type, text);
    }

    function showCard() {
        var name = playerName.value.trim();
        cardHint.classList.add('d-none');
        cardOut.classList.remove('d-none');
        cardType.textContent = current.type === 'truth' ? 'TRUTH' : 'DARE';
        cardType.className = 'badge fs-6 mb-2 ' + (current.type === 'truth' ? 'bg-primary' : 'bg-danger');
        cardText.textContent = (name ? name + ', ' : '') + current.text;
        cardOut.style.opacity = '0';
        var step = 0;
        var anim = setInterval(function () {
            step += 0.2;
            cardOut.style.opacity = String(Math.min(1, step));
            if (step >= 1) clearInterval(anim);
        }, 40);
    }

    function addHistory(type, text) {
        results.classList.remove('d-none');
        var li = document.createElement('li');
        var badge = document.createElement('span');
        badge.className = 'badge me-1 ' + (type === 'truth' ? 'bg-primary' : 'bg-danger');
        badge.textContent = type.toUpperCase();
        li.appendChild(badge);
        li.appendChild(document.createTextNode(text));
        historyList.prepend(li);
        while (historyList.children.length > 20) historyList.removeChild(historyList.lastChild);
    }

    truthBtn.addEventListener('click', function () { pick('truth'); });
    dareBtn.addEventListener('click', function () { pick('dare'); });
    skipBtn.addEventListener('click', function () {
        if (current) pick(current.type);
    });
})();
</script>
@endsection
