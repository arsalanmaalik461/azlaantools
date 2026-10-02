@extends('layouts.app')

@section('title', 'Dice Roller & Coin Flip - Roll Dice Online Free | Azlaan Tools')
@section('meta_description', 'Free dice roller and coin flip tool: roll 1 to 3 dice with animation and flip a coin with heads and tails statistics. No signup needed.')

@section('content')
<style>
.shake { animation: shakeAnim 0.5s ease; }
.flipanim { animation: flipAnim 0.6s ease; }
@@keyframes shakeAnim { 0% { transform: translateX(0); } 25% { transform: translateX(-8px) rotate(-8deg); } 50% { transform: translateX(8px) rotate(8deg); } 75% { transform: translateX(-5px); } 100% { transform: translateX(0); } }
@@keyframes flipAnim { 0% { transform: rotateY(0); } 100% { transform: rotateY(720deg); } }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Dice Roller &amp; Coin Flip</h1>
            <p class="lead text-muted">Roll up to 3 dice or flip a coin for games and quick decisions — free, fun and no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h2 class="h5">Dice Roller</h2>
                    <div id="diceDisplay" class="my-3" style="font-size:5rem;line-height:1;">&#9860;</div>
                    <div class="fw-bold fs-5 mb-3" id="diceTotal">Total: -</div>
                    <label for="diceCount" class="form-label fw-semibold">Number of dice</label>
                    <select class="form-select w-auto mx-auto mb-3" id="diceCount">
                        <option value="1">1 Dice</option>
                        <option value="2" selected>2 Dice</option>
                        <option value="3">3 Dice</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-lg" id="rollBtn">Roll Dice</button>
                    <div class="small text-muted mt-2" id="diceHistory">No rolls yet.</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h2 class="h5">Coin Flip</h2>
                    <div id="coinDisplay" class="my-3" style="font-size:5rem;line-height:1;">&#129689;</div>
                    <div class="fw-bold fs-5 mb-2" id="coinResult">Heads or Tails?</div>
                    <div class="small text-muted mb-3" id="coinStats">Total flips: 0 | Heads: 0 (0%) | Tails: 0 (0%)</div>
                    <button type="button" class="btn btn-success btn-lg" id="flipBtn">Flip Coin</button>
                    <button type="button" class="btn btn-outline-secondary" id="resetCoinBtn">Reset Stats</button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Choose 1, 2 or 3 dice and click <strong>Roll Dice</strong> — the total is shown instantly.</li>
                        <li>Click <strong>Flip Coin</strong> to get Heads or Tails with a flip animation.</li>
                        <li>Watch the coin statistics update with total flips and heads / tails percentages.</li>
                        <li>Use <strong>Reset Stats</strong> any time to start the coin count fresh.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var faces = ['&#9856;', '&#9857;', '&#9858;', '&#9859;', '&#9860;', '&#9861;'];
    var rollHistory = [];
    document.getElementById('rollBtn').addEventListener('click', function () {
        var count = parseInt(document.getElementById('diceCount').value, 10);
        var html = '', total = 0, vals = [];
        for (var i = 0; i < count; i++) {
            var v = Math.floor(Math.random() * 6) + 1;
            vals.push(v); total += v;
            html += '<span class="d-inline-block mx-1">' + faces[v - 1] + '</span>';
        }
        var disp = document.getElementById('diceDisplay');
        disp.innerHTML = html;
        disp.classList.remove('shake');
        void disp.offsetWidth;
        disp.classList.add('shake');
        document.getElementById('diceTotal').textContent = 'You rolled ' + vals.join(' + ') + ' = Total: ' + total;
        rollHistory.unshift(vals.join(', ') + ' (total ' + total + ')');
        if (rollHistory.length > 5) { rollHistory.pop(); }
        document.getElementById('diceHistory').textContent = 'Recent rolls: ' + rollHistory.join(' | ');
    });
    var flips = 0, heads = 0, tails = 0;
    document.getElementById('flipBtn').addEventListener('click', function () {
        var isHeads = Math.random() < 0.5;
        flips++;
        if (isHeads) { heads++; } else { tails++; }
        var disp = document.getElementById('coinDisplay');
        disp.innerHTML = '&#129689;';
        disp.classList.remove('flipanim');
        void disp.offsetWidth;
        disp.classList.add('flipanim');
        document.getElementById('coinResult').textContent = isHeads ? 'Heads! (heads side up)' : 'Tails!';
        var hp = flips ? Math.round((heads / flips) * 100) : 0;
        var tp = flips ? Math.round((tails / flips) * 100) : 0;
        document.getElementById('coinStats').textContent = 'Total flips: ' + flips + ' | Heads: ' + heads + ' (' + hp + '%) | Tails: ' + tails + ' (' + tp + '%)';
    });
    document.getElementById('resetCoinBtn').addEventListener('click', function () {
        flips = 0; heads = 0; tails = 0;
        document.getElementById('coinResult').textContent = 'Heads or Tails?';
        document.getElementById('coinStats').textContent = 'Total flips: 0 | Heads: 0 (0%) | Tails: 0 (0%)';
    });
})();
</script>
@endsection
