@extends('layouts.app')
@section('title', 'Morse Code Translator — Free Online Tool')
@section('meta_description', 'Translate text to Morse code and decode Morse back to text with audio playback')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Morse Code Translator</h1>
            <p class="lead small text-muted">Translate text into Morse code dots and dashes, decode Morse back into text, and listen to it played as real beeps.</p>

                    <label class="form-label fw-semibold" for="mcIn">Text or Morse code</label>
                    <textarea id="mcIn" class="form-control" rows="5" placeholder="Type text, or Morse with spaces between letters and / between words"></textarea>
                    <div class="row g-3 mt-1 align-items-end">
                        <div class="col-md-4"><label class="form-label" for="mcSpeed">Playback speed (WPM)</label><input type="number" id="mcSpeed" class="form-control" value="15" min="5" max="40"></div>
                        <div class="col-md-8 d-flex flex-wrap gap-2">
                            <button type="button" id="mcEnc" class="btn btn-primary">Text to Morse</button>
                            <button type="button" id="mcDec" class="btn btn-outline-primary">Morse to Text</button>
                            <button type="button" id="mcPlay" class="btn btn-success">Play Morse Audio</button>
                            <button type="button" id="mcCopy" class="btn btn-outline-secondary">Copy Result</button>
                        </div>
                    </div>
                    <label class="form-label mt-3" for="mcOut">Result</label>
                    <textarea id="mcOut" class="form-control font-monospace" rows="5" readonly></textarea>

            <div id="mcMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type plain text and click Text to Morse, or paste Morse and click Morse to Text.</li>
                    <li>Use spaces between letter codes and a slash between words when decoding.</li>
                    <li>Click Play Morse Audio to hear the result as beeps at your chosen speed.</li>
            </ol>
            <p class="small text-muted mb-0">Audio uses standard timing: a dash is three dots long, gaps inside a letter are one dot, between letters three dots and between words seven dots. Words per minute follows the PARIS standard.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("mcMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    var MAP = { A: ".-", B: "-...", C: "-.-.", D: "-..", E: ".", F: "..-.", G: "--.", H: "....", I: "..", J: ".---", K: "-.-", L: ".-..", M: "--", N: "-.", O: "---", P: ".--.", Q: "--.-", R: ".-.", S: "...", T: "-", U: "..-", V: "...-", W: ".--", X: "-..-", Y: "-.--", Z: "--..", "0": "-----", "1": ".----", "2": "..---", "3": "...--", "4": "....-", "5": ".....", "6": "-....", "7": "--...", "8": "---..", "9": "----.", ".": ".-.-.-", ",": "--..--", "?": "..--..", "!": "-.-.--", ":": "---...", "'": ".----.", "-": "-....-", "/": "-..-." };
    var REV = {}; Object.keys(MAP).forEach(function (k) { REV[MAP[k]] = k; });
    document.getElementById("mcEnc").addEventListener("click", function () {
        var out = document.getElementById("mcIn").value.toUpperCase().split("").map(function (ch) {
            if (ch === " " || ch === "\n") return "/";
            return MAP[ch] || "";
        }).filter(function (x) { return x !== ""; }).join(" ");
        out = out.replace(/(\/ )+/g, "/ ").replace(/ \/(\s|$)/g, " / ");
        document.getElementById("mcOut").value = out;
        showMsg("Converted to Morse code.");
    });
    document.getElementById("mcDec").addEventListener("click", function () {
        var tokens = document.getElementById("mcIn").value.trim().split(/\s+/);
        var out = tokens.map(function (t) { if (t === "/") return " "; return REV[t] || ""; }).join("");
        document.getElementById("mcOut").value = out.trim();
        showMsg("Decoded. Unknown codes are skipped.");
    });
    document.getElementById("mcPlay").addEventListener("click", function () {
        var code = document.getElementById("mcOut").value.trim();
        if (!code || (code.indexOf(".") === -1 && code.indexOf("-") === -1)) { showMsg("Convert some text to Morse first, then play it.", false); return; }
        var wpm = Math.max(5, Math.min(40, Number(document.getElementById("mcSpeed").value) || 15));
        var unit = 1200 / wpm;
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === "suspended") ctx.resume();
        var t = ctx.currentTime + 0.1;
        function beep(dur) {
            var o = ctx.createOscillator(), g = ctx.createGain();
            o.frequency.value = 650; o.type = "sine";
            g.gain.setValueAtTime(0.0001, t); g.gain.exponentialRampToValueAtTime(0.25, t + 0.01);
            g.gain.setValueAtTime(0.25, t + dur - 0.01); g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
            o.connect(g); g.connect(ctx.destination); o.start(t); o.stop(t + dur);
            t += dur;
        }
        code.split("").forEach(function (ch) {
            if (ch === ".") { beep(unit / 1000); t += unit / 1000; }
            else if (ch === "-") { beep(unit * 3 / 1000); t += unit / 1000; }
            else if (ch === " ") t += unit * 2 / 1000;
            else if (ch === "/") t += unit * 4 / 1000;
        });
        showMsg("Playing Morse at " + wpm + " WPM (about " + Math.max(1, Math.round(t - ctx.currentTime)) + "s).");
    });
    document.getElementById("mcCopy").addEventListener("click", function () { copyText(document.getElementById("mcOut").value, "Result copied."); });

})();
</script>
@endsection
