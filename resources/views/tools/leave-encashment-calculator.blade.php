@extends('layouts.app')

@section('title', 'Leave Encashment Calculator — Free Online Tool')
@section('meta_description', 'Enter monthly salary and unused leave days to find your leave encashment amount.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Leave Encashment Calculator</h1>
            <p class="lead small text-muted">Find the encashment for unused leave — the per-day rate is made from your monthly salary and multiplied by unused leave days. The day basis and tax are both editable.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="leSalary" class="form-label">Monthly Salary (Rs)</label><input type="number" class="form-control" id="leSalary" value="150000" step="any"></div>
<div class="col-md-4"><label for="leDays" class="form-label">Unused Leave Days</label><input type="number" class="form-control" id="leDays" value="30" step="1"></div>
<div class="col-md-4"><label for="leDivisor" class="form-label">Per-day Basis (days per month) — editable</label><input type="number" class="form-control" id="leDivisor" value="30" step="1"></div>
<div class="col-md-4"><label for="leTax" class="form-label">Tax Deduction (%) — editable</label><input type="number" class="form-control" id="leTax" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Per-day Salary</div><div class="fs-5 fw-bold" id="lePerDay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Gross Encashment</div><div class="fs-5 fw-bold" id="leGross">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Tax Deducted</div><div class="fs-5 fw-bold" id="leTaxAmt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Encashment</div><div class="fs-5 fw-bold" id="leNet">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your monthly salary — usually gross salary is used; check your company policy.</li>
                        <li>Enter the remaining (unused) leave days.</li>
                        <li>Enter the per-day basis — some employers use 30 days, some use working days.</li>
                        <li>If tax applies on encashment, enter that percentage to see the net amount.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The encashment formula depends on your HR policy and employment contract — some employers calculate on basic salary, some on gross. Rates change — verify with the official source before relying on this.</p>
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
    var sal=v("leSalary"), days=v("leDays"), divisor=v("leDivisor"), taxPct=v("leTax");
    if(sal<=0||days<=0||divisor<=0){msg("Enter salary, unused days and the per-day basis.");return;}
    msg(""); var perDay=sal/divisor; var gross=perDay*days; var tax=gross*taxPct/100;
    out("lePerDay",fmt2(perDay));out("leGross",fmt(gross));out("leTaxAmt",fmt(tax));out("leNet",fmt(gross-tax));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
