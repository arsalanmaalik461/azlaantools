@extends('layouts.app')

@section('title', 'Education Savings Calculator — Free Online Tool')
@section('meta_description', 'Calculate monthly saving needed for a child education fund with fee inflation and expected profit rate')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Education Savings Calculator</h1>
            <p class="lead small text-muted">Plan a fund for your child's education — include fee inflation to find the real cost when university starts and the monthly saving you need.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="edAge" class="form-label">Child Current Age (years)</label><input type="number" class="form-control" id="edAge" value="5" step="1"></div>
<div class="col-md-4"><label for="edStart" class="form-label">Age When Education Starts</label><input type="number" class="form-control" id="edStart" value="18" step="1"></div>
<div class="col-md-4"><label for="edCost" class="form-label">Total Education Cost at Today Prices (Rs)</label><input type="number" class="form-control" id="edCost" value="2000000" step="any"></div>
<div class="col-md-4"><label for="edInfl" class="form-label">Fee Inflation (% per year)</label><input type="number" class="form-control" id="edInfl" value="10" step="any"></div>
<div class="col-md-4"><label for="edRet" class="form-label">Expected Return (% per year)</label><input type="number" class="form-control" id="edRet" value="12" step="any"></div>
<div class="col-md-4"><label for="edSaved" class="form-label">Already Saved (Rs)</label><input type="number" class="form-control" id="edSaved" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Future Cost at Start Age</div><div class="fs-5 fw-bold" id="edFuture">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Years to Save</div><div class="fs-5 fw-bold" id="edYears">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Value of Current Savings at Start</div><div class="fs-5 fw-bold" id="edFvSaved">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Saving Needed</div><div class="fs-5 fw-bold" id="edMonthly">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the child's current age and the age when education will start.</li>
                        <li>Write the total degree cost at today's prices.</li>
                        <li>Enter fee inflation and the expected return on your savings.</li>
                        <li>See the future cost and the monthly saving needed — the earlier you start, the smaller the monthly amount.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This estimate assumes the full cost is needed at the start age; real fees are paid semester by semester, so this is a safe (conservative) estimate. The return is not guaranteed.</p>
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
    var age=v("edAge"), start=v("edStart"), cost=v("edCost"), infl=v("edInfl")/100, ret=v("edRet")/100, saved=v("edSaved");
    var years=start-age;
    if(cost<=0||years<=0){msg("Education start age must be higher than the current age, and cost must be entered.");return;}
    msg(""); var future=cost*Math.pow(1+infl,years); var fvSaved=saved*Math.pow(1+ret,years); var gap=Math.max(future-fvSaved,0);
    var rm=Math.pow(1+ret,1/12)-1, months=years*12;
    var monthly=rm===0?gap/months:gap*rm/(Math.pow(1+rm,months)-1);
    out("edFuture",fmt(future));out("edYears",years+"");out("edFvSaved",fmt(fvSaved));out("edMonthly",fmt(monthly));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
