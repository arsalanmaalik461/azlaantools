@extends('layouts.app')

@section('title', 'Effective Interest Rate Calculator — Free Online Tool')
@section('meta_description', 'Convert a nominal rate with monthly, quarterly or daily compounding into the true effective annual rate')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Effective Interest Rate Calculator</h1>
            <p class="lead small text-muted">Convert a nominal rate with its compounding frequency into the real effective annual rate (EAR) — compare different bank offers on one scale.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="erNom" class="form-label">Nominal Annual Rate (%)</label><input type="number" class="form-control" id="erNom" value="18" step="any"></div>
<div class="col-md-4"><label for="erFreq" class="form-label">Compounding Frequency</label><select class="form-select" id="erFreq"><option value="1">Yearly</option><option value="2">Half-yearly</option><option value="4">Quarterly</option><option value="12" selected>Monthly</option><option value="365">Daily</option></select></div>
<div class="col-md-4"><label for="erP" class="form-label">Principal to Test (Rs)</label><input type="number" class="form-control" id="erP" value="100000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Effective Annual Rate (EAR)</div><div class="fs-5 fw-bold" id="erEar">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Rate Per Compounding Period</div><div class="fs-5 fw-bold" id="erPeriodic">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Earned in 1 Year on Principal</div><div class="fs-5 fw-bold" id="erInterest">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the nominal annual rate quoted by the bank.</li>
                        <li>Select the compounding frequency — how often interest is added.</li>
                        <li>See the EAR — this is the real yearly rate used to compare offers.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Formula: EAR = (1 + nominal/m)^m − 1. The more often interest is added, the higher the effective rate.</p>
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
    var nom=v("erNom"), m=v("erFreq"), P=v("erP");
    if(m<=0){msg("Select a compounding frequency.");return;}
    msg(""); var ear=(Math.pow(1+nom/100/m,m)-1)*100;
    out("erEar",pct(ear));out("erPeriodic",pct(nom/m));out("erInterest",fmt(P*ear/100));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
