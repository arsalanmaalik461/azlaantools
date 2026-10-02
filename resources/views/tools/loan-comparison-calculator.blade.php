@extends('layouts.app')

@section('title', 'Loan Comparison Calculator — Free Online Tool')
@section('meta_description', 'Compare two or three loan offers side by side on payment, total interest, fees and total repayable amount')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Loan Comparison Calculator</h1>
            <p class="lead small text-muted">Compare up to three bank offers side by side — monthly payment, total interest, fees and total payable, so the cheapest loan is clear.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="lc1Amt" class="form-label">Offer 1 Amount (Rs)</label><input type="number" class="form-control" id="lc1Amt" value="1000000" step="any"></div>
<div class="col-md-4"><label for="lc1Rate" class="form-label">Offer 1 Rate (%)</label><input type="number" class="form-control" id="lc1Rate" value="18" step="any"></div>
<div class="col-md-4"><label for="lc1Months" class="form-label">Offer 1 Term (months)</label><input type="number" class="form-control" id="lc1Months" value="36" step="1"></div>
<div class="col-md-4"><label for="lc1Fees" class="form-label">Offer 1 Fees (Rs)</label><input type="number" class="form-control" id="lc1Fees" value="10000" step="any"></div>
<div class="col-md-4"><label for="lc2Amt" class="form-label">Offer 2 Amount (Rs)</label><input type="number" class="form-control" id="lc2Amt" value="1000000" step="any"></div>
<div class="col-md-4"><label for="lc2Rate" class="form-label">Offer 2 Rate (%)</label><input type="number" class="form-control" id="lc2Rate" value="20" step="any"></div>
<div class="col-md-4"><label for="lc2Months" class="form-label">Offer 2 Term (months)</label><input type="number" class="form-control" id="lc2Months" value="36" step="1"></div>
<div class="col-md-4"><label for="lc2Fees" class="form-label">Offer 2 Fees (Rs)</label><input type="number" class="form-control" id="lc2Fees" value="0" step="any"></div>
<div class="col-md-4"><label for="lc3Amt" class="form-label">Offer 3 Amount (Rs)</label><input type="number" class="form-control" id="lc3Amt" value="1000000" step="any"></div>
<div class="col-md-4"><label for="lc3Rate" class="form-label">Offer 3 Rate (%)</label><input type="number" class="form-control" id="lc3Rate" value="16" step="any"></div>
<div class="col-md-4"><label for="lc3Months" class="form-label">Offer 3 Term (months)</label><input type="number" class="form-control" id="lc3Months" value="48" step="1"></div>
<div class="col-md-4"><label for="lc3Fees" class="form-label">Offer 3 Fees (Rs)</label><input type="number" class="form-control" id="lc3Fees" value="20000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Lowest Total Cost Offer</div><div class="fs-5 fw-bold" id="lcBest">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Saving vs Most Expensive</div><div class="fs-5 fw-bold" id="lcSaving">—</div></div></div>
            </div>
<div class="table-responsive mt-4" style="max-height:420px;overflow-y:auto;"><table class="table table-sm table-striped align-middle mb-0"><thead><tr><th>Offer</th><th>Monthly Payment</th><th>Total Interest</th><th>Fees</th><th>Total Cost</th></tr></thead><tbody id="lcTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter amount, rate, term and fees for each bank offer (set the amount to 0 for an offer you do not have).</li>
                        <li>Compare the total cost in the table with monthly payment, total interest and fees.</li>
                        <li>The offer with the lowest total cost and the saving vs the most expensive one are shown below.</li>
                        <li>Do not look at the rate only — fees and a longer term often make a cheap rate expensive.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Comparison is best on the same amount. For floating rate offers the rate can change later — keep fixed and floating in mind separately.</p>
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
    var rows="", best=null, worst=null;
    for(var i=1;i<=3;i++){var P=v("lc"+i+"Amt"), rate=v("lc"+i+"Rate"), n=Math.round(v("lc"+i+"Months")), fees=v("lc"+i+"Fees");
        if(P<=0||n<=0){rows+="<tr><td>Offer "+i+"</td><td colspan=4>Not entered</td></tr>";continue;}
        var pay=pmt(P,rate,n); var totInt=pay*n-P; var total=pay*n+fees;
        rows+="<tr><td>Offer "+i+"</td><td>"+fmt(pay)+"</td><td>"+fmt(totInt)+"</td><td>"+fmt(fees)+"</td><td>"+fmt(total)+"</td></tr>";
        if(best===null||total<best.total)best={i:i,total:total}; if(worst===null||total>worst.total)worst={i:i,total:total};}
    html("lcTable",rows);
    if(best){msg("");out("lcBest","Offer "+best.i+" \u2014 "+fmt(best.total));out("lcSaving",fmt(worst.total-best.total));}
    else{msg("Enter at least one complete offer.");}
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
