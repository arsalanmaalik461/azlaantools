@extends('layouts.app')

@section('title', 'Home Affordability Calculator — Free Online Tool')
@section('meta_description', 'Estimate how much house price fits a monthly income using payment to income limits and down payment saved')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Home Affordability Calculator</h1>
            <p class="lead small text-muted">Find how much house your income can afford — combine the housing ratio, current debts and down payment to get a safe price range.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="haIncome" class="form-label">Monthly Income (Rs)</label><input type="number" class="form-control" id="haIncome" value="300000" step="any"></div>
<div class="col-md-4"><label for="haRatio" class="form-label">Max % of Income for Housing — editable</label><input type="number" class="form-control" id="haRatio" value="28" step="any"></div>
<div class="col-md-4"><label for="haCosts" class="form-label">Monthly Tax / Insurance / Maintenance (Rs)</label><input type="number" class="form-control" id="haCosts" value="15000" step="any"></div>
<div class="col-md-4"><label for="haDown" class="form-label">Down Payment Saved (Rs)</label><input type="number" class="form-control" id="haDown" value="2000000" step="any"></div>
<div class="col-md-4"><label for="haRate" class="form-label">Finance Rate (%)</label><input type="number" class="form-control" id="haRate" value="18" step="any"></div>
<div class="col-md-4"><label for="haYears" class="form-label">Finance Term (years)</label><input type="number" class="form-control" id="haYears" value="20" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Monthly Housing Budget</div><div class="fs-5 fw-bold" id="haBudget">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Budget for Finance Payment</div><div class="fs-5 fw-bold" id="haPiBudget">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Finance Amount</div><div class="fs-5 fw-bold" id="haLoan">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Affordable House Price</div><div class="fs-5 fw-bold" id="haPrice">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your monthly income and the max percentage for housing (guide: 28%).</li>
                        <li>Enter a monthly estimate for property tax, insurance and maintenance.</li>
                        <li>Enter the down payment, finance rate and term.</li>
                        <li>See the max affordable house price and keep your search in this range.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is an affordability estimate, not a bank or society approval. Keep a separate budget for transfer fees, stamp duty and development charges.</p>
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
    var inc=v("haIncome"), ratio=v("haRatio"), costs=v("haCosts"), down=v("haDown"), rate=v("haRate"), years=Math.round(v("haYears"));
    if(inc<=0||years<=0){msg("Enter monthly income and finance term.");return;}
    var budget=inc*ratio/100; var pi=Math.max(budget-costs,0);
    if(pi<=0)msg("Tax, insurance and maintenance estimates use the whole housing budget.");else msg("");
    var loan=pvAnnuity(pi,rate,years*12);
    out("haBudget",fmt(budget));out("haPiBudget",fmt(pi));out("haLoan",fmt(loan));out("haPrice",fmt(loan+down));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
