@extends('layouts.app')

@section('title', 'Balloon Loan Calculator — Free Online Tool')
@section('meta_description', 'Calculate small regular payments and the large final balloon payment due at the end of a loan term')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Balloon Loan Calculator</h1>
            <p class="lead small text-muted">A loan with small monthly payments and one large balloon payment at the end — see both amounts together.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="blP" class="form-label">Loan Amount (Rs)</label><input type="number" class="form-control" id="blP" value="2000000" step="any"></div>
<div class="col-md-4"><label for="blRate" class="form-label">Annual Interest Rate (%)</label><input type="number" class="form-control" id="blRate" value="15" step="any"></div>
<div class="col-md-4"><label for="blTerm" class="form-label">Actual Loan Term (months)</label><input type="number" class="form-control" id="blTerm" value="36" step="1"></div>
<div class="col-md-4"><label for="blAmort" class="form-label">Amortization Period Used for Payment (months)</label><input type="number" class="form-control" id="blAmort" value="84" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Payment</div><div class="fs-5 fw-bold" id="blPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Balloon Payment at End</div><div class="fs-5 fw-bold" id="blBalloon">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Paid During Term</div><div class="fs-5 fw-bold" id="blInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Paid (payments + balloon)</div><div class="fs-5 fw-bold" id="blTotal">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the loan amount and annual rate.</li>
                        <li>Enter the actual term — the number of months after which the balloon payment is due.</li>
                        <li>Enter the amortization period used to set the monthly payment (usually longer than the term).</li>
                        <li>See the monthly payment and the final balloon amount.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The monthly payment is calculated as if the loan ran for the full amortization period; the remaining balance at the end of the actual term becomes the balloon payment due in one lump sum.</p>
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
    var P=v("blP"), rate=v("blRate"), term=Math.round(v("blTerm")), amort=Math.round(v("blAmort"));
    if(P<=0||term<=0||amort<=0){msg("Enter the loan amount, term and amortization period.");return;}
    if(term>amort){msg("Balloon term is usually shorter than the amortization period. Results still shown.");}else{msg("");}
    var pay=pmt(P,rate,amort); var balloon=balAfter(P,rate,term,pay); var paid=pay*term;
    out("blPay",fmt(pay));out("blBalloon",fmt(balloon));out("blInt",fmt(paid+balloon-P));out("blTotal",fmt(paid+balloon));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
