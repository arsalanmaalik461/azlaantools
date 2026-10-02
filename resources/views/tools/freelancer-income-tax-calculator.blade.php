@extends('layouts.app')

@section('title', 'Freelancer Income Tax Calculator — Free Online Tool')
@section('meta_description', 'Enter your dollar or PKR income and PSEB status, and get a freelancer tax estimate.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Freelancer Income Tax Calculator</h1>
            <p class="lead small text-muted">Tax estimate for freelancers — your dollar income is converted to PKR at the exchange rate you enter, then the rate of your selected regime is applied. All rates are editable.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="ftUsd" class="form-label">Monthly Income (USD)</label><input type="number" class="form-control" id="ftUsd" value="2000" step="any"></div>
<div class="col-md-4"><label for="ftEx" class="form-label">Exchange Rate (Rs per USD) — editable</label><input type="number" class="form-control" id="ftEx" value="278" step="any"></div>
<div class="col-md-4"><label for="ftRegime" class="form-label">Tax Regime</label><select class="form-select" id="ftRegime"><option value="pseb" selected>PSEB registered exporter</option><option value="nonpseb">Not PSEB registered</option><option value="custom">Custom rate (use field below)</option></select></div>
<div class="col-md-4"><label for="ftCustom" class="form-label">Custom Tax Rate (% of gross) — editable</label><input type="number" class="form-control" id="ftCustom" value="1" step="any"></div>
<div class="col-md-4"><label for="ftMonths" class="form-label">Months Worked Per Year</label><input type="number" class="form-control" id="ftMonths" value="12" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Income (PKR)</div><div class="fs-5 fw-bold" id="ftPkrMonth">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Annual Gross Income (PKR)</div><div class="fs-5 fw-bold" id="ftAnnual">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Tax Rate Applied</div><div class="fs-5 fw-bold" id="ftRateUsed">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Estimated Annual Tax</div><div class="fs-5 fw-bold" id="ftTax">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Monthly Income After Tax</div><div class="fs-5 fw-bold" id="ftNetMonth">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your monthly dollar income and the current exchange rate.</li>
                        <li>Choose your regime — PSEB registered freelancers get a concessional final tax on IT services exports; others pay a different rate.</li>
                        <li>Use the custom rate option to test any rate.</li>
                        <li>See the yearly tax and your net monthly income after tax.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is only an estimate. Freelancer tax depends on your type of income, filer status and current FBR rules, and rates can change in the budget. Rates change — verify with the official source before relying on this. For large amounts, talk to a tax consultant.</p>
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
    var usd=v("ftUsd"), ex=v("ftEx"), regime=sv("ftRegime"), months=Math.round(v("ftMonths"));
    if(usd<=0||ex<=0||months<=0){msg("Enter income in USD, the exchange rate and months.");return;}
    msg(""); var rate=regime==="pseb"?0.25:(regime==="nonpseb"?1.0:v("ftCustom"));
    var pkrMonth=usd*ex; var annual=pkrMonth*months; var tax=annual*rate/100;
    out("ftPkrMonth",fmt(pkrMonth));out("ftAnnual",fmt(annual));out("ftRateUsed",pct(rate));out("ftTax",fmt(tax));out("ftNetMonth",fmt((annual-tax)/months));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
