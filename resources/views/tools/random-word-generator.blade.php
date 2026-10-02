@extends('layouts.app')
@section('title', 'Random Word Generator — Free Online Tool')
@section('meta_description', 'Generate random English words for games passwords and writing prompts')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Random Word Generator</h1>
            <p class="lead small text-muted">Generate random English words for party games, classroom activities, password ideas and writing prompts.</p>

                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="rwCount">How many words</label><input type="number" id="rwCount" class="form-control" value="5" min="1" max="50"></div>
                        <div class="col-md-3"><label class="form-label" for="rwMin">Min length</label><input type="number" id="rwMin" class="form-control" value="3" min="1" max="20"></div>
                        <div class="col-md-3"><label class="form-label" for="rwMax">Max length</label><input type="number" id="rwMax" class="form-control" value="12" min="1" max="20"></div>
                        <div class="col-md-3 d-flex align-items-end"><button type="button" id="rwGo" class="btn btn-primary w-100">Generate Words</button></div>
                    </div>
                    <div class="form-check mt-3"><input type="checkbox" id="rwUnique" class="form-check-input" checked><label class="form-check-label" for="rwUnique">No repeated words</label></div>
                    <div id="rwOut" class="mt-3 d-flex flex-wrap gap-2"></div>
                    <label class="form-label mt-3" for="rwText">Words as plain text</label>
                    <textarea id="rwText" class="form-control" rows="3" readonly></textarea>
                    <button type="button" id="rwCopy" class="btn btn-outline-secondary mt-2">Copy Words</button>

            <div id="rwMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose how many words you want and an optional length range.</li>
                    <li>Click Generate Words for a fresh set.</li>
                    <li>Copy the words for your game, lesson or prompt.</li>
            </ol>
            <p class="small text-muted mb-0">Words come from a built in list of common everyday English words, so every result is a real, spellable word. For a strong password, combine several words with numbers and symbols using a password generator.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("rwMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    var WORDS = ["apple", "river", "mountain", "garden", "window", "bridge", "silver", "orange", "planet", "harbor", "meadow", "candle", "forest", "breeze", "copper", "desert", "engine", "falcon", "glacier", "hammer", "island", "jungle", "kitten", "ladder", "marble", "needle", "ocean", "palace", "quilt", "rocket", "saddle", "temple", "umbrella", "valley", "walnut", "yellow", "zebra", "anchor", "basket", "castle", "dragon", "ember", "feather", "guitar", "helmet", "ivory", "jacket", "kettle", "lemon", "mirror", "napkin", "orchard", "pencil", "quiver", "ribbon", "sailor", "tiger", "unity", "velvet", "willow", "yonder", "zenith", "bamboo", "cactus", "dolphin", "eagle", "fjord", "granite", "horizon", "ink", "jasmine", "koala", "lantern", "magnet", "nomad", "opal", "pyramid", "quartz", "rainbow", "sapphire", "tundra", "urban", "violet", "whisper", "xenon", "yonder", "zephyr", "atlas", "birch", "comet", "delta", "elm", "flint", "grove", "heron", "iris", "jade", "karma", "lotus", "mango", "north", "onyx", "pearl", "quest", "ridge", "solar", "topaz", "urban", "vista", "wander", "yonder", "bloom", "cedar", "drift", "elm", "fern", "grain", "haven", "inlet", "jewel", "knoll", "lunar", "misty", "noble", "oasis", "pine", "quill", "rose", "stone", "tide", "umber", "vale", "wave", "yarn", "zinc", "cloud", "dawn", "echo", "flame", "glow", "haze", "isle", "jolt", "keel", "lake", "moon", "night", "opal", "peak", "rain", "snow", "star", "tree", "vine", "wind", "wolf", "yard", "zest"];
    document.getElementById("rwGo").addEventListener("click", function () {
        var n = Math.max(1, Math.min(50, parseInt(document.getElementById("rwCount").value, 10) || 5));
        var mn = Math.max(1, parseInt(document.getElementById("rwMin").value, 10) || 1);
        var mx = Math.max(mn, parseInt(document.getElementById("rwMax").value, 10) || 20);
        var pool = WORDS.filter(function (w) { return w.length >= mn && w.length <= mx; });
        if (!pool.length) { showMsg("No words match that length range. Widen the range.", false); return; }
        var unique = document.getElementById("rwUnique").checked, picked = [], guard = 0;
        while (picked.length < n && guard < 500) {
            guard++;
            var w = pool[Math.floor(Math.random() * pool.length)];
            if (unique && picked.indexOf(w) !== -1) continue;
            picked.push(w);
        }
        var out = document.getElementById("rwOut"); out.innerHTML = "";
        picked.forEach(function (w) { var sp = document.createElement("span"); sp.className = "badge bg-primary fs-6"; sp.textContent = w; out.appendChild(sp); });
        document.getElementById("rwText").value = picked.join(", ");
        showMsg("Generated " + picked.length + " word(s) from a list of " + pool.length + " matching words.");
    });
    document.getElementById("rwCopy").addEventListener("click", function () { copyText(document.getElementById("rwText").value, "Words copied."); });
    document.getElementById("rwGo").click();

})();
</script>
@endsection
