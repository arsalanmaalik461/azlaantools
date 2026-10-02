@extends('layouts.app')

@section('title', 'Hajj Savings Calculator — Free Online Tool')
@section('meta_description', 'Calculate monthly saving needed to reach a Hajj cost target by a chosen year with yearly cost increase')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Hajj Savings Calculator</h1>
            <p class="lead small text-muted">A monthly saving plan for Hajj — package costs rise every year, so include inflation to find the real target and monthly amount.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="hjCost" class="form-label">Current Hajj Cost Per Person (Rs)</label><input type="number" class="form-control" id="hjCost" value="1200000" step="any"></div>
<div class="col-md-4"><label for="hjPersons" class="form-label">Number of Persons</label><input type="number" class="form-control" id="hjPersons" value="2" step="1"></div>
<div class="col-md-4"><label for="hjYears" class="form-label">Years Until Target Hajj</label><input type="number" class="form-control" id="hjYears" value="3" step="1"></div>
<div class="col-md-4"><label for="hjInfl" class="form-label">Yearly Cost Increase (%)</label><input type="number" class="form-control" id="hjInfl" value="10" step="any"></div>
<div class="col-md-4"><label for="hjRet" class="form-label">Return on Savings (%)</label><input type="number" class="form-control" id="hjRet" value="10" step="any"></div>
<div class="col-md-4"><label for="hjSaved" class="form-label">Already Saved (Rs)</label><input type="number" class="form-control" id="hjSaved" value="200000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Future Total Cost</div><div class="fs-5 fw-bold" id="hjFuture">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Value of Current Savings Then</div><div class="fs-5 fw-bold" id="hjFvSaved">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Saving Needed</div><div class="fs-5 fw-bold" id="hjMonthly">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total You Will Deposit</div><div class="fs-5 fw-bold" id="hjTotalSave">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter today per person Hajj package cost and the number of persons.</li>
                        <li>Enter how many years later you plan Hajj.</li>
                        <li>Enter your estimate of yearly cost increase and return on savings.</li>
                        <li>See the future cost and required monthly saving.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The Hajj package price depends on the government scheme, private package, exchange rate and Saudi charges, and changes every year. Rates change — verify with the official source before relying on this.</p>
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
    var cost=v("hjCost"), persons=Math.round(v("hjPersons")), years=Math.round(v("hjYears")), infl=v("hjInfl")/100, ret=v("hjRet")/100, saved=v("hjSaved");
    if(cost<=0||persons<=0||years<=0){msg("Enter the current cost, persons and years.");return;}
    msg(""); var future=cost*persons*Math.pow(1+infl,years); var fvSaved=saved*Math.pow(1+ret,years); var gap=Math.max(future-fvSaved,0);
    var rm=Math.pow(1+ret,1/12)-1, months=years*12;
    var monthly=rm===0?gap/months:gap*rm/(Math.pow(1+rm,months)-1);
    out("hjFuture",fmt(future));out("hjFvSaved",fmt(fvSaved));out("hjMonthly",fmt(monthly));out("hjTotalSave",fmt(monthly*months));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
