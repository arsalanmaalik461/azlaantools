@extends('layouts.app')

@section('title', 'Murabaha Calculator — Free Online Tool')
@section('meta_description', 'Calculate total murabaha cost and monthly installment from asset cost, bank profit amount and repayment term')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Murabaha Calculator</h1>
            <p class="lead small text-muted">Murabaha financing calculation — add the bank profit to the asset cost to get the total sale price and monthly installment.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="muCost" class="form-label">Asset Cost (Rs)</label><input type="number" class="form-control" id="muCost" value="1000000" step="any"></div>
<div class="col-md-4"><label for="muDown" class="form-label">Down Payment (Rs)</label><input type="number" class="form-control" id="muDown" value="200000" step="any"></div>
<div class="col-md-4"><label for="muMode" class="form-label">Profit Basis</label><select class="form-select" id="muMode"><option value="flat" selected>Flat % of cost (whole term)</option><option value="annual">Annual % of cost × years</option></select></div>
<div class="col-md-4"><label for="muRate" class="form-label">Bank Profit Rate (%)</label><input type="number" class="form-control" id="muRate" value="15" step="any"></div>
<div class="col-md-4"><label for="muMonths" class="form-label">Term (months)</label><input type="number" class="form-control" id="muMonths" value="36" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Bank Profit Amount</div><div class="fs-5 fw-bold" id="muProfit">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Murabaha Selling Price</div><div class="fs-5 fw-bold" id="muPrice">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Amount Paid in Installments</div><div class="fs-5 fw-bold" id="muFinance">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Installment</div><div class="fs-5 fw-bold" id="muMonthly">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the asset cost (car, machine, or goods).</li>
                        <li>Enter the down payment.</li>
                        <li>Select the profit basis — a flat percentage for the whole term, or an annual percentage multiplied by the years. Use the same basis written in your bank offer letter.</li>
                        <li>See the total selling price and monthly installment.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">In murabaha the bank buys the asset and sells it with profit — the price is fixed and the installment does not change during the term. In the actual offer, takaful, processing fee and documentation charges may be separate.</p>
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
    var cost=v("muCost"), down=v("muDown"), mode=sv("muMode"), rate=v("muRate"), n=Math.round(v("muMonths"));
    if(cost<=0||n<=0||down>=cost){msg("Enter the asset cost, a down payment below the cost, and the term.");return;}
    msg(""); var profit=mode==="flat"?cost*rate/100:cost*rate/100*(n/12);
    var price=cost+profit; var financed=price-down;
    out("muProfit",fmt(profit));out("muPrice",fmt(price));out("muFinance",fmt(financed));out("muMonthly",fmt(financed/n));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
