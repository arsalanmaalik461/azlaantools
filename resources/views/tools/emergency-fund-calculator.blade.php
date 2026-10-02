@extends('layouts.app')

@section('title', 'Emergency Fund Calculator — Free Online Tool')
@section('meta_description', 'Calculate an emergency fund target from monthly expenses and months of cover, plus the saving plan to build it')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Emergency Fund Calculator</h1>
            <p class="lead small text-muted">Find your emergency fund target — how many months of expenses should be safe, and when your target will be complete with monthly saving.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="efExp" class="form-label">Monthly Essential Expenses (Rs)</label><input type="number" class="form-control" id="efExp" value="80000" step="any"></div>
<div class="col-md-4"><label for="efMonths" class="form-label">Months of Cover</label><select class="form-select" id="efMonths"><option value="3">3 months</option><option value="6" selected>6 months</option><option value="9">9 months</option><option value="12">12 months</option></select></div>
<div class="col-md-4"><label for="efSaved" class="form-label">Already Saved (Rs)</label><input type="number" class="form-control" id="efSaved" value="100000" step="any"></div>
<div class="col-md-4"><label for="efMonthly" class="form-label">Monthly Saving (Rs)</label><input type="number" class="form-control" id="efMonthly" value="20000" step="any"></div>
<div class="col-md-4"><label for="efRate" class="form-label">Return on Fund (%)</label><input type="number" class="form-control" id="efRate" value="8" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Emergency Fund Target</div><div class="fs-5 fw-bold" id="efTarget">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Amount Still Needed</div><div class="fs-5 fw-bold" id="efGap">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Time to Build Fund</div><div class="fs-5 fw-bold" id="efTime">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Your Current Cover</div><div class="fs-5 fw-bold" id="efCover">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter only your essential monthly expenses — rent, bills, groceries, transport.</li>
                        <li>Select how many months of cover you need (common advice is 3–6 months).</li>
                        <li>Enter the amount already saved and your monthly saving.</li>
                        <li>See the target and the time needed to reach it.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Keep your emergency fund somewhere easy to reach (a savings account or money market fund) — this is not an investment, it is protection. If your job is unstable, 9–12 months of cover is better.</p>
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
    var exp=v("efExp"), monthsCover=v("efMonths"), saved=v("efSaved"), monthly=v("efMonthly"), rate=v("efRate")/100/12;
    if(exp<=0){msg("Enter your monthly essential expenses.");return;}
    var target=exp*monthsCover; var gap=Math.max(target-saved,0);
    out("efTarget",fmt(target));out("efGap",fmt(gap));out("efCover",num(saved/exp,1)+" months of expenses");
    if(gap<=0){msg("Target already reached. Well done.");out("efTime","Already funded");return;}
    if(monthly<=0){msg("Enter a monthly saving amount to see the time needed.");out("efTime","\u2014");return;}
    msg(""); var bal=saved, months=0;
    while(bal<target&&months<1200){bal=bal*(1+rate)+monthly;months++;}
    out("efTime",months<1200?months+" months (about "+num(months/12,1)+" years)":"1200+ months");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
