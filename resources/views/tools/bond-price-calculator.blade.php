@extends('layouts.app')

@section('title', 'Bond Price Calculator — Free Online Tool')
@section('meta_description', 'Calculate the fair price of a bond or sukuk from face value, coupon rate, market yield and years to maturity')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Bond Price Calculator</h1>
            <p class="lead small text-muted">Find the fair price of a PIB, treasury bill alternative or sukuk — enter face value, coupon, market yield and maturity. You enter all the rates yourself; there is no live market data.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="bpFace" class="form-label">Face Value (Rs)</label><input type="number" class="form-control" id="bpFace" value="100000" step="any"></div>
<div class="col-md-4"><label for="bpCoupon" class="form-label">Coupon Rate (% per year)</label><input type="number" class="form-control" id="bpCoupon" value="12" step="any"></div>
<div class="col-md-4"><label for="bpYield" class="form-label">Market Yield (% per year)</label><input type="number" class="form-control" id="bpYield" value="14" step="any"></div>
<div class="col-md-4"><label for="bpYears" class="form-label">Years to Maturity</label><input type="number" class="form-control" id="bpYears" value="5" step="any"></div>
<div class="col-md-4"><label for="bpFreq" class="form-label">Coupon Payments Per Year</label><select class="form-select" id="bpFreq"><option value="1">Yearly (1)</option><option value="2" selected>Half-yearly (2)</option><option value="4">Quarterly (4)</option></select></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Fair Price</div><div class="fs-5 fw-bold" id="bpPrice">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Price per 100 Face Value</div><div class="fs-5 fw-bold" id="bpPer100">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Coupon Per Payment</div><div class="fs-5 fw-bold" id="bpCouponAmt">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Premium / Discount</div><div class="fs-5 fw-bold" id="bpStatus">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the face value of the bond or sukuk.</li>
                        <li>Enter the coupon rate and market yield (the return you expect).</li>
                        <li>Select the years to maturity and the coupon frequency.</li>
                        <li>See the fair price and the premium or discount status.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Standard discounted cash flow bond pricing. This is the clean price — accrued profit, brokerage and tax are not included. Yields change daily in the market; enter the yield you want to test.</p>
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
    var F=v("bpFace"), c=v("bpCoupon"), y=v("bpYield"), years=v("bpYears"), m=v("bpFreq");
    if(F<=0||years<=0||m<=0){msg("Enter face value, years and frequency.");return;}
    msg(""); var n=years*m, coupon=F*c/100/m, r=y/100/m;
    var price=r===0?coupon*n+F:coupon*(1-Math.pow(1+r,-n))/r+F*Math.pow(1+r,-n);
    out("bpPrice",fmt2(price));out("bpPer100",num(price/F*100,2));out("bpCouponAmt",fmt2(coupon));
    out("bpStatus",price>F?"Premium (above face value)":(price<F?"Discount (below face value)":"At par"));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
