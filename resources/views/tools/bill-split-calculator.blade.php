@extends('layouts.app')

@section('title', 'Bill Split Calculator — Free Online Tool')
@section('meta_description', 'Split a restaurant, trip or group expense bill among people with custom shares and optional service charge')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Bill Split Calculator</h1>
            <p class="lead small text-muted">Split a restaurant, trip or group expense equally among friends — find each person's share including service charge and tax.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="bsTotal" class="form-label">Total Bill Amount (Rs)</label><input type="number" class="form-control" id="bsTotal" value="8500" step="any"></div>
<div class="col-md-4"><label for="bsService" class="form-label">Service Charge (%)</label><input type="number" class="form-control" id="bsService" value="10" step="any"></div>
<div class="col-md-4"><label for="bsTax" class="form-label">Tax / GST (%)</label><input type="number" class="form-control" id="bsTax" value="5" step="any"></div>
<div class="col-md-4"><label for="bsPeople" class="form-label">Number of People</label><input type="number" class="form-control" id="bsPeople" value="4" step="1"></div>
<div class="col-md-4"><label for="bsExtra" class="form-label">Extra Fixed Charges (Rs)</label><input type="number" class="form-control" id="bsExtra" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Grand Total (with charges)</div><div class="fs-5 fw-bold" id="bsGrand">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Per Person Share</div><div class="fs-5 fw-bold" id="bsPer">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Charges Added</div><div class="fs-5 fw-bold" id="bsCharges">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Charges Per Person</div><div class="fs-5 fw-bold" id="bsPerCharge">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the total bill amount.</li>
                        <li>Enter the service charge and tax percentage (if not already included in the bill).</li>
                        <li>Enter the number of people.</li>
                        <li>Each person's share will show instantly.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is an equal split. If someone spent more or less, adjust their share manually — this tool is for equal splits.</p>
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
    var total=v("bsTotal"), service=v("bsService"), tax=v("bsTax"), people=Math.round(v("bsPeople")), extra=v("bsExtra");
    if(total<=0||people<=0){msg("Enter the bill amount and at least 1 person.");return;}
    msg(""); var charges=total*(service+tax)/100+extra; var grand=total+charges;
    out("bsGrand",fmt(grand));out("bsPer",fmt2(grand/people));out("bsCharges",fmt(charges));out("bsPerCharge",fmt2(charges/people));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
