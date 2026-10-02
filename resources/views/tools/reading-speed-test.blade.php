@extends('layouts.app')
@section('title', 'Reading Speed Test — Free Online Tool')
@section('meta_description', 'Measure reading speed in words per minute with a timed passage')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Reading Speed Test</h1>
            <p class="lead small text-muted">Read a passage as fast as you honestly can, click finished, and get your reading speed in words per minute.</p>

                    <label class="form-label fw-semibold" for="rsPassage">Choose a passage</label>
                    <select id="rsPassage" class="form-select">
                        <option value="rsP1">Passage 1: The Old Lighthouse (everyday story)</option>
                        <option value="rsP2">Passage 2: How Bees Communicate (science)</option>
                        <option value="rsP3">Passage 3: A Busy Market Morning (descriptive)</option>
                    </select>
                    <div id="rsP1" class="rs-passage border rounded p-3 mt-3">The old lighthouse stood at the edge of the harbor, where the fishing boats returned each evening with their catch. For more than a hundred years its lamp had guided sailors safely home, turning slowly through fog, rain and clear summer nights. The keeper, an elderly man named Tomas, climbed the narrow stairs every evening at sunset. He polished the great glass lens, checked the oil, and watched the sea settle into darkness. Tomas loved the quiet hours. He read books by lamplight, listened to the waves below, and wrote letters to his sister in a distant city. When storms came, the tower shook and the windows rattled, but the light never failed. Sailors said that as long as the lighthouse burned, no ship from their village would be lost. When Tomas finally retired, the whole harbor came to thank him, and the new electric lamp was lit in his honor on a calm evening in June.</div>
                    <div id="rsP2" class="rs-passage border rounded p-3 mt-3 d-none">Honey bees live in colonies that can contain tens of thousands of individuals, yet they coordinate their work with remarkable precision. When a scout bee discovers a rich source of flowers, she returns to the hive and performs a special movement on the honeycomb known as the waggle dance. The angle of her dance in relation to the sun tells the other bees the direction of the flowers, while the length of the central waggle run communicates the distance. Remarkably, the bees adjust their reading of the angle as the sun moves across the sky during the day. Other workers then fly directly to the reported location, even if it lies several kilometers away. Scientists have confirmed this system through careful experiments with marked bees and artificial feeding stations. The dance is one of the most sophisticated forms of communication known in any insect, and it shows how much information can be shared without anything resembling human language.</div>
                    <div id="rsP3" class="rs-passage border rounded p-3 mt-3 d-none">By six in the morning the market was already alive with color and noise. Farmers unloaded crates of tomatoes, mangoes and leafy greens, calling out prices to the early shoppers who carried woven baskets on their arms. The smell of fresh bread drifted from the bakery stall, mixing with cardamom, fried onions and the sharp sweetness of cut melon. Porters hurried between the rows with loaded carts, warning everyone aside with friendly shouts. In the cloth section, bolts of bright fabric hung like flags, and tailors measured customers beside humming sewing machines. Children weaved through the crowd with trays of tea, earning coins from grateful stallholders. By noon the sun stood high, the best produce was gone, and the lanes slowly emptied. The shopkeepers swept their stalls, counted the morning earnings, and settled down for a quiet hour of rest before the evening crowd returned.</div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" id="rsStart" class="btn btn-primary btn-lg">Start Reading</button>
                        <button type="button" id="rsDone" class="btn btn-success btn-lg" disabled>I Finished Reading</button>
                        <button type="button" id="rsReset" class="btn btn-outline-secondary btn-lg">Reset</button>
                    </div>
                    <p class="mt-3 mb-0">Status: <strong id="rsStatus">Press Start, then read the passage at your normal honest pace.</strong></p>
                    <div class="row g-3 mt-1 text-center">
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="rsWpm">-</div><div class="small text-muted">Words per minute</div></div></div>
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="rsTime">-</div><div class="small text-muted">Seconds taken</div></div></div>
                        <div class="col-4"><div class="border rounded p-3"><div class="fs-3 fw-bold" id="rsWords">-</div><div class="small text-muted">Passage words</div></div></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="rsLevel"></p>

            <div id="rsMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose a passage, then press Start Reading and read it fully at an honest pace.</li>
                    <li>The moment you finish, press I Finished Reading.</li>
                    <li>Read your words per minute score and the guide below it.</li>
            </ol>
            <p class="small text-muted mb-0">Typical adult reading speeds for non fiction are roughly 200 to 300 words per minute with good comprehension. Speed alone means little without understanding, so read honestly rather than skimming.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rsMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    var startAt = null;
    function currentPassage() { return document.getElementById(document.getElementById("rsPassage").value); }
    function wordCount() { return currentPassage().textContent.trim().split(/\s+/).filter(Boolean).length; }
    document.getElementById("rsPassage").addEventListener("change", function () {
        document.querySelectorAll(".rs-passage").forEach(function (el) { el.classList.add("d-none"); });
        currentPassage().classList.remove("d-none");
        document.getElementById("rsWords").textContent = wordCount();
        document.getElementById("rsWpm").textContent = "-"; document.getElementById("rsTime").textContent = "-"; document.getElementById("rsLevel").textContent = "";
        startAt = null; document.getElementById("rsDone").disabled = true;
        document.getElementById("rsStatus").textContent = "Press Start, then read the passage at your normal honest pace.";
    });
    document.getElementById("rsStart").addEventListener("click", function () {
        startAt = performance.now();
        document.getElementById("rsDone").disabled = false;
        document.getElementById("rsWords").textContent = wordCount();
        document.getElementById("rsStatus").textContent = "Timer running. Read the whole passage now...";
    });
    document.getElementById("rsDone").addEventListener("click", function () {
        if (!startAt) { showMsg("Press Start Reading first.", false); return; }
        var secs = (performance.now() - startAt) / 1000, words = wordCount();
        var wpm = Math.round(words / (secs / 60));
        document.getElementById("rsTime").textContent = secs.toFixed(1);
        document.getElementById("rsWpm").textContent = wpm;
        var level = wpm < 120 ? "Below 120 WPM: a careful, slow pace. Great for study reading." : (wpm < 200 ? "120 to 200 WPM: a relaxed everyday reading pace." : (wpm < 300 ? "200 to 300 WPM: the typical adult range with good comprehension." : (wpm < 450 ? "300 to 450 WPM: fast reading. Check you truly took in the details." : "Above 450 WPM: skimming territory for most people. Comprehension usually drops here.")));
        document.getElementById("rsLevel").textContent = level;
        document.getElementById("rsStatus").textContent = "Done. Press Reset or switch passage to try again.";
        startAt = null; document.getElementById("rsDone").disabled = true;
        showMsg("You read " + words + " words in " + secs.toFixed(1) + " seconds.");
    });
    document.getElementById("rsReset").addEventListener("click", function () {
        startAt = null; document.getElementById("rsDone").disabled = true;
        document.getElementById("rsWpm").textContent = "-"; document.getElementById("rsTime").textContent = "-"; document.getElementById("rsLevel").textContent = "";
        document.getElementById("rsStatus").textContent = "Press Start, then read the passage at your normal honest pace.";
    });
    document.getElementById("rsWords").textContent = wordCount();

})();
</script>
@endsection
