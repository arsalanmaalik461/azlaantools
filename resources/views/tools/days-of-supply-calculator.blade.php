@extends('layouts.app')

@section('title', 'Days of Supply Calculator — Free Online Tool')
@section('meta_description', 'Calculate how many days a stock of food, medicine or feed will last by daily use.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Days of Supply Calculator</h1>
                    <p class="lead small text-muted">Enter how much stock you have and how much is used per day — ration, medicine, chicken feed, anything — and see the days it will last and the date it runs out.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="spStock">Stock on hand</label><input type="number" class="form-control" id="spStock" value="50" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="spUnit">Unit (label only)</label><input type="text" class="form-control" id="spUnit" value="kg"></div>
                        <div class="col-md-4"><label class="form-label" for="spUse">Daily use (same unit)</label><input type="number" class="form-control" id="spUse" value="2" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="spBuffer">Safety buffer days</label><input type="number" class="form-control" id="spBuffer" value="3" min="0" step="1"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="spOutRes">Enter stock and daily use to see how long it lasts.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the stock you have and the unit name (kg, litres, tablets).</li>
                        <li>Enter the average daily use in the same unit.</li>
                        <li>Read the days of supply, the run out date, and the reorder date after your safety buffer.</li>
                    </ol>
                    <p class="small text-muted mb-0">Days of supply is simply stock divided by daily use, assuming steady consumption. The reorder date subtracts your safety buffer so you restock before the stock actually finishes.</p>
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
    function el(id) { return document.getElementById(id); }
    function parseD(v) { if (!v) { return null; } var p = v.split("-"); var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10)); return isNaN(d.getTime()) ? null : d; }
    function todayStr() { var d = new Date(); function pad(n) { return (n < 10 ? "0" : "") + n; } return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    function fmtD(d) { return d.toLocaleDateString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric" }); }
    function calc() {
        var stock = parseFloat(el("spStock").value), use = parseFloat(el("spUse").value), buffer = parseInt(el("spBuffer").value, 10);
        var unit = el("spUnit").value || "units";
        var out = el("spOutRes");
        if (isNaN(stock) || stock < 0 || isNaN(use) || use <= 0) { out.textContent = "Please enter stock and a daily use greater than zero."; return; }
        if (isNaN(buffer) || buffer < 0) { buffer = 0; }
        var days = stock / use;
        var today = parseD(todayStr());
        var runOut = new Date(today.getTime()); runOut.setDate(runOut.getDate() + Math.floor(days));
        var reorder = new Date(runOut.getTime()); reorder.setDate(reorder.getDate() - buffer);
        out.innerHTML = "<strong>" + stock + " " + unit + " will last about " + days.toFixed(1) + " days</strong> at " + use + " " + unit + " per day.<br>Expected run out date: <strong>" + fmtD(runOut) + "</strong>. Reorder by: <strong>" + fmtD(reorder) + "</strong> (with a " + buffer + " day safety buffer).";
    }
    ["spStock", "spUnit", "spUse", "spBuffer"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
