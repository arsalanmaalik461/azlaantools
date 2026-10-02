@extends('layouts.app')

@section('title', 'CAGR Calculator — Free Online Tool')
@section('meta_description', 'Calculate compound annual growth rate between a starting value and ending value over any number of years')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">CAGR Calculator</h1>
            <p class="lead small text-muted">Find the compound annual growth rate (CAGR) of an investment, business sales or portfolio — the average yearly growth from the starting and ending values.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="cgStart" class="form-label">Starting Value (Rs)</label><input type="number" class="form-control" id="cgStart" value="100000" step="any"></div>
<div class="col-md-4"><label for="cgEnd" class="form-label">Ending Value (Rs)</label><input type="number" class="form-control" id="cgEnd" value="250000" step="any"></div>
<div class="col-md-4"><label for="cgYears" class="form-label">Number of Years</label><input type="number" class="form-control" id="cgYears" value="5" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">CAGR</div><div class="fs-5 fw-bold" id="cgCagr">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Growth</div><div class="fs-5 fw-bold" id="cgTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Absolute Gain</div><div class="fs-5 fw-bold" id="cgGain">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Time to Double at This Rate</div><div class="fs-5 fw-bold" id="cgDouble">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the starting value (investment, sales or revenue).</li>
                        <li>Enter the ending value and the total number of years.</li>
                        <li>See the CAGR, total growth and doubling time.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">CAGR smooths the growth into one average yearly rate — the actual growth in each year can be different.</p>
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
    var a=v("cgStart"), b=v("cgEnd"), years=v("cgYears");
    if(a<=0||b<=0||years<=0){msg("Starting and ending values and years must be greater than zero.");return;}
    msg(""); var cagr=(Math.pow(b/a,1/years)-1)*100;
    out("cgCagr",pct(cagr));out("cgTotal",pct((b-a)/a*100));out("cgGain",fmt(b-a));
    out("cgDouble",cagr>0?num(Math.log(2)/Math.log(1+cagr/100),1)+" years":"\u2014 (no growth)");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
