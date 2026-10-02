@extends('layouts.app')

@section('title', 'Auto Loan Calculator — Free Online Tool')
@section('meta_description', 'Calculate car loan monthly payment, total interest and total cost from price, down payment and term')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Auto Loan Calculator</h1>
            <p class="lead small text-muted">Enter the car price, down payment and term — see the monthly installment, total markup and the total cost of the car right away.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="auPrice" class="form-label">Car Price (Rs)</label><input type="number" class="form-control" id="auPrice" value="3000000" step="any"></div>
<div class="col-md-4"><label for="auDown" class="form-label">Down Payment (Rs)</label><input type="number" class="form-control" id="auDown" value="600000" step="any"></div>
<div class="col-md-4"><label for="auTrade" class="form-label">Trade-in Value (Rs)</label><input type="number" class="form-control" id="auTrade" value="0" step="any"></div>
<div class="col-md-4"><label for="auTax" class="form-label">Registration / Tax Added (%)</label><input type="number" class="form-control" id="auTax" value="0" step="any"></div>
<div class="col-md-4"><label for="auRate" class="form-label">Annual Markup Rate (%)</label><input type="number" class="form-control" id="auRate" value="18" step="any"></div>
<div class="col-md-4"><label for="auMonths" class="form-label">Term (months)</label><input type="number" class="form-control" id="auMonths" value="60" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Amount Financed</div><div class="fs-5 fw-bold" id="auLoan">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Installment</div><div class="fs-5 fw-bold" id="auPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Interest / Markup</div><div class="fs-5 fw-bold" id="auInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Cost of Car (all payments + upfront)</div><div class="fs-5 fw-bold" id="auTotal">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the car price and the registration or tax percentage.</li>
                        <li>Enter the down payment and the trade-in value of your old car.</li>
                        <li>Enter the bank markup rate and the term in months.</li>
                        <li>See the installment, total markup and total cost.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Estimate only. Bank auto finance in Pakistan may add processing fees, insurance and tracker charges, and rates are often KIBOR-linked and can change during the term.</p>
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
    var price=v("auPrice"), down=v("auDown"), trade=v("auTrade"), tax=v("auTax"), rate=v("auRate"), n=Math.round(v("auMonths"));
    var gross=price*(1+tax/100); var loan=gross-down-trade;
    if(price<=0||n<=0){msg("Enter the car price and a valid term.");return;}
    if(loan<0){msg("Down payment plus trade-in exceeds the car price.");loan=0;}
    msg(""); var pay=pmt(loan,rate,n); var totPay=pay*n;
    out("auLoan",fmt(loan));out("auPay",fmt(pay));out("auInt",fmt(totPay-loan));out("auTotal",fmt(down+trade+totPay));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
