@extends('layouts.app')

@section('title', 'Dividend Yield Calculator — Free Online Tool')
@section('meta_description', 'Calculate dividend yield and yield on cost from annual dividend and the price paid per share')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Dividend Yield Calculator</h1>
            <p class="lead small text-muted">Calculate dividend yield and yield on cost — a useful ratio to compare the real return percentage on old holdings.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="dyDps" class="form-label">Annual Dividend Per Share (Rs)</label><input type="number" class="form-control" id="dyDps" value="15" step="any"></div>
<div class="col-md-4"><label for="dyCost" class="form-label">Price You Paid Per Share (Rs)</label><input type="number" class="form-control" id="dyCost" value="120" step="any"></div>
<div class="col-md-4"><label for="dyCurrent" class="form-label">Current Price Per Share (Rs)</label><input type="number" class="form-control" id="dyCurrent" value="150" step="any"></div>
<div class="col-md-4"><label for="dyShares" class="form-label">Number of Shares</label><input type="number" class="form-control" id="dyShares" value="1000" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Current Dividend Yield</div><div class="fs-5 fw-bold" id="dyCurrentY">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Yield on Cost (your purchase price)</div><div class="fs-5 fw-bold" id="dyYoc">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Your Annual Dividend Income</div><div class="fs-5 fw-bold" id="dyIncome">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Unrealised Gain Per Share</div><div class="fs-5 fw-bold" id="dyGain">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the annual dividend per share.</li>
                        <li>Enter your purchase price and today's price.</li>
                        <li>Current yield is for new buyers; yield on cost shows the return on your own holding.</li>
                        <li>Compare both — a rising yield on cost is a sign of good long-term income investing.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Yield only measures dividend income; price change is separate. A dividend policy can change at any time.</p>
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
    var dps=v("dyDps"), cost=v("dyCost"), cur=v("dyCurrent"), shares=v("dyShares");
    if(cost<=0||cur<=0){msg("Enter the price paid and the current price per share.");return;}
    msg("");
    out("dyCurrentY",pct(dps/cur*100));out("dyYoc",pct(dps/cost*100));out("dyIncome",fmt(dps*shares));out("dyGain",fmt(cur-cost)+" ("+pct((cur-cost)/cost*100)+")");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
