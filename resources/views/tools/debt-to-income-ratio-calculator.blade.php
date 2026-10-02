@extends('layouts.app')

@section('title', 'Debt To Income Ratio Calculator — Free Online Tool')
@section('meta_description', 'Calculate debt to income ratio from monthly income and all monthly debt payments and check bank eligibility bands')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Debt To Income Ratio Calculator</h1>
            <p class="lead small text-muted">Find your debt-to-income (DTI) ratio — banks approve or reject loans based on this ratio, so check it yourself before applying.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="dtiIncome" class="form-label">Gross Monthly Income (Rs)</label><input type="number" class="form-control" id="dtiIncome" value="250000" step="any"></div>
<div class="col-md-4"><label for="dtiHouse" class="form-label">Housing / Rent / Home Finance (Rs / month)</label><input type="number" class="form-control" id="dtiHouse" value="50000" step="any"></div>
<div class="col-md-4"><label for="dtiCar" class="form-label">Car Finance (Rs / month)</label><input type="number" class="form-control" id="dtiCar" value="30000" step="any"></div>
<div class="col-md-4"><label for="dtiCards" class="form-label">Credit Cards Minimums (Rs / month)</label><input type="number" class="form-control" id="dtiCards" value="10000" step="any"></div>
<div class="col-md-4"><label for="dtiOther" class="form-label">Other Loans (Rs / month)</label><input type="number" class="form-control" id="dtiOther" value="10000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Front-end Ratio (housing only)</div><div class="fs-5 fw-bold" id="dtiFront">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Back-end Ratio (all debts)</div><div class="fs-5 fw-bold" id="dtiBack">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Eligibility Band</div><div class="fs-5 fw-bold" id="dtiBand">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Room for New Payment (at 43% back-end cap)</div><div class="fs-5 fw-bold" id="dtiRoom">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your gross monthly income (before tax).</li>
                        <li>Enter each monthly debt payment separately — housing, car, cards and other loans.</li>
                        <li>Look at the back-end ratio — this is what really decides eligibility.</li>
                        <li>If the ratio is high, pay down some debts first, then apply.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">A back-end ratio below 36% is usually seen as strong, and 43% is the limit for many lenders, but every bank has its own policy. Rates change — verify with the official source before relying on this.</p>
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
    var inc=v("dtiIncome"), house=v("dtiHouse"), total=house+v("dtiCar")+v("dtiCards")+v("dtiOther");
    if(inc<=0){msg("Enter your gross monthly income.");return;}
    msg(""); var front=house/inc*100, back=total/inc*100;
    out("dtiFront",pct(front));out("dtiBack",pct(back));
    out("dtiBand",back<=36?"Strong \u2014 most banks comfortable":(back<=43?"Borderline \u2014 approval possible with good history":"High \u2014 most banks likely to decline or reduce amount"));
    out("dtiRoom",fmt(Math.max(inc*0.43-total,0)));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
