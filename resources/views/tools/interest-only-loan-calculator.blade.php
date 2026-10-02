@extends('layouts.app')

@section('title', 'Interest Only Loan Calculator — Free Online Tool')
@section('meta_description', 'Calculate interest only payments during the interest only period and the payment after principal repayment starts')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Interest Only Loan Calculator</h1>
            <p class="lead small text-muted">See the markup-only payment in the interest-only period, and the new payment once principal is added — compare both phases together.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="ioP" class="form-label">Loan Amount (Rs)</label><input type="number" class="form-control" id="ioP" value="5000000" step="any"></div>
<div class="col-md-4"><label for="ioRate" class="form-label">Annual Rate (%)</label><input type="number" class="form-control" id="ioRate" value="18" step="any"></div>
<div class="col-md-4"><label for="ioIoYears" class="form-label">Interest-Only Period (years)</label><input type="number" class="form-control" id="ioIoYears" value="2" step="any"></div>
<div class="col-md-4"><label for="ioTotalYears" class="form-label">Total Loan Term (years)</label><input type="number" class="form-control" id="ioTotalYears" value="10" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Payment (interest-only phase)</div><div class="fs-5 fw-bold" id="ioPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Payment (after IO phase)</div><div class="fs-5 fw-bold" id="ioPayAfter">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Paid in IO Phase</div><div class="fs-5 fw-bold" id="ioIntIo">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Interest Over Full Term</div><div class="fs-5 fw-bold" id="ioIntTotal">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the loan amount and annual rate.</li>
                        <li>Enter the interest-only period and the total term in years.</li>
                        <li>Compare the monthly payment of both phases — the second phase payment is always higher because the principal is also included.</li>
                        <li>Look at the total interest and plan your cash flow around it.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">In the interest-only phase the principal does not reduce at all, so the later payment jumps up suddenly. Prepare for that payment shock in advance.</p>
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
    var P=v("ioP"), rate=v("ioRate"), ioY=v("ioIoYears"), totalY=v("ioTotalYears");
    if(P<=0||totalY<=ioY||ioY<0){msg("Total term must be longer than the interest-only period.");return;}
    msg(""); var ioPay=P*rate/100/12; var remMonths=Math.round((totalY-ioY)*12); var after=pmt(P,rate,remMonths);
    var intIo=ioPay*Math.round(ioY*12); var intAfter=after*remMonths-P;
    out("ioPay",fmt(ioPay));out("ioPayAfter",fmt(after));out("ioIntIo",fmt(intIo));out("ioIntTotal",fmt(intIo+intAfter));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
