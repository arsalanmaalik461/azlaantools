@extends('layouts.app')

@section('title', '50 30 20 Budget Calculator — Free Online Tool')
@section('meta_description', 'Split monthly income into needs, wants and savings targets using the popular 50 30 20 budgeting rule')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">50 30 20 Budget Calculator</h1>
            <p class="lead small text-muted">Split your monthly income using the 50/30/20 rule — needs (50%), wants (30%), and savings (20%) — and compare it with your actual spending.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="bgIncome" class="form-label">Monthly Take-home Income (Rs)</label><input type="number" class="form-control" id="bgIncome" value="150000" step="any"></div>
<div class="col-md-4"><label for="bgNeeds" class="form-label">Your Actual Monthly Needs Spending (Rs)</label><input type="number" class="form-control" id="bgNeeds" value="0" step="any"></div>
<div class="col-md-4"><label for="bgWants" class="form-label">Your Actual Monthly Wants Spending (Rs)</label><input type="number" class="form-control" id="bgWants" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Needs Target (50%)</div><div class="fs-5 fw-bold" id="bgNeedsT">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Wants Target (30%)</div><div class="fs-5 fw-bold" id="bgWantsT">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Savings Target (20%)</div><div class="fs-5 fw-bold" id="bgSaveT">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Needs vs Target</div><div class="fs-5 fw-bold" id="bgNeedsStatus">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Wants vs Target</div><div class="fs-5 fw-bold" id="bgWantsStatus">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Income Left After Your Actual Spending</div><div class="fs-5 fw-bold" id="bgLeft">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your monthly take-home income.</li>
                        <li>The targets will show instantly: 50% needs, 30% wants, 20% savings.</li>
                        <li>If you enter your actual needs and wants spending, you will also see the difference from the target.</li>
                        <li>Try to keep your spending within these limits every month.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Needs include rent, bills, groceries, transport, and minimum loan payments. Wants include eating out, shopping, and entertainment. This rule is a starting guideline — in expensive cities, needs can be more than 50%.</p>
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
    var inc=v("bgIncome"), needs=v("bgNeeds"), wants=v("bgWants");
    if(inc<=0){msg("Enter your monthly income.");return;}
    msg(""); var nT=inc*0.5, wT=inc*0.3, sT=inc*0.2;
    out("bgNeedsT",fmt(nT));out("bgWantsT",fmt(wT));out("bgSaveT",fmt(sT));
    out("bgNeedsStatus",needs<=0?"\u2014":(needs<=nT?fmt(nT-needs)+" under target":fmt(needs-nT)+" OVER target"));
    out("bgWantsStatus",wants<=0?"\u2014":(wants<=wT?fmt(wT-wants)+" under target":fmt(wants-wT)+" OVER target"));
    out("bgLeft",fmt(inc-needs-wants));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
