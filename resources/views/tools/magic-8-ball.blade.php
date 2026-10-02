@extends('layouts.app')

@section('title', 'Magic 8 Ball Online - Azlaan Tools')
@section('meta_description', 'Ask a yes or no question and shake the magic 8 ball for a fun random answer, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Magic 8 Ball Online</h1>
            <p class="lead text-muted">Ask a yes/no question, shake the ball and get a fun answer. Just for fun!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <style>
                        .eight-ball { width: 220px; height: 220px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #3a3a4a, #0a0a12 70%); margin: 0 auto; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,.35); }
                        .eight-ball .window { width: 110px; height: 110px; border-radius: 50%; background: #101018; border: 3px solid #2c2c3a; position: absolute; top: 55px; left: 55px; display: flex; align-items: center; justify-content: center; }
                        .eight-ball .answer { color: #7fd0ff; font-size: 13px; font-weight: bold; padding: 8px; text-align: center; line-height: 1.25; }
                        .eight-ball .eight { position: absolute; top: 12px; left: 0; right: 0; color: #fff; font-size: 22px; font-weight: bold; }
                        .shaking { animation: shakeAnim .7s ease-in-out; }
                        @keyframes shakeAnim {
                            0%,100% { transform: translateX(0) rotate(0); }
                            20% { transform: translateX(-18px) rotate(-8deg); }
                            40% { transform: translateX(16px) rotate(7deg); }
                            60% { transform: translateX(-12px) rotate(-5deg); }
                            80% { transform: translateX(8px) rotate(4deg); }
                        }
                    </style>

                    <div class="eight-ball" id="ball">
                        <div class="eight">8</div>
                        <div class="window"><div class="answer" id="ballAnswer">Ask me<br>anything</div></div>
                    </div>

                    <div class="mb-3 mt-4">
                        <label for="question" class="form-label fw-semibold">Your yes/no question</label>
                        <input type="text" class="form-control" id="question" placeholder="e.g. Will I pass my exam?">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Shake the Ball</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-start">
                        <h5>Answer history</h5>
                        <ul class="list-group" id="historyList"></ul>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Just for fun — make important decisions with thinking and advice.</p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write your yes/no question.</li>
                <li>Press "Shake the Ball".</li>
                <li>Read the ball's answer — shake again to ask once more.</li>
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
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var ball = document.getElementById('ball');
    var ballAnswer = document.getElementById('ballAnswer');
    var historyList = document.getElementById('historyList');
    var busy = false;

    var ANSWERS = [
        'It is certain.', 'Without a doubt.', 'Yes, definitely.', 'Most likely.',
        'Signs point to yes.', 'As I see it, yes.', 'Reply hazy, try again.',
        'Ask again later.', 'Better not tell you now.', 'Cannot predict now.',
        'Concentrate and ask again.', 'Do not count on it.', 'My reply is no.',
        'My sources say no.', 'Outlook not so good.', 'Very doubtful.',
        'Yes, in due time.', 'Absolutely!', 'The odds are in your favor.', 'Chances are slim.'
    ];

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
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function addHistory(q, a) {
        var li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = '<strong>Q:</strong> ' + esc(q) + '<br><strong class="text-primary">A:</strong> ' + esc(a);
        historyList.insertBefore(li, historyList.firstChild);
        while (historyList.children.length > 10) historyList.removeChild(historyList.lastChild);
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (busy) return;
        var q = document.getElementById('question').value.trim();
        if (!q) { showError('Please ask a question first.'); return; }

        busy = true;
        goBtn.disabled = true;
        ballAnswer.innerHTML = '...';
        ball.classList.remove('shaking');
        // restart animation
        void ball.offsetWidth;
        ball.classList.add('shaking');

        setTimeout(function () {
            var ans = ANSWERS[Math.floor(Math.random() * ANSWERS.length)];
            ballAnswer.textContent = ans;
            addHistory(q, ans);
            busy = false;
            goBtn.disabled = false;
            document.getElementById('question').value = '';
        }, 750);
    });
})();
</script>
@endsection
