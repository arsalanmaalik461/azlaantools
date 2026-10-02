@extends('layouts.app')
@section('title', 'Extract Numbers from Text — Free Online Tool')
@section('meta_description', 'Extract all numbers figures and amounts from any text block')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Extract Numbers from Text</h1>
            <p class="lead small text-muted">Paste any text and extract every number, figure and amount, with a sum and average worked out for you.</p>

                    <label class="form-label fw-semibold" for="enIn">Your text</label>
                    <textarea id="enIn" class="form-control" rows="7" placeholder="Paste or type text here"></textarea>

                    <div class="form-check mt-3"><input type="checkbox" id="enDec" class="form-check-input" checked><label class="form-check-label" for="enDec">Include decimal values</label></div>
                    <p class="mt-3 mb-0">Sum: <strong id="enSum">0</strong> &nbsp;·&nbsp; Average: <strong id="enAvg">0</strong></p>

                    <button type="button" id="enGo" class="btn btn-primary btn-lg w-100 mt-3">Extract</button>
                    <label class="form-label mt-3" for="enOut">Results (<span id="enCount">0</span>)</label>
                    <textarea id="enOut" class="form-control" rows="8" readonly></textarea>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="enCopy" class="btn btn-outline-secondary">Copy Results</button><button type="button" id="enDl" class="btn btn-outline-secondary">Download .txt</button></div>

            <div id="enMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Paste the text containing numbers.</li>
                    <li>Choose whether decimals count, then click Extract.</li>
                    <li>Copy the numbers, and check the sum and average shown under the options.</li>
            </ol>
            <p class="small text-muted mb-0">Thousands separators are understood for the sum. Numbers inside words, dates written as words, and fractions like 1/2 are handled as their separate digits.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("enMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

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

    function runExtract() {
        var text = document.getElementById("enIn").value;
        var dec = document.getElementById("enDec").checked;
        var re = dec ? /-?\d[\d,]*(\.\d+)?/g : /-?\d[\d,]*/g;
        var found = text.match(re) || [];
        var vals = found.map(function (n) { return parseFloat(n.replace(/,/g, "")); }).filter(function (v) { return !isNaN(v); });
        var sum = vals.reduce(function (a, b) { return a + b; }, 0);
        document.getElementById("enOut").value = found.join("\n");
        document.getElementById("enCount").textContent = found.length;
        document.getElementById("enSum").textContent = vals.length ? sum.toLocaleString("en-US", { maximumFractionDigits: 2 }) : "0";
        document.getElementById("enAvg").textContent = vals.length ? (sum / vals.length).toLocaleString("en-US", { maximumFractionDigits: 2 }) : "0";
        showMsg(found.length ? "Found " + found.length + " number(s)." : "No numbers found in this text.");
    }

    document.getElementById("enGo").addEventListener("click", runExtract);
    document.getElementById("enCopy").addEventListener("click", function () { copyText(document.getElementById("enOut").value, "Results copied."); });
    document.getElementById("enDl").addEventListener("click", function () { var v = document.getElementById("enOut").value; if (!v) { showMsg("Nothing to download yet.", false); return; } dl(new Blob([v], { type: "text/plain" }), "extracted.txt"); });

})();
</script>
@endsection
