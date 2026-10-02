@extends('layouts.app')

@section('title', 'Dividend Calculator — Free Online Tool')
@section('meta_description', 'Estimate dividend income from number of shares, dividend per share and payment frequency over a year')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Dividend Calculator</h1>
            <p class="lead small text-muted">Estimate your yearly dividend income from the number of shares and dividend per share — no live price feed needed.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="dvShares" class="form-label">Number of Shares</label><input type="number" class="form-control" id="dvShares" value="1000" step="1"></div>
<div class="col-md-4"><label for="dvDps" class="form-label">Dividend Per Share (Rs, per payment)</label><input type="number" class="form-control" id="dvDps" value="5" step="any"></div>
<div class="col-md-4"><label for="dvFreq" class="form-label">Payments Per Year</label><input type="number" class="form-control" id="dvFreq" value="2" step="1"></div>
<div class="col-md-4"><label for="dvYears" class="form-label">Projection Years</label><input type="number" class="form-control" id="dvYears" value="5" step="1"></div>
<div class="col-md-4"><label for="dvGrowth" class="form-label">Annual Dividend Growth (%)</label><input type="number" class="form-control" id="dvGrowth" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Dividend Income Per Year (year 1)</div><div class="fs-5 fw-bold" id="dvAnnual">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Average Monthly Income</div><div class="fs-5 fw-bold" id="dvMonthly">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Over Projection Period</div><div class="fs-5 fw-bold" id="dvTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Income Per Payment</div><div class="fs-5 fw-bold" id="dvPerPayment">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the number of shares.</li>
                        <li>Enter the announced dividend per share and the number of payments per year.</li>
                        <li>Enter the projection years and expected dividend growth.</li>
                        <li>See yearly, monthly average, and total dividend income.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Dividends depend on company profit and board decisions — past dividends do not guarantee future payouts. Withholding tax on dividends is not included in this estimate.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

function v(id){var e=document.getElementById(id);var n=parseFloat(e?e.value:"");return isNaN(n)?0:n;}
function sv(id){var e=document.getElementById(id);return e?e.value:"";}
function fmt(n){if(!isFinite(n))return "\u2014";return "Rs "+Math.round(n).toLocaleString("en-PK");}
function fmt2(n){if(!isFinite(n))return "\u2014";return "Rs "+Number(n).toLocaleString("en-PK",{minimumFractionDigits:2,maximumFractionDigits:2});}
function num(n,d){if(!isFinite(n))return "\u2014";return Number(n).toLocaleString("en-PK",{minimumFractionDigits:d,maximumFractionDigits:d});}
function pct(n){return num(n,2)+"%";}
function out(id,txt){var e=document.getElementById(id);if(e)e.textContent=txt;}
function html(id,txt){var e=document.getElementById(id);if(e)e.innerHTML=txt;}
function msg(t){var e=document.getElementById("msg");if(e){e.textContent=t;e.className=t?"alert alert-warning mt-3":"d-none";}}
function pmt(P,annual,n){if(P<=0||n<=0)return 0;var r=annual/100/12;if(r===0)return P/n;var f=Math.pow(1+r,n);return P*r*f/(f-1);}
function pvAnnuity(pay,annual,n){if(pay<=0||n<=0)return 0;var r=annual/100/12;if(r===0)return pay*n;return pay*(1-Math.pow(1+r,-n))/r;}
function balAfter(P,annual,k,pay){if(P<=0||k<=0)return Math.max(P,0);var r=annual/100/12;if(r===0)return Math.max(P-pay*k,0);var f=Math.pow(1+r,k);return Math.max(P*f-pay*((f-1)/r),0);}


function calc(){
    var shares=v("dvShares"), dps=v("dvDps"), freq=v("dvFreq"), years=Math.round(v("dvYears")), g=v("dvGrowth")/100;
    if(shares<=0||dps<0||freq<=0||years<=0){msg("Enter shares, dividend per share, frequency and years.");return;}
    msg(""); var annual=shares*dps*freq; var total=0;
    for(var y=0;y<years;y++){total+=annual*Math.pow(1+g,y);}
    out("dvAnnual",fmt(annual));out("dvMonthly",fmt(annual/12));out("dvTotal",fmt(total));out("dvPerPayment",fmt(shares*dps));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
