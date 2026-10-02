@extends('layouts.app')
@section('title', 'BPM Tap Tempo Counter — Free Online Tool')
@section('meta_description', 'Tap along with music to measure beats per minute instantly')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">BPM Tap Tempo Counter</h1>
            <p class="lead small text-muted">Tap the big button along with the beat of any song and get its tempo in beats per minute instantly.</p>

                    <div class="text-center">
                        <button type="button" id="bpTap" class="btn btn-primary btn-lg w-100 py-5 fs-3">TAP ON THE BEAT</button>
                        <div class="row g-3 mt-3">
                            <div class="col-4"><div class="border rounded p-3"><div class="fs-2 fw-bold" id="bpBpm">-</div><div class="small text-muted">BPM</div></div></div>
                            <div class="col-4"><div class="border rounded p-3"><div class="fs-2 fw-bold" id="bpCount">0</div><div class="small text-muted">Taps</div></div></div>
                            <div class="col-4"><div class="border rounded p-3"><div class="fs-2 fw-bold" id="bpMs">-</div><div class="small text-muted">ms per beat</div></div></div>
                        </div>
                        <button type="button" id="bpReset" class="btn btn-outline-secondary mt-3">Reset</button>
                        <p class="small text-muted mt-2 mb-0" id="bpNote">Tap at least 4 times for a steady reading. Waiting more than 2 seconds between taps starts a fresh measurement.</p>
                    </div>

            <div id="bpMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Play the song anywhere and tap the big button exactly on each beat.</li>
                    <li>After a few taps the BPM reading steadies.</li>
                    <li>Press Reset to measure a different song.</li>
            </ol>
            <p class="small text-muted mb-0">The counter averages the gaps between your recent taps. Human tapping has small errors, so treat the result as accurate to about plus or minus 1 BPM with steady tapping.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("bpMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    var taps = [];
    function show() {
        document.getElementById("bpCount").textContent = taps.length;
        if (taps.length < 2) { document.getElementById("bpBpm").textContent = "-"; document.getElementById("bpMs").textContent = "-"; return; }
        var gaps = [];
        for (var i = 1; i < taps.length; i++) gaps.push(taps[i] - taps[i - 1]);
        var recent = gaps.slice(-12), avg = recent.reduce(function (a, b) { return a + b; }, 0) / recent.length;
        document.getElementById("bpMs").textContent = Math.round(avg);
        document.getElementById("bpBpm").textContent = Math.round(60000 / avg);
    }
    document.getElementById("bpTap").addEventListener("click", function () {
        var now = performance.now();
        if (taps.length && now - taps[taps.length - 1] > 2000) taps = [];
        taps.push(now); if (taps.length > 32) taps.shift();
        show();
    });
    document.getElementById("bpReset").addEventListener("click", function () { taps = []; show(); showMsg("Counter reset. Start tapping on the beat."); });

})();
</script>
@endsection
