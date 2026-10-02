@extends('layouts.app')

@section('title', 'Negative Marking Score Calculator — Free Online Tool')
@section('meta_description', 'Find your final score from correct, wrong and skipped questions in an entry test.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Negative Marking Score Calculator</h1>
            <p class="lead small text-muted">Enter the total questions and the count of correct and wrong answers — get the final score after negative marking, with percent and grade estimate in steps.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">                    <div class="col-md-4"><label class="form-label" for="nmTot">Total questions</label><input type="number" class="form-control inp" id="nmTot" placeholder="e.g. 200" step="1"></div>                    <div class="col-md-4"><label class="form-label" for="nmCor">Correct answers</label><input type="number" class="form-control inp" id="nmCor" placeholder="e.g. 150" step="1"></div>                    <div class="col-md-4"><label class="form-label" for="nmWr">Wrong answers</label><input type="number" class="form-control inp" id="nmWr" placeholder="e.g. 30" step="1"></div>                    <div class="col-md-4"><label class="form-label" for="nmPer">Marks per correct answer</label><input type="number" class="form-control inp" id="nmPer" placeholder="4" step="any" value="4"></div>                    <div class="col-md-4"><label class="form-label" for="nmNeg">Marks cut per wrong answer</label><input type="number" class="form-control inp" id="nmNeg" placeholder="1" step="any" value="1"></div></div>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the total, and the correct and wrong counts.</li><li>Set marks per question and penalty as per your test (they are editable).</li><li>Skipped questions are calculated on their own — no penalty on them.</li></ol>
                    <p class="small text-muted mb-0">Note: Default values (4 marks per question, 1 mark penalty) are common in many entry tests, but every test has its own pattern — MDCAT, ECAT etc. have their own pattern. Rates change — verify with the official source before relying on this.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function calc() {
        var tot = intv("nmTot"), cor = intv("nmCor"), wr = intv("nmWr"), per = num("nmPer"), neg = num("nmNeg");
        if ([tot, cor, wr].some(function (v) { return v === null; }) || per === null || neg === null) { err("Fill all fields correctly."); return; }
        if (tot <= 0 || cor < 0 || wr < 0 || per <= 0 || neg < 0) { err("Values cannot be negative and total must be more than 0."); return; }
        if (cor + wr > tot) { err("Correct + wrong cannot be more than total questions."); return; }
        var skipped = tot - cor - wr; var gained = cor * per, lost = wr * neg, score = gained - lost, max = tot * per;
        show("<div class=\"alert alert-success mb-0 fs-5\"><strong>Final Score = " + fmt(score, 2) + " / " + fmt(max, 0) + " (" + fmt(score / max * 100, 2) + "%)</strong></div>");
        showSteps(["Correct answers: " + cor + " x " + fmt(per, 2) + " = +" + fmt(gained, 2) + " marks", "Wrong answers: " + wr + " x " + fmt(neg, 2) + " = −" + fmt(lost, 2) + " marks (negative marking)", "Skipped questions: " + skipped + " (no marks, no penalty)", "Final = " + fmt(gained, 2) + " − " + fmt(lost, 2) + " = " + fmt(score, 2), "Without negative marking the score would be " + fmt(gained, 2) + " — wrong answers cost " + fmt(lost, 2) + " marks"]);
    }
    bind(calc); calc();

})();
</script>
@endsection
