@extends('layouts.app')

@section('title', 'Debt Payoff Calculator — Free Online Tool')
@section('meta_description', 'Plan clearing multiple debts with snowball and avalanche methods and compare total interest and debt free date')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Debt Payoff Calculator</h1>
            <p class="lead small text-muted">Plan how to clear several debts together — compare the snowball method (smallest balance first) with the avalanche method (highest rate first).</p>
            <div class="row g-3">
<div class="col-md-4"><label for="d1Bal" class="form-label">Debt 1 Balance (Rs)</label><input type="number" class="form-control" id="d1Bal" value="50000" step="any"></div>
<div class="col-md-4"><label for="d1Rate" class="form-label">Debt 1 APR (%)</label><input type="number" class="form-control" id="d1Rate" value="42" step="any"></div>
<div class="col-md-4"><label for="d1Min" class="form-label">Debt 1 Minimum Payment (Rs)</label><input type="number" class="form-control" id="d1Min" value="2500" step="any"></div>
<div class="col-md-4"><label for="d2Bal" class="form-label">Debt 2 Balance (Rs)</label><input type="number" class="form-control" id="d2Bal" value="300000" step="any"></div>
<div class="col-md-4"><label for="d2Rate" class="form-label">Debt 2 Rate (%)</label><input type="number" class="form-control" id="d2Rate" value="18" step="any"></div>
<div class="col-md-4"><label for="d2Min" class="form-label">Debt 2 Minimum Payment (Rs)</label><input type="number" class="form-control" id="d2Min" value="10000" step="any"></div>
<div class="col-md-4"><label for="d3Bal" class="form-label">Debt 3 Balance (Rs)</label><input type="number" class="form-control" id="d3Bal" value="20000" step="any"></div>
<div class="col-md-4"><label for="d3Rate" class="form-label">Debt 3 APR (%)</label><input type="number" class="form-control" id="d3Rate" value="36" step="any"></div>
<div class="col-md-4"><label for="d3Min" class="form-label">Debt 3 Minimum Payment (Rs)</label><input type="number" class="form-control" id="d3Min" value="1500" step="any"></div>
<div class="col-md-4"><label for="dxExtra" class="form-label">Extra Monthly Amount (Rs)</label><input type="number" class="form-control" id="dxExtra" value="5000" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Snowball: Months to Debt Free</div><div class="fs-5 fw-bold" id="dpSnowMonths">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Snowball: Total Interest</div><div class="fs-5 fw-bold" id="dpSnowInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Avalanche: Months to Debt Free</div><div class="fs-5 fw-bold" id="dpAvalMonths">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Avalanche: Total Interest</div><div class="fs-5 fw-bold" id="dpAvalInt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Better Method (less interest)</div><div class="fs-5 fw-bold" id="dpBetter">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Interest Saved by Better Method</div><div class="fs-5 fw-bold" id="dpSaving">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter up to three debts — balance, rate and minimum payment (keep the balance 0 for any debt you do not have).</li>
                        <li>Enter the extra amount you can pay each month on top of the minimums.</li>
                        <li>Compare the months and total interest for both methods.</li>
                        <li>Whichever method you choose, roll the minimum payment of each cleared debt into the next debt — that is the core idea of snowball and avalanche.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">The simulation applies interest each month first, then minimum payments, then the extra amount on the target debt. When a debt is cleared, its minimum payment joins the extra amount.</p>
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


function simulate(method){
    var debts=[{b:v("d1Bal"),r:v("d1Rate")/100/12,min:v("d1Min")},{b:v("d2Bal"),r:v("d2Rate")/100/12,min:v("d2Min")},{b:v("d3Bal"),r:v("d3Rate")/100/12,min:v("d3Min")}].filter(function(d){return d.b>0;});
    var baseExtra=v("dxExtra"), totalInt=0, months=0;
    while(debts.some(function(d){return d.b>0.01;})&&months<600){
        months++;
        debts.forEach(function(d){if(d.b>0){var i=d.b*d.r;totalInt+=i;d.b+=i;}});
        debts.forEach(function(d){if(d.b>0){var p=Math.min(d.min,d.b);d.b-=p;}});
        var freed=0; debts.forEach(function(d){if(d.b<=0.01)freed+=d.min;});
        var pool=baseExtra+freed;
        var alive=debts.filter(function(d){return d.b>0.01;});
        alive.sort(function(a,b){return method==="snowball"?a.b-b.b:b.r-a.r;});
        for(var k=0;k<alive.length&&pool>0;k++){var p2=Math.min(pool,alive[k].b);alive[k].b-=p2;pool-=p2;}
    }
    return {months:months,interest:totalInt,done:months<600};
}
function calc(){
    if(v("d1Bal")<=0&&v("d2Bal")<=0&&v("d3Bal")<=0){msg("Enter at least one debt balance.");return;}
    msg(""); var s=simulate("snowball"), a=simulate("avalanche");
    out("dpSnowMonths",s.done?s.months+"":"600+ (check payments)");out("dpSnowInt",fmt(s.interest));
    out("dpAvalMonths",a.done?a.months+"":"600+ (check payments)");out("dpAvalInt",fmt(a.interest));
    if(s.done&&a.done){var better=s.interest<=a.interest?"Snowball":"Avalanche";out("dpBetter",better);out("dpSaving",fmt(Math.abs(s.interest-a.interest)));}
    else{out("dpBetter","Payments too low to clear within 50 years");out("dpSaving","\u2014");}
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
