@extends('layouts.app')

@section('title', 'Credit Card Payoff Calculator — Free Online Tool')
@section('meta_description', 'Calculate months and total interest needed to clear a card balance with a fixed monthly payment')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Credit Card Payoff Calculator</h1>
            <p class="lead small text-muted">Plan ahead: with a fixed monthly payment, see how many months it takes to clear your credit card balance and how much total interest you will pay.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="ccpBal" class="form-label">Card Balance (Rs)</label><input type="number" class="form-control" id="ccpBal" value="150000" step="any"></div>
<div class="col-md-4"><label for="ccpApr" class="form-label">Card APR (%)</label><input type="number" class="form-control" id="ccpApr" value="42" step="any"></div>
<div class="col-md-4"><label for="ccpPay" class="form-label">Fixed Monthly Payment (Rs)</label><input type="number" class="form-control" id="ccpPay" value="10000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Months to Pay Off</div><div class="fs-5 fw-bold" id="ccpMonths">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Payoff Time</div><div class="fs-5 fw-bold" id="ccpTime">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Interest Paid</div><div class="fs-5 fw-bold" id="ccpInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Amount Paid</div><div class="fs-5 fw-bold" id="ccpTotal">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the card balance and APR.</li>
                        <li>Enter the fixed amount you can pay every month.</li>
                        <li>See the months, total interest, and total paid.</li>
                        <li>Try a higher payment and see how much interest you save.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This calculation assumes no new spending. Stop new purchases on the card or the balance will grow again. The minimum payment is only about 5%, which keeps the debt going for years.</p>
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
    var bal=v("ccpBal"), apr=v("ccpApr"), pay=v("ccpPay");
    if(bal<=0||pay<=0){msg("Enter the balance and a monthly payment.");out("ccpMonths","\u2014");return;}
    var r=apr/100/12;
    if(pay<=bal*r){msg("Your monthly payment does not even cover one month of interest, so the balance will never clear. Increase the payment.");out("ccpMonths","\u2014");out("ccpTime","\u2014");out("ccpInt","\u2014");out("ccpTotal","\u2014");return;}
    msg(""); var b=bal, totInt=0, months=0;
    while(b>0&&months<1200){var interest=b*r;var principal=Math.min(pay-interest,b);b-=principal;totInt+=interest;months++;}
    var yrs=Math.floor(months/12), rem=months%12;
    out("ccpMonths",months+"");out("ccpTime",yrs>0?yrs+" years "+rem+" months":months+" months");out("ccpInt",fmt(totInt));out("ccpTotal",fmt(bal+totInt));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
