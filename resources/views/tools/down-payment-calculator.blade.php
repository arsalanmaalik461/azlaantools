@extends('layouts.app')

@section('title', 'Down Payment Calculator — Free Online Tool')
@section('meta_description', 'Calculate down payment amount for a target percentage and the monthly saving needed to reach it by a set date')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Down Payment Calculator</h1>
            <p class="lead small text-muted">How much down payment you need for a house or car, and how long it will take with your monthly saving — a complete saving plan.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="dpPrice" class="form-label">Target Property / Car Price (Rs)</label><input type="number" class="form-control" id="dpPrice" value="10000000" step="any"></div>
<div class="col-md-4"><label for="dpPct" class="form-label">Down Payment (%)</label><input type="number" class="form-control" id="dpPct" value="20" step="any"></div>
<div class="col-md-4"><label for="dpSaved" class="form-label">Already Saved (Rs)</label><input type="number" class="form-control" id="dpSaved" value="500000" step="any"></div>
<div class="col-md-4"><label for="dpMonthly" class="form-label">Monthly Saving (Rs)</label><input type="number" class="form-control" id="dpMonthly" value="50000" step="any"></div>
<div class="col-md-4"><label for="dpRate" class="form-label">Expected Return on Savings (%)</label><input type="number" class="form-control" id="dpRate" value="10" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Down Payment Needed</div><div class="fs-5 fw-bold" id="dpTarget">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Still Needed Today</div><div class="fs-5 fw-bold" id="dpGap">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Months to Reach Target</div><div class="fs-5 fw-bold" id="dpMonths">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Monthly Deposits Needed</div><div class="fs-5 fw-bold" id="dpDeposits">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the target price and down payment percentage (usually 20–30% for a house).</li>
                        <li>Enter your already saved amount and monthly saving.</li>
                        <li>Enter the expected return rate on savings.</li>
                        <li>See the target, gap, and months to reach it.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The return rate is an assumption. Apart from the down payment, keep a separate buffer for registration, transfer, and bank charges.</p>
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
    var price=v("dpPrice"), pctv=v("dpPct"), saved=v("dpSaved"), monthly=v("dpMonthly"), rate=v("dpRate")/100/12;
    if(price<=0||pctv<=0){msg("Enter the target price and down payment percentage.");return;}
    var target=price*pctv/100; var gap=Math.max(target-saved,0);
    if(gap<=0){msg("You have already saved the full down payment.");out("dpTarget",fmt(target));out("dpGap",fmt(0));out("dpMonths","0");out("dpDeposits",fmt(0));return;}
    if(monthly<=0){msg("Enter a monthly saving amount to see the time needed.");return;}
    msg(""); var bal=saved, months=0;
    while(bal<target&&months<1200){bal=bal*(1+rate)+monthly;months++;}
    out("dpTarget",fmt(target));out("dpGap",fmt(gap));out("dpMonths",months<1200?months+" (about "+num(months/12,1)+" years)":"1200+");out("dpDeposits",fmt(monthly*months));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
