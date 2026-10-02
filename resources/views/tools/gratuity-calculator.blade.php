@extends('layouts.app')

@section('title', 'Gratuity Calculator Pakistan — Free Online Tool')
@section('meta_description', 'Enter your last salary and years of service to estimate your gratuity on retirement or resignation.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Gratuity Calculator Pakistan</h1>
            <p class="lead small text-muted">Estimate gratuity at the end of employment — your last monthly salary, years of service, and the commonly used formula, whose day basis is editable.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="grSalary" class="form-label">Last Monthly Salary (Rs)</label><input type="number" class="form-control" id="grSalary" value="120000" step="any"></div>
<div class="col-md-4"><label for="grYears" class="form-label">Years of Service</label><input type="number" class="form-control" id="grYears" value="15" step="any"></div>
<div class="col-md-4"><label for="grDivisor" class="form-label">Per-day Salary Basis (days per month) — editable</label><input type="number" class="form-control" id="grDivisor" value="26" step="1"></div>
<div class="col-md-4"><label for="grDays" class="form-label">Gratuity Days Per Year of Service — editable</label><input type="number" class="form-control" id="grDays" value="30" step="1"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Per-day Salary</div><div class="fs-5 fw-bold" id="grPerDay">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Gratuity Days</div><div class="fs-5 fw-bold" id="grDaysTotal">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Estimated Gratuity Amount</div><div class="fs-5 fw-bold" id="grAmount">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">One Month Salary Per Year (comparison)</div><div class="fs-5 fw-bold" id="grAlt">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your last monthly salary.</li>
                        <li>Enter your total years of service (part years can be written as decimals).</li>
                        <li>Confirm the formula basis — usually a 26 or 30 day monthly basis and 30 days per year.</li>
                        <li>See the estimated gratuity amount and the one-month-per-year comparison.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Gratuity rights and the formula depend on labour law and company policy, and some organizations have different rules. Rates change — verify with the official source before relying on this. Confirm the formula with your HR or appointment letter.</p>
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
    var sal=v("grSalary"), years=v("grYears"), divisor=v("grDivisor"), days=v("grDays");
    if(sal<=0||years<=0||divisor<=0||days<=0){msg("Enter salary, years of service and the day basis.");return;}
    msg(""); var perDay=sal/divisor; var totalDays=days*years;
    out("grPerDay",fmt2(perDay));out("grDaysTotal",num(totalDays,0)+" days");out("grAmount",fmt(perDay*totalDays));out("grAlt",fmt(sal*years));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
