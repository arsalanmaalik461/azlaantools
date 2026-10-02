@extends('layouts.app')
@section('title', 'Letter Frequency Counter — Free Online Tool')
@section('meta_description', 'Count how often each letter appears in a text passage')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Letter Frequency Counter</h1>
            <p class="lead small text-muted">Paste any text to count how often every letter appears, with percentages, useful for cipher solving and language study.</p>

                    <label class="form-label fw-semibold" for="lfIn">Your text</label>
                    <textarea id="lfIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="form-check mt-3"><input type="checkbox" id="lfCase" class="form-check-input"><label class="form-check-label" for="lfCase">Treat upper and lower case separately</label></div>
                    <button type="button" id="lfGo" class="btn btn-primary btn-lg w-100 mt-3">Count Letter Frequency</button>
                    <p class="mt-3 mb-0">Total letters: <strong id="lfTotal">0</strong> &nbsp;·&nbsp; Other characters: <strong id="lfOther">0</strong></p>
                    <div class="table-responsive mt-2"><table class="table table-sm align-middle"><thead><tr><th>Letter</th><th>Count</th><th>Percent</th><th style="width:40%">Share</th></tr></thead><tbody id="lfBody"><tr><td colspan="4" class="text-muted">No results yet.</td></tr></tbody></table></div>

            <div id="lfMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste your text passage.</li>
                    <li>Choose whether case matters, then click Count.</li>
                    <li>Read the table sorted from most to least frequent letter.</li>
            </ol>
            <p class="small text-muted mb-0">In typical English prose, E is usually the most frequent letter at about 12 percent, followed by T and A. Big differences can hint that a text is encoded.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("lfMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    document.getElementById("lfGo").addEventListener("click", function () {
        var text = document.getElementById("lfIn").value, separate = document.getElementById("lfCase").checked;
        var counts = {}, total = 0, other = 0;
        for (var i = 0; i < text.length; i++) {
            var ch = text.charAt(i);
            if (/[a-zA-Z]/.test(ch)) { var key = separate ? ch : ch.toLowerCase(); counts[key] = (counts[key] || 0) + 1; total++; }
            else if (!/\s/.test(ch)) other++;
        }
        document.getElementById("lfTotal").textContent = total.toLocaleString("en-US");
        document.getElementById("lfOther").textContent = other.toLocaleString("en-US");
        var keys = Object.keys(counts).sort(function (a, b) { return counts[b] - counts[a] || (a < b ? -1 : 1); });
        var body = document.getElementById("lfBody"); body.innerHTML = "";
        if (!keys.length) { body.innerHTML = "<tr><td colspan=\"4\" class=\"text-muted\">No letters found in this text.</td></tr>"; showMsg("No letters found.", false); return; }
        keys.forEach(function (k) {
            var pct = total ? counts[k] / total * 100 : 0;
            var tr = document.createElement("tr");
            var td1 = document.createElement("td"); td1.className = "fw-bold font-monospace"; td1.textContent = k;
            var td2 = document.createElement("td"); td2.textContent = counts[k].toLocaleString("en-US");
            var td3 = document.createElement("td"); td3.textContent = pct.toFixed(2) + "%";
            var td4 = document.createElement("td");
            var barWrap = document.createElement("div"); barWrap.className = "progress"; barWrap.style.height = "10px";
            var bar = document.createElement("div"); bar.className = "progress-bar"; bar.style.width = Math.max(1, pct * 4) + "%";
            barWrap.appendChild(bar); td4.appendChild(barWrap);
            tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
            body.appendChild(tr);
        });
        showMsg("Counted " + total + " letters across " + keys.length + " distinct letters.");
    });

})();
</script>
@endsection
