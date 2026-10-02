@extends('layouts.app')
@section('title', 'White Noise Generator — Free Online Tool')
@section('meta_description', 'Play white pink and brown noise for sleep focus and baby comfort')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">White Noise Generator</h1>
            <p class="lead small text-muted">Play looping white, pink or brown noise in your browser for sleep, focus, studying or calming a baby. No audio files needed.</p>

                    <div class="text-center">
                        <div class="btn-group btn-group-lg flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-primary" data-noise="white">White Noise</button>
                            <button type="button" class="btn btn-outline-primary" data-noise="pink">Pink Noise</button>
                            <button type="button" class="btn btn-outline-primary" data-noise="brown">Brown Noise</button>
                        </div>
                        <div class="fs-4 fw-bold mt-3" id="wnState">Stopped</div>
                        <label class="form-label mt-2" for="wnVol">Volume: <span id="wnVolVal">50</span>%</label>
                        <input type="range" id="wnVol" class="form-range" min="0" max="100" value="50">
                        <label class="form-label" for="wnTimer">Sleep timer</label>
                        <select id="wnTimer" class="form-select w-auto mx-auto"><option value="0">Off (plays until stopped)</option><option value="15">Stop after 15 minutes</option><option value="30">Stop after 30 minutes</option><option value="60">Stop after 60 minutes</option></select>
                        <div class="mt-3"><button type="button" id="wnStop" class="btn btn-danger btn-lg">Stop Noise</button></div>
                    </div>

            <div id="wnMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose White, Pink or Brown noise.</li>
                    <li>Set the volume and an optional sleep timer.</li>
                    <li>Press Stop Noise any time, or let the timer stop it for you.</li>
            </ol>
            <p class="small text-muted mb-0">White noise has equal energy at every frequency and sounds like TV static. Pink noise is softer and more balanced, and brown noise is deep and rumbly like distant thunder. All three are generated live with the Web Audio API.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("wnMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    var actx = null, srcNode = null, gainNode = null, timerId = null, current = "";
    function makeBuffer(type) {
        var len = actx.sampleRate * 3, buf = actx.createBuffer(1, len, actx.sampleRate), d = buf.getChannelData(0);
        if (type === "white") { for (var i = 0; i < len; i++) d[i] = Math.random() * 2 - 1; }
        else if (type === "pink") {
            var b0 = 0, b1 = 0, b2 = 0;
            for (var j = 0; j < len; j++) { var w = Math.random() * 2 - 1; b0 = 0.997 * b0 + 0.029 * w; b1 = 0.985 * b1 + 0.032 * w; b2 = 0.95 * b2 + 0.048 * w; d[j] = (b0 + b1 + b2 + w * 0.05) * 2.2; }
        } else {
            var last = 0;
            for (var k = 0; k < len; k++) { var w2 = Math.random() * 2 - 1; last = (last + 0.02 * w2) / 1.02; d[k] = last * 3.5; }
        }
        return buf;
    }
    function stopNoise(silent) {
        if (srcNode) { try { srcNode.stop(); } catch (e) {} srcNode = null; }
        if (timerId) { clearTimeout(timerId); timerId = null; }
        current = ""; document.getElementById("wnState").textContent = "Stopped";
        if (!silent) showMsg("Noise stopped.");
    }
    function playNoise(type) {
        if (!actx) actx = new (window.AudioContext || window.webkitAudioContext)();
        if (actx.state === "suspended") actx.resume();
        stopNoise(true);
        srcNode = actx.createBufferSource(); srcNode.buffer = makeBuffer(type); srcNode.loop = true;
        if (!gainNode) { gainNode = actx.createGain(); gainNode.connect(actx.destination); }
        gainNode.gain.value = Number(document.getElementById("wnVol").value) / 100 * 0.6;
        srcNode.connect(gainNode); srcNode.start();
        current = type;
        document.getElementById("wnState").textContent = "Playing: " + type + " noise";
        var mins = Number(document.getElementById("wnTimer").value);
        if (mins > 0) timerId = setTimeout(function () { stopNoise(); showMsg("Sleep timer finished, noise stopped."); }, mins * 60000);
        showMsg("Playing " + type + " noise" + (mins > 0 ? ", will stop in " + mins + " minutes." : "."));
    }
    document.querySelectorAll("[data-noise]").forEach(function (btn) { btn.addEventListener("click", function () { playNoise(btn.getAttribute("data-noise")); }); });
    document.getElementById("wnVol").addEventListener("input", function () { document.getElementById("wnVolVal").textContent = this.value; if (gainNode) gainNode.gain.value = Number(this.value) / 100 * 0.6; });
    document.getElementById("wnTimer").addEventListener("change", function () { if (current) playNoise(current); });
    document.getElementById("wnStop").addEventListener("click", function () { stopNoise(); });

})();
</script>
@endsection
