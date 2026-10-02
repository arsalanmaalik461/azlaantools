@extends('layouts.app')

@section('title', 'Lumpsum Calculator — Free Online Tool')
@section('meta_description', 'Project growth of a one time investment with expected yearly return and optional yearly top up amount')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Lumpsum Calculator</h1>
            <p class="lead small text-muted">Project the growth of a one-time investment — expected return, yearly top-up and also the real value after inflation.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="luP" class="form-label">One-time Investment (Rs)</label><input type="number" class="form-control" id="luP" value="500000" step="any"></div>
<div class="col-md-4"><label for="luRate" class="form-label">Expected Annual Return (%)</label><input type="number" class="form-control" id="luRate" value="15" step="any"></div>
<div class="col-md-4"><label for="luYears" class="form-label">Years</label><input type="number" class="form-control" id="luYears" value="7" step="1"></div>
<div class="col-md-4"><label for="luTop" class="form-label">Yearly Top-up (Rs, optional)</label><input type="number" class="form-control" id="luTop" value="0" step="any"></div>
<div class="col-md-4"><label for="luInfl" class="form-label">Inflation (% per year)</label><input type="number" class="form-control" id="luInfl" value="8" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Future Value</div><div class="fs-5 fw-bold" id="luFv">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Invested</div><div class="fs-5 fw-bold" id="luInvested">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Gain</div><div class="fs-5 fw-bold" id="luGain">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Future Value in Today Money (after inflation)</div><div class="fs-5 fw-bold" id="luReal">—</div></div></div>
            </div>
<div class="table-responsive mt-4" style="max-height:420px;overflow-y:auto;"><table class="table table-sm table-striped align-middle mb-0"><thead><tr><th>Year</th><th>Invested So Far</th><th>Value at Year End</th></tr></thead><tbody id="luTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter your one-time investment amount.</li>
                        <li>Enter the expected annual return and years.</li>
                        <li>If you invest more each year, enter the yearly top-up.</li>
                        <li>See the year-by-year table, total gain and the real value after inflation.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Mutual fund and scheme returns are not guaranteed and change every year. This projection uses one fixed assumed return — the actual result can be lower or higher.</p>
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
    var P=v("luP"), rate=v("luRate")/100, years=Math.round(v("luYears")), top=v("luTop"), infl=v("luInfl")/100;
    if((P<=0&&top<=0)||years<=0){msg("Enter an investment amount and years.");html("luTable","");return;}
    msg(""); var val=P, rows="";
    for(var y=1;y<=years;y++){val=val*(1+rate)+top;rows+="<tr><td>"+y+"</td><td>"+fmt(P+top*y)+"</td><td>"+fmt(val)+"</td></tr>";}
    var invested=P+top*years;
    out("luFv",fmt(val));out("luInvested",fmt(invested));out("luGain",fmt(val-invested));out("luReal",fmt(val/Math.pow(1+infl,years)));html("luTable",rows);
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
