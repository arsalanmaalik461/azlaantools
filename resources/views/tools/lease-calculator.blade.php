@extends('layouts.app')

@section('title', 'Lease Calculator — Free Online Tool')
@section('meta_description', 'Calculate equipment or vehicle lease payment from asset cost, residual value, term and lease rate factor')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Lease Calculator</h1>
            <p class="lead small text-muted">Find the monthly payment for an equipment, generator or vehicle lease — see depreciation and finance charge separately from asset cost, residual value and lease rate.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="lsCost" class="form-label">Asset Cost (Rs)</label><input type="number" class="form-control" id="lsCost" value="5000000" step="any"></div>
<div class="col-md-4"><label for="lsResidual" class="form-label">Residual Value at End (Rs)</label><input type="number" class="form-control" id="lsResidual" value="1500000" step="any"></div>
<div class="col-md-4"><label for="lsMonths" class="form-label">Lease Term (months)</label><input type="number" class="form-control" id="lsMonths" value="36" step="1"></div>
<div class="col-md-4"><label for="lsRate" class="form-label">Annual Lease Rate (%)</label><input type="number" class="form-control" id="lsRate" value="15" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Depreciation Charge</div><div class="fs-5 fw-bold" id="lsDep">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Finance Charge</div><div class="fs-5 fw-bold" id="lsFin">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Monthly Lease Payment</div><div class="fs-5 fw-bold" id="lsPay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Lease Payments</div><div class="fs-5 fw-bold" id="lsTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total With Residual Buyout</div><div class="fs-5 fw-bold" id="lsBuyout">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the asset cost and the residual value at the end of the lease.</li>
                        <li>Enter the term in months and the annual lease rate.</li>
                        <li>See the two parts of the monthly payment — depreciation and finance charge.</li>
                        <li>If you want to buy the asset when the lease ends, check the total buyout amount.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Uses the standard lease formula, where the finance charge applies on the average outstanding value. Real lease contracts may have advance rentals, insurance and maintenance separately.</p>
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
    var cost=v("lsCost"), res=v("lsResidual"), n=Math.round(v("lsMonths")), rate=v("lsRate")/100/12;
    if(cost<=0||n<=0||res<0||res>=cost){msg("Residual value must be less than the asset cost.");return;}
    msg(""); var dep=(cost-res)/n; var fin=(cost+res)*rate; var pay=dep+fin;
    out("lsDep",fmt(dep));out("lsFin",fmt(fin));out("lsPay",fmt(pay));out("lsTotal",fmt(pay*n));out("lsBuyout",fmt(pay*n+res));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
