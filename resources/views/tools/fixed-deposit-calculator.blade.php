@extends('layouts.app')

@section('title', 'Fixed Deposit Calculator — Free Online Tool')
@section('meta_description', 'Calculate maturity value and profit on a term deposit with monthly, quarterly or yearly profit payout options')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Fixed Deposit Calculator</h1>
            <p class="lead small text-muted">Calculate profit on term deposits and saving certificates — monthly, quarterly or yearly payout, or compounding at maturity; see every option's total.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="fdP" class="form-label">Deposit Amount (Rs)</label><input type="number" class="form-control" id="fdP" value="500000" step="any"></div>
<div class="col-md-4"><label for="fdRate" class="form-label">Annual Profit Rate (%)</label><input type="number" class="form-control" id="fdRate" value="11" step="any"></div>
<div class="col-md-4"><label for="fdYears" class="form-label">Term (years)</label><input type="number" class="form-control" id="fdYears" value="3" step="any"></div>
<div class="col-md-4"><label for="fdMode" class="form-label">Profit Option</label><select class="form-select" id="fdMode"><option value="12">Monthly payout</option><option value="4" selected>Quarterly payout</option><option value="1">Yearly payout</option><option value="compound">Reinvest till maturity (quarterly compounding)</option></select></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Profit Per Payout</div><div class="fs-5 fw-bold" id="fdPer">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Profit Over Term</div><div class="fs-5 fw-bold" id="fdTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Value (principal + all profit)</div><div class="fs-5 fw-bold" id="fdMaturity">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the deposit amount and the bank's annual profit rate.</li>
                        <li>Enter the term in years.</li>
                        <li>Select a profit option — with payout options you get profit every period and the principal stays the same; with reinvest, the profit compounds.</li>
                        <li>See the per-payout profit, total profit and total value.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Withholding tax and zakat may apply on payout options and are not included in this calculation. Deposit rates change with banks and the State Bank policy rate. Rates change — verify with the official source before relying on this.</p>
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
    var P=v("fdP"), rate=v("fdRate"), years=v("fdYears"), mode=sv("fdMode");
    if(P<=0||years<=0){msg("Enter the deposit amount and term.");return;}
    msg("");
    if(mode==="compound"){var maturity=P*Math.pow(1+rate/100/4,4*years);out("fdPer","\u2014 (reinvested)");out("fdTotal",fmt(maturity-P));out("fdMaturity",fmt(maturity));}
    else{var f=parseFloat(mode);var per=P*rate/100/f;var total=per*f*years;out("fdPer",fmt(per));out("fdTotal",fmt(total));out("fdMaturity",fmt(P+total));}
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
