@extends('layouts.app')

@section('title', 'Diminishing Musharakah Calculator — Free Online Tool')
@section('meta_description', 'Find the full payment plan for Islamic home financing with monthly rent and unit purchase.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Diminishing Musharakah Calculator</h1>
            <p class="lead small text-muted">Enter the house price, your share and the bank share — every month the bank share keeps going down, and rent is charged only on the remaining bank share. This is the basic structure of diminishing musharakah.</p>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="dmPrice">Property price (Rs)</label><input type="number" class="form-control dm-in" id="dmPrice" value="15000000" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="dmOwnPct">Your share (%)</label><input type="number" class="form-control dm-in" id="dmOwnPct" value="20" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="dmYears">Tenure (years)</label><input type="number" class="form-control dm-in" id="dmYears" value="15" step="any"></div>
                <div class="col-md-4"><label class="form-label" for="dmRentRate">Annual rent rate on bank share (%)</label><input type="number" class="form-control dm-in" id="dmRentRate" value="18" step="any"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="dmMsg"></div>
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-3"><div class="text-muted small">Bank share (start)</div><div class="fs-5 fw-bold" id="dmBank">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Monthly unit purchase</div><div class="fs-5 fw-bold" id="dmUnit">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">First monthly payment</div><div class="fs-5 fw-bold" id="dmFirst">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Last monthly payment</div><div class="fs-5 fw-bold" id="dmLast">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Total rent over tenure</div><div class="fs-5 fw-bold" id="dmTotalRent">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Total paid to bank</div><div class="fs-5 fw-bold" id="dmTotal">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Your upfront share</div><div class="fs-5 fw-bold" id="dmOwn">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Months</div><div class="fs-5 fw-bold" id="dmMonths">—</div></div>
                </div>
            </div>
            <div class="table-responsive mt-3"><table class="table table-sm"><thead><tr><th>Year</th><th>Bank share at year start</th><th>Rent that year</th><th>Units bought that year</th></tr></thead><tbody id="dmTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the property price and your share (percent).</li><li>Enter the tenure and the annual rent rate on the bank share — this rate changes with the bank and the plan.</li><li>See the first and last monthly payment, total rent and the year-by-year schedule.</li></ol>
            <p class="small text-muted mb-0">Note: This is a worksheet model: the real Islamic bank rent rate, takaful, processing fees and early purchase rules may be different. Rates change — verify with the official source before relying on this. Before signing any contract, be sure to check the bank schedule and the mufti-approved structure.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    function calc() {
        var price = num("dmPrice"), ownPct = num("dmOwnPct"), years = num("dmYears"), rate = num("dmRentRate"), msg = el("dmMsg");
        if (price <= 0 || ownPct < 0 || ownPct >= 100 || years <= 0) { msg.textContent = "Enter valid values: price must be positive, your share between 0 and 100, and tenure at least 1 year."; msg.classList.remove("d-none"); return; }
        msg.classList.add("d-none");
        var months = Math.round(years * 12), bankStart = price * (1 - ownPct / 100), own = price * ownPct / 100;
        var unit = bankStart / months, totalRent = 0, bank = bankStart, first = 0, last = 0, rows = "", yearRent = 0;
        for (var i = 1; i <= months; i++) {
            var rent = bank * (rate / 100) / 12;
            totalRent += rent; yearRent += rent;
            if (i === 1) first = unit + rent;
            if (i === months) last = unit + rent;
            bank -= unit; if (bank < 0.01) bank = 0;
            if (i % 12 === 0 || i === months) { var yr = Math.ceil(i / 12); rows += "<tr><td>" + yr + "</td><td>" + rs(bankStart - unit * Math.min(months, (yr - 1) * 12)) + "</td><td>" + rs(yearRent) + "</td><td>" + rs(unit * (i - (yr - 1) * 12)) + "</td></tr>"; yearRent = 0; }
        }
        el("dmBank").textContent = rs(bankStart); el("dmUnit").textContent = rs(unit);
        el("dmFirst").textContent = rs(first); el("dmLast").textContent = rs(last);
        el("dmTotalRent").textContent = rs(totalRent); el("dmTotal").textContent = rs(bankStart + totalRent);
        el("dmOwn").textContent = rs(own); el("dmMonths").textContent = months;
        el("dmTable").innerHTML = rows;
    }
    document.querySelectorAll(".dm-in").forEach(function (f) { f.addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
