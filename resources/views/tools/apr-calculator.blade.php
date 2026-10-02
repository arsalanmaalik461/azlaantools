@extends('layouts.app')

@section('title', 'APR Calculator — Free Online Tool')
@section('meta_description', 'Calculate the true annual percentage rate of a loan after adding fees, insurance and processing charges')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">APR Calculator</h1>
            <p class="lead small text-muted">Add processing fees and charges to the advertised rate to find the loan's true annual percentage rate (APR) and compare offers fairly.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="apLoan" class="form-label">Loan Amount (Rs)</label><input type="number" class="form-control" id="apLoan" value="500000" step="any"></div>
<div class="col-md-4"><label for="apRate" class="form-label">Advertised Annual Rate (%)</label><input type="number" class="form-control" id="apRate" value="18" step="any"></div>
<div class="col-md-4"><label for="apMonths" class="form-label">Term (months)</label><input type="number" class="form-control" id="apMonths" value="36" step="1"></div>
<div class="col-md-4"><label for="apFees" class="form-label">Upfront Fees and Charges (Rs)</label><input type="number" class="form-control" id="apFees" value="10000" step="any"></div>
<div class="col-md-4"><label for="apMonthlyFee" class="form-label">Monthly Insurance / Charges (Rs)</label><input type="number" class="form-control" id="apMonthlyFee" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">True APR</div><div class="fs-5 fw-bold" id="apApr">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Effective Annual Rate</div><div class="fs-5 fw-bold" id="apEff">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Payment (incl. monthly charges)</div><div class="fs-5 fw-bold" id="apPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Amount You Receive</div><div class="fs-5 fw-bold" id="apNet">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Cost of Loan</div><div class="fs-5 fw-bold" id="apCost">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Extra Cost vs Advertised Rate</div><div class="fs-5 fw-bold" id="apExtra">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the loan amount, advertised rate and term.</li>
                        <li>Write upfront amounts like processing fee, documentation and insurance in the fees field.</li>
                        <li>Enter the charges applied every month in the monthly charges field.</li>
                        <li>Compare the true APR with the advertised rate — a higher APR means a more expensive loan.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">APR is solved numerically so that the present value of all payments equals the net amount you actually receive. Lenders may disclose APR with slightly different conventions.</p>
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
    var P=v("apLoan"), rate=v("apRate"), n=Math.round(v("apMonths")), fees=v("apFees"), mfee=v("apMonthlyFee");
    if(P<=0||n<=0){msg("Enter a loan amount and term.");return;}
    var net=P-fees; if(net<=0){msg("Upfront fees must be less than the loan amount.");return;}
    msg(""); var basePay=pmt(P,rate,n); var pay=basePay+mfee;
    var lo=0, hi=0.2, mid=0;
    for(var i=0;i<100;i++){mid=(lo+hi)/2;var pv=mid===0?pay*n:pay*(1-Math.pow(1+mid,-n))/mid;if(pv>net)lo=mid;else hi=mid;}
    var apr=mid*12*100, eff=(Math.pow(1+mid,12)-1)*100, totalCost=pay*n+fees;
    out("apApr",pct(apr));out("apEff",pct(eff));out("apPay",fmt(pay));out("apNet",fmt(net));out("apCost",fmt(totalCost));out("apExtra",fmt(totalCost-(basePay*n)));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
