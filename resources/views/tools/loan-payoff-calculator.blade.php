@extends('layouts.app')

@section('title', 'Loan Payoff Calculator — Free Online Tool')
@section('meta_description', 'See how extra monthly or one time payments cut loan term and total interest and find the early payoff date')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Loan Payoff Calculator</h1>
            <p class="lead small text-muted">How much sooner the loan ends and how much interest is saved with extra payments — a clear comparison of the regular plan and the faster plan.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="lpP" class="form-label">Loan Amount (Rs)</label><input type="number" class="form-control" id="lpP" value="500000" step="any"></div>
<div class="col-md-4"><label for="lpRate" class="form-label">Annual Rate (%)</label><input type="number" class="form-control" id="lpRate" value="18" step="any"></div>
<div class="col-md-4"><label for="lpMonths" class="form-label">Original Term (months)</label><input type="number" class="form-control" id="lpMonths" value="36" step="1"></div>
<div class="col-md-4"><label for="lpExtra" class="form-label">Extra Monthly Payment (Rs)</label><input type="number" class="form-control" id="lpExtra" value="5000" step="any"></div>
<div class="col-md-4"><label for="lpOne" class="form-label">One-time Extra Payment in Month 1 (Rs)</label><input type="number" class="form-control" id="lpOne" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Original Total Interest</div><div class="fs-5 fw-bold" id="lpOrigInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">New Payoff Time (months)</div><div class="fs-5 fw-bold" id="lpNewMonths">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">New Total Interest</div><div class="fs-5 fw-bold" id="lpNewInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Saved</div><div class="fs-5 fw-bold" id="lpSaved">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Months Sooner</div><div class="fs-5 fw-bold" id="lpSooner">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the loan amount, rate and original term.</li>
                        <li>Enter the extra amount you can pay each month, and if you want to pay a lump sum in the first month, enter that too.</li>
                        <li>See the new payoff time, interest saved and how many months sooner the loan ends.</li>
                        <li>Give the bank written instructions to apply extra payments to the principal.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The calculation assumes the bank charges no prepayment penalty. Some banks take early settlement charges — check your loan agreement.</p>
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
    var P=v("lpP"), rate=v("lpRate"), n=Math.round(v("lpMonths")), extra=v("lpExtra"), one=v("lpOne");
    if(P<=0||n<=0){msg("Enter the loan amount and original term.");return;}
    msg(""); var r=rate/100/12, basePay=pmt(P,rate,n);
    var origInt=basePay*n-P;
    var bal=P, totInt=0, months=0;
    while(bal>0.01&&months<1200){months++;var interest=bal*r;totInt+=interest;var principal=basePay-interest+extra;if(months===1)principal+=one;if(principal>bal)principal=bal;bal-=principal;}
    out("lpOrigInt",fmt(origInt));out("lpNewMonths",months+"");out("lpNewInt",fmt(totInt));out("lpSaved",fmt(origInt-totInt));out("lpSooner",(n-months)+"");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
