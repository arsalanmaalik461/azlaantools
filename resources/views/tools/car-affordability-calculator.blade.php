@extends('layouts.app')

@section('title', 'Car Affordability Calculator — Free Online Tool')
@section('meta_description', 'Estimate a safe car price from income, running costs and installment limits before taking any auto finance')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Car Affordability Calculator</h1>
            <p class="lead small text-muted">Find a safe car budget based on your salary — it includes fuel and maintenance costs along with the installment, not just the monthly payment.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="caIncome" class="form-label">Monthly Income (Rs)</label><input type="number" class="form-control" id="caIncome" value="200000" step="any"></div>
<div class="col-md-4"><label for="caPct" class="form-label">Max % of Income for Car (installment + running) — editable</label><input type="number" class="form-control" id="caPct" value="25" step="any"></div>
<div class="col-md-4"><label for="caRunning" class="form-label">Monthly Fuel + Maintenance (Rs)</label><input type="number" class="form-control" id="caRunning" value="25000" step="any"></div>
<div class="col-md-4"><label for="caDown" class="form-label">Down Payment Saved (Rs)</label><input type="number" class="form-control" id="caDown" value="500000" step="any"></div>
<div class="col-md-4"><label for="caRate" class="form-label">Finance Markup Rate (%)</label><input type="number" class="form-control" id="caRate" value="18" step="any"></div>
<div class="col-md-4"><label for="caMonths" class="form-label">Finance Term (months)</label><input type="number" class="form-control" id="caMonths" value="60" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Monthly Car Budget</div><div class="fs-5 fw-bold" id="caBudget">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Affordable Installment</div><div class="fs-5 fw-bold" id="caInstall">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Finance Amount</div><div class="fs-5 fw-bold" id="caLoan">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Max Affordable Car Price</div><div class="fs-5 fw-bold" id="caPrice">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your monthly income and the maximum percentage to spend on the car (common guide is 20-25%).</li>
                        <li>Enter a monthly estimate for fuel, maintenance and insurance.</li>
                        <li>Enter the down payment, finance rate and term.</li>
                        <li>See the maximum affordable car price and choose a car below it.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is an affordability guide, not a bank approval. Banks also look at their debt burden ratio and your credit history.</p>
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
    var inc=v("caIncome"), pctv=v("caPct"), run=v("caRunning"), down=v("caDown"), rate=v("caRate"), n=Math.round(v("caMonths"));
    if(inc<=0||n<=0){msg("Enter your monthly income and finance term.");return;}
    var budget=inc*pctv/100; var install=Math.max(budget-run,0);
    if(install<=0)msg("Running costs alone use up the whole car budget percentage. Increase the percentage or lower running costs.");else msg("");
    var loan=pvAnnuity(install,rate,n);
    out("caBudget",fmt(budget));out("caInstall",fmt(install));out("caLoan",fmt(loan));out("caPrice",fmt(loan+down));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
