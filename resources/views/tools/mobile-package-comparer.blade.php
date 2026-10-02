@extends('layouts.app')

@section('title', 'Mobile Package Comparer — Free Online Tool')
@section('meta_description', 'Enter price and validity for two or three packages, and compare daily and per GB cost.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Mobile Package Comparer</h1>
                    <p class="lead small text-muted">For Jazz, Zong, Telenor or Ufone — enter the price, validity and data of any two or three packages and see which one is cheapest per day and per GB.</p>
                    <div class="row g-3 mb-2">
                        <div class="col-md-3"><label class="form-label" for="pkName1">Package 1 name</label><input type="text" class="form-control" id="pkName1" value="Package 1"></div>
                        <div class="col-md-3"><label class="form-label" for="pkPrice1">Price (Rs)</label><input type="number" class="form-control" id="pkPrice1" value="500" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkDays1">Validity (days)</label><input type="number" class="form-control" id="pkDays1" value="30" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkGb1">Data (GB)</label><input type="number" class="form-control" id="pkGb1" value="20" min="0" step="any"></div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-3"><label class="form-label" for="pkName2">Package 2 name</label><input type="text" class="form-control" id="pkName2" value="Package 2"></div>
                        <div class="col-md-3"><label class="form-label" for="pkPrice2">Price (Rs)</label><input type="number" class="form-control" id="pkPrice2" value="750" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkDays2">Validity (days)</label><input type="number" class="form-control" id="pkDays2" value="30" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkGb2">Data (GB)</label><input type="number" class="form-control" id="pkGb2" value="40" min="0" step="any"></div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-3"><label class="form-label" for="pkName3">Package 3 name</label><input type="text" class="form-control" id="pkName3" value="Package 3"></div>
                        <div class="col-md-3"><label class="form-label" for="pkPrice3">Price (Rs)</label><input type="number" class="form-control" id="pkPrice3" value="999" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkDays3">Validity (days)</label><input type="number" class="form-control" id="pkDays3" value="30" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="pkGb3">Data (GB)</label><input type="number" class="form-control" id="pkGb3" value="100" min="0" step="any"></div>
                    </div>
                    <div class="table-responsive mt-2"><table class="table table-striped mb-0"><thead><tr><th>Package</th><th>Cost per day</th><th>Cost per GB</th><th>GB per day</th></tr></thead><tbody id="pkBody"></tbody></table></div>
                    <div class="alert alert-info mt-3 mb-0" id="pkMsg">Enter packages to compare them.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the name, price (Rs), validity (days) and data (GB) of each package.</li>
                        <li>Compare each package in the table by its per day cost, per GB cost and daily GB.</li>
                        <li>The cheapest per day and per GB values will be highlighted below.</li>
                    </ol>
                    <p class="small text-muted mb-0">This tool only calculates from the values you enter — no live package data is used. Minutes, SMS and social data (separate GB for WhatsApp, Facebook etc.) are not included; the comparison is only on total data GB.</p>
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
    function calc() {
        var body = el("pkBody"), msg = el("pkMsg");
        var html = "", bestDay = null, bestGb = null, bestDayName = "", bestGbName = "";
        for (var i = 1; i <= 3; i++) {
            var name = el("pkName" + i).value || ("Package " + i);
            var price = parseFloat(el("pkPrice" + i).value), days = parseFloat(el("pkDays" + i).value), gb = parseFloat(el("pkGb" + i).value);
            if (isNaN(price) || price <= 0 || isNaN(days) || days <= 0 || isNaN(gb) || gb <= 0) { html += "<tr><td>" + name + "</td><td colspan=\"3\">Values incomplete</td></tr>"; continue; }
            var perDay = price / days, perGb = price / gb, gbDay = gb / days;
            if (bestDay === null || perDay < bestDay) { bestDay = perDay; bestDayName = name; }
            if (bestGb === null || perGb < bestGb) { bestGb = perGb; bestGbName = name; }
            html += "<tr><td>" + name + "</td><td>Rs " + perDay.toFixed(2) + "</td><td>Rs " + perGb.toFixed(2) + "</td><td>" + gbDay.toFixed(2) + " GB</td></tr>";
        }
        body.innerHTML = html;
        msg.innerHTML = bestDay !== null ? "<strong>Cheapest per day:</strong> " + bestDayName + " (Rs " + bestDay.toFixed(2) + " per day) &nbsp; <strong>Cheapest data:</strong> " + bestGbName + " (Rs " + bestGb.toFixed(2) + " per GB)" : "Please enter complete values for at least one package.";
    }
    for (var i = 1; i <= 3; i++) { ["pkName", "pkPrice", "pkDays", "pkGb"].forEach(function (p) { el(p + i).addEventListener("input", calc); }); }
    calc();
})();
</script>
@endsection
