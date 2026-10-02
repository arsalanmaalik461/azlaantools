@extends('layouts.app')

@section('title', 'Annuity Calculator — Free Online Tool')
@section('meta_description', 'Calculate regular payout from a lump sum annuity or the lump sum needed for a target monthly payout')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Annuity Calculator</h1>
            <p class="lead small text-muted">Regular monthly income from a lump sum, or the lump sum needed for a target monthly income — both calculations in one place.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="anMode" class="form-label">Calculation Mode</label><select class="form-select" id="anMode"><option value="payout" selected>Lump sum to regular payout</option><option value="lump">Target payout to lump sum needed</option></select></div>
<div class="col-md-4"><label for="anLump" class="form-label">Lump Sum Amount (Rs)</label><input type="number" class="form-control" id="anLump" value="5000000" step="any"></div>
<div class="col-md-4"><label for="anTarget" class="form-label">Target Payout Per Period (Rs)</label><input type="number" class="form-control" id="anTarget" value="50000" step="any"></div>
<div class="col-md-4"><label for="anRate" class="form-label">Annual Return Rate (%)</label><input type="number" class="form-control" id="anRate" value="10" step="any"></div>
<div class="col-md-4"><label for="anYears" class="form-label">Payout Period (years)</label><input type="number" class="form-control" id="anYears" value="20" step="any"></div>
<div class="col-md-4"><label for="anFreq" class="form-label">Payouts Per Year</label><select class="form-select" id="anFreq"><option value="12" selected>Monthly (12)</option><option value="4">Quarterly (4)</option><option value="1">Yearly (1)</option></select></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Result</div><div class="fs-5 fw-bold" id="anMain">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Paid Out Over Period</div><div class="fs-5 fw-bold" id="anTotalOut">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Portion of Payouts</div><div class="fs-5 fw-bold" id="anInterest">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Choose the mode: payout from a lump sum, or lump sum from a target payout.</li>
                        <li>Enter the amount, expected annual return rate and payout period.</li>
                        <li>Select the payout frequency (monthly, quarterly or yearly).</li>
                        <li>See the per-payout amount, total paid out and the interest portion in the result.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is a payout (immediate) annuity using a fixed assumed return. Real annuity and pension products deduct charges and returns are not guaranteed, so treat the result as a planning estimate.</p>
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
    var mode=sv("anMode"), rate=v("anRate"), years=v("anYears"), m=v("anFreq");
    if(years<=0||m<=0){msg("Enter a payout period of at least 1 year.");return;}
    msg(""); var n=years*m, r=rate/100/m;
    if(mode==="payout"){var P=v("anLump");if(P<=0){msg("Enter the lump sum amount.");return;}
        var pay=r===0?P/n:P*r/(1-Math.pow(1+r,-n));var tot=pay*n;
        out("anMain",fmt(pay)+" per payout");out("anTotalOut",fmt(tot));out("anInterest",fmt(tot-P));
    } else {var want=v("anTarget");if(want<=0){msg("Enter the target payout per period.");return;}
        var need=r===0?want*n:want*(1-Math.pow(1+r,-n))/r;var tot2=want*n;
        out("anMain",fmt(need)+" lump sum needed");out("anTotalOut",fmt(tot2));out("anInterest",fmt(tot2-need));
    }
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
