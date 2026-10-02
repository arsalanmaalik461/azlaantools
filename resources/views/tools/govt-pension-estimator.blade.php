@extends('layouts.app')

@section('title', 'Government Pension Estimator — Free Online Tool')
@section('meta_description', 'Enter last basic pay and service years to estimate monthly pension and commutation.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Government Pension Estimator</h1>
            <p class="lead small text-muted">Pension estimate for government employees — gross pension, commutation (lump sum) and monthly net pension from last basic pay and qualifying service. Formula percentages are editable.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="gpBasic" class="form-label">Last Basic Pay (Rs)</label><input type="number" class="form-control" id="gpBasic" value="100000" step="any"></div>
<div class="col-md-4"><label for="gpYears" class="form-label">Qualifying Service (years, max counted 30)</label><input type="number" class="form-control" id="gpYears" value="30" step="any"></div>
<div class="col-md-4"><label for="gpCommPct" class="form-label">Commutation Percentage (%) — editable</label><input type="number" class="form-control" id="gpCommPct" value="35" step="any"></div>
<div class="col-md-4"><label for="gpFactor" class="form-label">Commutation Factor (per age table) — editable</label><input type="number" class="form-control" id="gpFactor" value="15" step="any"></div>
<div class="col-md-4"><label for="gpAllow" class="form-label">Pension Increases / Allowances Already Included (%) — editable</label><input type="number" class="form-control" id="gpAllow" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Gross Monthly Pension</div><div class="fs-5 fw-bold" id="gpGross">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Monthly Pension (after commutation)</div><div class="fs-5 fw-bold" id="gpNet">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Commuted Portion (monthly)</div><div class="fs-5 fw-bold" id="gpCommAmt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Commutation Lump Sum</div><div class="fs-5 fw-bold" id="gpLump">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the last basic pay (without allowances).</li>
                        <li>Enter qualifying service years — the formula counts a maximum of 30 years.</li>
                        <li>Enter the commutation percentage and the factor from your age table (both are editable).</li>
                        <li>See gross pension, monthly pension after commutation, and the lump sum.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Standard formula: gross pension = last basic pay × service years × 7 / 300. The commutation factor comes from the official table for your age, and rules change over time. Rates change — verify with the official source before relying on this. Confirm the final calculation with the AG / treasury office.</p>
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
    var basic=v("gpBasic"), years=Math.min(v("gpYears"),30), commPct=v("gpCommPct"), factor=v("gpFactor"), allow=v("gpAllow");
    if(basic<=0||years<=0){msg("Enter last basic pay and years of service.");return;}
    msg(""); var gross=basic*years*7/300; gross=gross*(1+allow/100);
    var commuted=gross*commPct/100; var lump=commuted*12*factor;
    out("gpGross",fmt(gross));out("gpNet",fmt(gross-commuted));out("gpCommAmt",fmt(commuted));out("gpLump",fmt(lump));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
