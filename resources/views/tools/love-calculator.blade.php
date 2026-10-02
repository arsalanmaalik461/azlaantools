@extends('layouts.app')

@section('title', 'Love Percentage Calculator - Fun Name Match | Azlaan Tools')
@section('meta_description', 'Free love percentage calculator for fun. Enter two names and get a playful compatibility percentage instantly. For entertainment only, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Love Percentage Calculator</h1>
            <p class="lead text-muted">Enter two names and get a fun love percentage — same names always give the same result!</p>
            <div class="alert alert-info">Just for fun — for entertainment only 😄 — this is not a real prediction.</div>
            <div class="card shadow-sm mb-4"><div class="card-body text-center">
                <div class="row g-3">
                    <div class="col-md-5"><label class="form-label" for="name1">Your Name</label><input type="text" class="form-control text-center" id="name1" placeholder="e.g. Ali"></div>
                    <div class="col-md-2 fs-2">❤️</div>
                    <div class="col-md-5"><label class="form-label" for="name2">Partner Name</label><input type="text" class="form-control text-center" id="name2" placeholder="e.g. Fatima"></div>
                </div>
                <button type="button" class="btn btn-danger mt-3 px-4" id="calcBtn">Calculate Love %</button>
                <div class="alert alert-warning mt-3 d-none" id="errBox">Please enter both names.</div>
                <div class="d-none mt-4" id="resultBox">
                    <div class="fs-1" id="heartIcon">💖</div>
                    <div class="display-4 fw-bold text-danger" id="pctOut">0%</div>
                    <div class="progress mt-3" style="height: 28px;"><div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" id="bar" role="progressbar" style="width: 0%;"></div></div>
                    <p class="fs-5 mt-3 mb-0" id="msgOut"></p>
                </div>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Enter your name and your partner's name.</li><li>Click Calculate Love %.</li><li>Share the fun result — remember, it is just for entertainment!</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
#heartIcon { animation: pulse 1s ease-in-out infinite; display: inline-block; }
@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.25); } }
</style>
@endsection

@section('scripts')
<script>
(function () {
    function hashCode(s) { var h = 0; for (var i = 0; i < s.length; i++) { h = ((h << 5) - h + s.charCodeAt(i)) | 0; } return Math.abs(h); }
    document.getElementById('calcBtn').addEventListener('click', function () {
        var n1 = document.getElementById('name1').value.trim().toLowerCase(), n2 = document.getElementById('name2').value.trim().toLowerCase();
        var err = document.getElementById('errBox');
        if (!n1 || !n2) { err.classList.remove('d-none'); return; }
        err.classList.add('d-none');
        var pair = [n1, n2].sort().join(' loves ');
        var pct = 35 + (hashCode(pair) % 66); // 35-100, always decent and deterministic
        var msg = pct >= 90 ? 'Made for each other! Perfect match! 💍' : pct >= 75 ? 'Beautiful couple — great match! 🌹' : pct >= 60 ? 'Good chemistry — keep it growing! 😊' : pct >= 45 ? 'Strong friendship, love can grow too! 🙂' : 'A little effort, a little smile — all will be fine! 💪';
        var box = document.getElementById('resultBox'); box.classList.remove('d-none');
        var bar = document.getElementById('bar'), out = document.getElementById('pctOut');
        var current = 0; bar.style.width = '0%';
        var iv = setInterval(function () { current += 2; if (current >= pct) { current = pct; clearInterval(iv); } out.textContent = current + '%'; bar.style.width = current + '%'; }, 25);
        document.getElementById('msgOut').textContent = msg;
    });
})();
</script>
@endsection
