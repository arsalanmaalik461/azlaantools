@extends('layouts.app')
@section('title', 'Keyword Density Checker — Free Online Tool')
@section('meta_description', 'Measure keyword and phrase density percentages in any article')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Keyword Density Checker</h1>
            <p class="lead small text-muted">Paste an article to measure how often single words and 2 or 3 word phrases repeat, with density percentages for SEO writing.</p>

                    <label class="form-label fw-semibold" for="kdIn">Your text</label>
                    <textarea id="kdIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="form-check mt-3"><input type="checkbox" id="kdStop" class="form-check-input" checked><label class="form-check-label" for="kdStop">Hide common stop words in the single word list (the, and, of...)</label></div>
                    <button type="button" id="kdGo" class="btn btn-primary btn-lg w-100 mt-3">Analyze Density</button>
                    <p class="mt-3 mb-0">Total words: <strong id="kdTotal">0</strong></p>
                    <div class="row g-3 mt-1">
                        <div class="col-md-4"><h3 class="h6">Top single words</h3><div id="kdOne"></div></div>
                        <div class="col-md-4"><h3 class="h6">Top 2-word phrases</h3><div id="kdTwo"></div></div>
                        <div class="col-md-4"><h3 class="h6">Top 3-word phrases</h3><div id="kdThree"></div></div>
                    </div>

            <div id="kdMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste the full article text.</li>
                    <li>Choose whether to hide common stop words in the single word list.</li>
                    <li>Click analyze to see the top words and phrases with counts and density percentages.</li>
            </ol>
            <p class="small text-muted mb-0">Density is the phrase count multiplied by its word length, divided by total words, times 100. There is no single perfect density; natural writing usually keeps a main phrase near 1 to 2 percent.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("kdMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    var STOP = { the: 1, and: 1, of: 1, to: 1, a: 1, in: 1, is: 1, for: 1, on: 1, with: 1, as: 1, by: 1, an: 1, be: 1, are: 1, or: 1, at: 1, from: 1, it: 1, this: 1, that: 1, you: 1, your: 1, we: 1, our: 1 };
    function renderList(id, map, total, size, hideStop) {
        var entries = Object.keys(map).map(function (k) { return [k, map[k]]; }).filter(function (e) { return !(hideStop && size === 1 && STOP[e[0]]); });
        entries.sort(function (a, b) { return b[1] - a[1] || (a[0] < b[0] ? -1 : 1); });
        entries = entries.slice(0, 12);
        var box = document.getElementById(id); box.innerHTML = "";
        if (!entries.length) { box.innerHTML = "<p class=\"text-muted small\">Not enough text.</p>"; return; }
        var ol = document.createElement("ol"); ol.className = "small ps-3";
        entries.forEach(function (e) {
            var li = document.createElement("li");
            var dens = total ? (e[1] * size / total * 100).toFixed(2) : "0.00";
            li.textContent = e[0] + " - " + e[1] + "x (" + dens + "%)";
            ol.appendChild(li);
        });
        box.appendChild(ol);
    }
    document.getElementById("kdGo").addEventListener("click", function () {
        var words = document.getElementById("kdIn").value.toLowerCase().replace(/[^a-z0-9\s'-]/g, " ").split(/\s+/).filter(Boolean);
        document.getElementById("kdTotal").textContent = words.length.toLocaleString("en-US");
        if (!words.length) { showMsg("Paste some text first.", false); return; }
        function grams(n) { var m = {}; for (var i = 0; i + n <= words.length; i++) { var k = words.slice(i, i + n).join(" "); m[k] = (m[k] || 0) + 1; } return m; }
        var hide = document.getElementById("kdStop").checked;
        renderList("kdOne", grams(1), words.length, 1, hide);
        renderList("kdTwo", grams(2), words.length, 2, false);
        renderList("kdThree", grams(3), words.length, 3, false);
        showMsg("Analyzed " + words.length + " words.");
    });

})();
</script>
@endsection
