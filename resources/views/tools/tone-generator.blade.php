@extends('layouts.app')
@section('title', 'Tone and Frequency Generator — Free Online Tool')
@section('meta_description', 'Generate pure tones from 1 Hz to 20000 Hz for testing audio')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Tone and Frequency Generator</h1>
            <p class="lead small text-muted">Generate a pure tone at any frequency from 1 Hz to 20000 Hz, play it live, and download it as a WAV test file.</p>

                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="tgFreq">Frequency (Hz)</label><input type="number" id="tgFreq" class="form-control" value="440" min="1" max="20000" step="1"></div>
                        <div class="col-md-6"><label class="form-label" for="tgType">Waveform</label><select id="tgType" class="form-select"><option value="sine">Sine (pure)</option><option value="square">Square</option><option value="triangle">Triangle</option><option value="sawtooth">Sawtooth</option></select></div>
                        <div class="col-12"><label class="form-label" for="tgSlider">Frequency slider: <span id="tgFreqVal">440</span> Hz</label><input type="range" id="tgSlider" class="form-range" min="0" max="1000" value="500"></div>
                        <div class="col-md-6"><label class="form-label" for="tgVol">Volume: <span id="tgVolVal">50</span>%</label><input type="range" id="tgVol" class="form-range" min="1" max="100" value="50"></div>
                        <div class="col-md-6"><label class="form-label" for="tgDur">Download length (seconds)</label><input type="number" id="tgDur" class="form-control" value="3" min="1" max="60"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="tgPlay" class="btn btn-primary btn-lg">Play Tone</button>
                        <button type="button" id="tgStop" class="btn btn-outline-secondary btn-lg">Stop</button>
                        <button type="button" id="tgPreset" class="btn btn-outline-secondary">Common tones: 50 / 440 / 1000 / 10000 Hz</button>
                        <button type="button" id="tgDl" class="btn btn-success btn-lg">Download WAV</button>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Start at low volume. Very high frequencies and square waves can be harsh, and many laptop speakers cannot reproduce below about 100 Hz or above 15000 Hz.</p>

            <div id="tgMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type a frequency or drag the slider (it is logarithmic, like hearing).</li>
                    <li>Choose a waveform and set a safe volume, then press Play Tone.</li>
                    <li>Optionally download the tone as a WAV file of your chosen length.</li>
            </ol>
            <p class="small text-muted mb-0">The slider maps exponentially from 20 Hz to 20000 Hz so low frequencies get fine control, matching how pitch is perceived.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("tgMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    function bufferToWav(buf) {
        var numCh = Math.min(2, buf.numberOfChannels);
        var sr = buf.sampleRate, len = buf.length, block = numCh * 2, dataSize = len * block;
        var ab = new ArrayBuffer(44 + dataSize), view = new DataView(ab);
        function ws(off, s) { for (var i = 0; i < s.length; i++) view.setUint8(off + i, s.charCodeAt(i)); }
        ws(0, "RIFF"); view.setUint32(4, 36 + dataSize, true); ws(8, "WAVE"); ws(12, "fmt ");
        view.setUint32(16, 16, true); view.setUint16(20, 1, true); view.setUint16(22, numCh, true);
        view.setUint32(24, sr, true); view.setUint32(28, sr * block, true); view.setUint16(32, block, true);
        view.setUint16(34, 16, true); ws(36, "data"); view.setUint32(40, dataSize, true);
        var chans = []; for (var c = 0; c < numCh; c++) chans.push(buf.getChannelData(c));
        var off = 44;
        for (var i = 0; i < len; i++) { for (var ch = 0; ch < numCh; ch++) { var s = Math.max(-1, Math.min(1, chans[ch][i])); view.setInt16(off, s < 0 ? s * 32768 : s * 32767, true); off += 2; } }
        return new Blob([ab], { type: "audio/wav" });
    }

    var actx = null, osc = null, gainNode = null, presets = [50, 440, 1000, 10000], pIdx = 1;
    function freqFromSlider(v) { return Math.round(20 * Math.pow(1000, v / 1000)); }
    function sliderFromFreq(f) { return Math.round(1000 * Math.log(Math.max(20, f) / 20) / Math.log(1000)); }
    function syncFromInput() { var f = Math.max(1, Math.min(20000, Number(document.getElementById("tgFreq").value) || 440)); document.getElementById("tgSlider").value = sliderFromFreq(f); document.getElementById("tgFreqVal").textContent = f; if (osc) osc.frequency.value = f; }
    document.getElementById("tgSlider").addEventListener("input", function () { var f = freqFromSlider(Number(this.value)); document.getElementById("tgFreq").value = f; document.getElementById("tgFreqVal").textContent = f; if (osc) osc.frequency.value = f; });
    document.getElementById("tgFreq").addEventListener("input", syncFromInput);
    document.getElementById("tgVol").addEventListener("input", function () { document.getElementById("tgVolVal").textContent = this.value; if (gainNode) gainNode.gain.value = Number(this.value) / 100 * 0.5; });
    document.getElementById("tgType").addEventListener("change", function () { if (osc) osc.type = this.value; });
    document.getElementById("tgPlay").addEventListener("click", function () {
        if (!actx) actx = new (window.AudioContext || window.webkitAudioContext)();
        if (actx.state === "suspended") actx.resume();
        if (osc) { try { osc.stop(); } catch (e) {} osc = null; }
        osc = actx.createOscillator(); gainNode = actx.createGain();
        osc.type = document.getElementById("tgType").value;
        osc.frequency.value = Math.max(1, Math.min(20000, Number(document.getElementById("tgFreq").value) || 440));
        gainNode.gain.value = Number(document.getElementById("tgVol").value) / 100 * 0.5;
        osc.connect(gainNode); gainNode.connect(actx.destination); osc.start();
        showMsg("Playing " + osc.frequency.value + " Hz " + osc.type + " tone.");
    });
    document.getElementById("tgStop").addEventListener("click", function () { if (osc) { try { osc.stop(); } catch (e) {} osc = null; } showMsg("Tone stopped."); });
    document.getElementById("tgPreset").addEventListener("click", function () { pIdx = (pIdx + 1) % presets.length; document.getElementById("tgFreq").value = presets[pIdx]; syncFromInput(); showMsg("Frequency set to " + presets[pIdx] + " Hz."); });
    document.getElementById("tgDl").addEventListener("click", function () {
        var f = Math.max(1, Math.min(20000, Number(document.getElementById("tgFreq").value) || 440));
        var dur = Math.max(1, Math.min(60, Number(document.getElementById("tgDur").value) || 3));
        var off = new OfflineAudioContext(1, 44100 * dur, 44100);
        var o = off.createOscillator(), g = off.createGain();
        o.type = document.getElementById("tgType").value; o.frequency.value = f; g.gain.value = Number(document.getElementById("tgVol").value) / 100 * 0.5;
        o.connect(g); g.connect(off.destination); o.start();
        off.startRendering().then(function (buf) { dl(bufferToWav(buf), "tone-" + f + "hz.wav"); showMsg("Downloaded " + dur + "s WAV at " + f + " Hz."); });
    });
    syncFromInput();

})();
</script>
@endsection
