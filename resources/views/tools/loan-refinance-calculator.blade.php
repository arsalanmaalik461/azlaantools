@extends('layouts.app')

@section('title', 'Loan Refinance Calculator — Free Online Tool')
@section('meta_description', 'Compare an existing loan with a new lower rate loan and find monthly saving, break even month and net benefit')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Loan Refinance Calculator</h1>
            <p class="lead small text-muted">Will replacing your old loan with a new lower-rate loan benefit you — find the monthly saving, break-even month and total net benefit.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="rfBal" class="form-label">Current Loan Balance (Rs)</label><input type="number" class="form-control" id="rfBal" value="800000" step="any"></div>
<div class="col-md-4"><label for="rfOldRate" class="form-label">Current Rate (%)</label><input type="number" class="form-control" id="rfOldRate" value="22" step="any"></div>
<div class="col-md-4"><label for="rfOldMonths" class="form-label">Months Left on Current Loan</label><input type="number" class="form-control" id="rfOldMonths" value="36" step="1"></div>
<div class="col-md-4"><label for="rfNewRate" class="form-label">New Loan Rate (%)</label><input type="number" class="form-control" id="rfNewRate" value="17" step="any"></div>
<div class="col-md-4"><label for="rfNewMonths" class="form-label">New Loan Term (months)</label><input type="number" class="form-control" id="rfNewMonths" value="36" step="1"></div>
<div class="col-md-4"><label for="rfFees" class="form-label">Refinance Fees / Closing Costs (Rs)</label><input type="number" class="form-control" id="rfFees" value="15000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Current Monthly Payment</div><div class="fs-5 fw-bold" id="rfOldPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">New Monthly Payment</div><div class="fs-5 fw-bold" id="rfNewPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Saving</div><div class="fs-5 fw-bold" id="rfSave">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Break-even Month</div><div class="fs-5 fw-bold" id="rfBreak">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Benefit Over New Term</div><div class="fs-5 fw-bold" id="rfNet">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your current balance, rate and remaining months.</li>
                        <li>Enter the new loan rate, term and all fees.</li>
                        <li>See the monthly saving and break-even month — the fees are recovered in that many months of saving.</li>
                        <li>If the net benefit is negative, do not refinance, even if the rate is lower.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">A longer new term lowers the monthly payment but can increase total interest — that is why the net benefit line is on the total of the term, not only the installment.</p>
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
    var bal=v("rfBal"), oldR=v("rfOldRate"), oldN=Math.round(v("rfOldMonths")), newR=v("rfNewRate"), newN=Math.round(v("rfNewMonths")), fees=v("rfFees");
    if(bal<=0||oldN<=0||newN<=0){msg("Enter the balance and both terms.");return;}
    msg(""); var oldPay=pmt(bal,oldR,oldN), newPay=pmt(bal,newR,newN); var save=oldPay-newPay;
    var oldCost=oldPay*oldN, newCost=newPay*newN+fees;
    out("rfOldPay",fmt(oldPay));out("rfNewPay",fmt(newPay));out("rfSave",fmt(save));
    out("rfBreak",save>0?Math.ceil(fees/save)+"":"No saving \u2014 payment does not drop");
    out("rfNet",fmt(oldCost-newCost));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
