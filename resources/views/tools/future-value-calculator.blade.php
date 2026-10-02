@extends('layouts.app')

@section('title', 'Future Value Calculator — Free Online Tool')
@section('meta_description', 'Calculate the future value of a lump sum or regular deposits with compound growth over any period')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Future Value Calculator</h1>
            <p class="lead small text-muted">Find the future value of your current amount and monthly deposits — see how much your money will grow with compound growth in the future.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="fvP" class="form-label">Lump Sum Today (Rs)</label><input type="number" class="form-control" id="fvP" value="200000" step="any"></div>
<div class="col-md-4"><label for="fvMonthly" class="form-label">Monthly Deposit (Rs)</label><input type="number" class="form-control" id="fvMonthly" value="10000" step="any"></div>
<div class="col-md-4"><label for="fvRate" class="form-label">Annual Return Rate (%)</label><input type="number" class="form-control" id="fvRate" value="12" step="any"></div>
<div class="col-md-4"><label for="fvYears" class="form-label">Years</label><input type="number" class="form-control" id="fvYears" value="10" step="any"></div>
<div class="col-md-4"><label for="fvFreq" class="form-label">Compounding Frequency</label><select class="form-select" id="fvFreq"><option value="1">Yearly</option><option value="4">Quarterly</option><option value="12" selected>Monthly</option><option value="365">Daily</option></select></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Future Value</div><div class="fs-5 fw-bold" id="fvTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Invested</div><div class="fs-5 fw-bold" id="fvInvested">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Growth (interest earned)</div><div class="fs-5 fw-bold" id="fvGrowth">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your lump sum amount today and monthly deposit (enter 0 for whichever does not apply).</li>
                        <li>Enter the expected annual return and years.</li>
                        <li>Select the compounding frequency.</li>
                        <li>See the future value, total invested and growth separately.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Standard time value of money formula. The return rate is an assumption — actual returns change with the market and are not guaranteed.</p>
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
    var P=v("fvP"), dep=v("fvMonthly"), rate=v("fvRate")/100, years=v("fvYears"), n=v("fvFreq");
    if(years<=0||(P<=0&&dep<=0)){msg("Enter a lump sum or monthly deposit and the number of years.");return;}
    msg(""); var fvP=P*Math.pow(1+rate/n,n*years);
    var rm=Math.pow(1+rate/n,n/12)-1, months=Math.round(years*12);
    var fvD=dep<=0?0:(rm===0?dep*months:dep*(Math.pow(1+rm,months)-1)/rm);
    var invested=P+dep*months;
    out("fvTotal",fmt(fvP+fvD));out("fvInvested",fmt(invested));out("fvGrowth",fmt(fvP+fvD-invested));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
