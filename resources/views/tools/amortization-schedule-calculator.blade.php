@extends('layouts.app')

@section('title', 'Amortization Schedule Calculator — Free Online Tool')
@section('meta_description', 'Generate a complete printable amortization table showing principal, interest and balance for every payment')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Amortization Schedule Calculator</h1>
            <p class="lead small text-muted">Full month-by-month repayment table for any loan — see the principal, interest and remaining balance of every payment in one place. Use the print button in your browser to save the table.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="amP" class="form-label">Loan Amount (Rs)</label><input type="number" class="form-control" id="amP" value="1000000" step="any"></div>
<div class="col-md-4"><label for="amRate" class="form-label">Annual Interest Rate (%)</label><input type="number" class="form-control" id="amRate" value="18" step="any"></div>
<div class="col-md-4"><label for="amMonths" class="form-label">Loan Term (months)</label><input type="number" class="form-control" id="amMonths" value="36" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Payment</div><div class="fs-5 fw-bold" id="amPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Interest</div><div class="fs-5 fw-bold" id="amInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Payable</div><div class="fs-5 fw-bold" id="amTot">—</div></div></div>
            </div>
<div class="table-responsive mt-4" style="max-height:420px;overflow-y:auto;"><table class="table table-sm table-striped align-middle mb-0"><thead><tr><th>Month</th><th>Payment</th><th>Principal</th><th>Interest</th><th>Remaining Balance</th></tr></thead><tbody id="amTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the loan amount, annual rate and term in months.</li>
                        <li>The monthly payment, total interest and total payable appear right away.</li>
                        <li>See every month's principal, interest and balance in the full table below.</li>
                        <li>Use your browser's print option to print the table.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Uses the standard amortization formula with a fixed monthly rate. Actual bank schedules can differ slightly due to rounding, processing fees, insurance or floating (KIBOR-linked) rates.</p>
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
    var P=v("amP"), rate=v("amRate"), n=Math.round(v("amMonths"));
    if(P<=0||n<=0){msg("Enter a loan amount and a term of at least 1 month.");html("amTable","");out("amPay","\u2014");out("amInt","\u2014");out("amTot","\u2014");return;}
    if(n>600){msg("Term is capped at 600 months (50 years) for the table.");n=600;}
    msg(""); var pay=pmt(P,rate,n), r=rate/100/12, bal=P, totInt=0, rows="";
    for(var m=1;m<=n;m++){var interest=bal*r;var principal=pay-interest;if(principal>bal){principal=bal;}bal-=principal;if(bal<0.01)bal=0;totInt+=interest;var thisPay=principal+interest;
        rows+="<tr><td>"+m+"</td><td>"+fmt(thisPay)+"</td><td>"+fmt(principal)+"</td><td>"+fmt(interest)+"</td><td>"+fmt(bal)+"</td></tr>";}
    out("amPay",fmt(pay));out("amInt",fmt(totInt));out("amTot",fmt(P+totInt));html("amTable",rows);
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
