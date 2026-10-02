@extends('layouts.app')

@section('title', 'Credit Card Interest Calculator — Free Online Tool')
@section('meta_description', 'Calculate daily and monthly interest charged on a credit card balance from APR and average daily balance')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Credit Card Interest Calculator</h1>
            <p class="lead small text-muted">See how much daily and monthly interest your credit card balance costs — estimate the finance charge from APR and average daily balance.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="cciBal" class="form-label">Average Daily Balance (Rs)</label><input type="number" class="form-control" id="cciBal" value="100000" step="any"></div>
<div class="col-md-4"><label for="cciApr" class="form-label">Card APR (%)</label><input type="number" class="form-control" id="cciApr" value="42" step="any"></div>
<div class="col-md-4"><label for="cciDays" class="form-label">Billing Cycle Days</label><input type="number" class="form-control" id="cciDays" value="30" step="1"></div>
<div class="col-md-4"><label for="cciMonths" class="form-label">Months Balance Stays Unpaid</label><input type="number" class="form-control" id="cciMonths" value="6" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Per Day</div><div class="fs-5 fw-bold" id="cciDaily">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Per Billing Cycle</div><div class="fs-5 fw-bold" id="cciCycle">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Per Month (average)</div><div class="fs-5 fw-bold" id="cciMonthly">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Balance After Unpaid Months (interest compounded)</div><div class="fs-5 fw-bold" id="cciProjected">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the average daily balance from your statement.</li>
                        <li>Enter the card APR (in Pakistan usually 35–45%).</li>
                        <li>Enter the billing cycle days and how many months the balance will stay unpaid.</li>
                        <li>See the daily, cycle, and monthly interest.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Banks use the average daily balance method, and you only get a grace period when the previous balance is paid in full. Late fees and cash advance charges are not included.</p>
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
    var bal=v("cciBal"), apr=v("cciApr"), days=Math.round(v("cciDays")), months=Math.round(v("cciMonths"));
    if(bal<=0||apr<0){msg("Enter the balance and APR.");return;}
    msg(""); var daily=bal*apr/100/365; var cycle=daily*days; var monthly=bal*apr/100/12;
    var proj=bal*Math.pow(1+apr/100/12,months);
    out("cciDaily",fmt2(daily));out("cciCycle",fmt(cycle));out("cciMonthly",fmt(monthly));out("cciProjected",fmt(proj));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
