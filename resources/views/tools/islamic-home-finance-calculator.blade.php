@extends('layouts.app')

@section('title', 'Islamic Home Finance Calculator — Free Online Tool')
@section('meta_description', 'Model diminishing musharaka home finance with bank share buyout, rent on bank share and monthly total payment')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Islamic Home Finance Calculator</h1>
            <p class="lead small text-muted">A diminishing musharaka model — rent on the bank's share plus buying back a part of the bank's share every month (unit purchase); monthly payment plus a yearly schedule.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="ihPrice" class="form-label">Property Price (Rs)</label><input type="number" class="form-control" id="ihPrice" value="15000000" step="any"></div>
<div class="col-md-4"><label for="ihBankPct" class="form-label">Bank Share (%)</label><input type="number" class="form-control" id="ihBankPct" value="70" step="any"></div>
<div class="col-md-4"><label for="ihMonths" class="form-label">Term (months)</label><input type="number" class="form-control" id="ihMonths" value="180" step="1"></div>
<div class="col-md-4"><label for="ihRent" class="form-label">Annual Rent Rate on Bank Share (%) — editable</label><input type="number" class="form-control" id="ihRent" value="12" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Your Initial Share (down payment)</div><div class="fs-5 fw-bold" id="ihCust">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Bank Share Amount</div><div class="fs-5 fw-bold" id="ihBank">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">First Monthly Payment (rent + unit)</div><div class="fs-5 fw-bold" id="ihFirst">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Last Monthly Payment</div><div class="fs-5 fw-bold" id="ihLast">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Average Monthly Payment</div><div class="fs-5 fw-bold" id="ihAvg">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Rent Paid Over Term</div><div class="fs-5 fw-bold" id="ihRentTotal">—</div></div></div>
            </div>
<div class="table-responsive mt-4" style="max-height:420px;overflow-y:auto;"><table class="table table-sm table-striped align-middle mb-0"><thead><tr><th>Year</th><th>Rent Paid in Year</th><th>Units Bought in Year</th><th>Bank Share at Year End</th></tr></thead><tbody id="ihTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the total property price.</li>
                        <li>Enter the bank's share in percent — the rest is your initial share (down payment).</li>
                        <li>Enter the term in months and the rent rate on the bank's share.</li>
                        <li>See the first and last monthly payment — the rent falls every month because the bank's share keeps reducing. See the yearly table for details.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">This is a simplified model of diminishing musharaka: the unit price is assumed fixed for the whole term, and rent is charged only on the bank's remaining share. In real Islamic banks the rent is revised against a benchmark, and takaful and fees are separate. For Shariah details, read your bank's mufti board document.</p>
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
    var price=v("ihPrice"), bankPct=v("ihBankPct"), months=Math.round(v("ihMonths")), rentRate=v("ihRent")/100/12;
    if(price<=0||months<=0||bankPct<=0||bankPct>=100){msg("Enter the property price, a bank share between 1 and 99 percent, and the term.");html("ihTable","");return;}
    msg(""); var bank=price*bankPct/100, cust=price-bank, unit=bank/months, share=bank, totRent=0, totPay=0, firstPay=0, lastPay=0, rows="";
    var yRent=0, yUnit=0;
    for(var m=1;m<=months;m++){var rent=share*rentRate;var pay=rent+unit;totRent+=rent;totPay+=pay;if(m===1)firstPay=pay;lastPay=pay;share-=unit;if(share<0.01)share=0;yRent+=rent;yUnit+=unit;
        if(m%12===0||m===months){rows+="<tr><td>"+Math.ceil(m/12)+"</td><td>"+fmt(yRent)+"</td><td>"+fmt(yUnit)+"</td><td>"+fmt(share)+"</td></tr>";yRent=0;yUnit=0;}}
    out("ihCust",fmt(cust));out("ihBank",fmt(bank));out("ihFirst",fmt(firstPay));out("ihLast",fmt(lastPay));out("ihAvg",fmt(totPay/months));out("ihRentTotal",fmt(totRent));html("ihTable",rows);
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
