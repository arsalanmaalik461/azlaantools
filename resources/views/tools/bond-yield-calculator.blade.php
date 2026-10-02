@extends('layouts.app')

@section('title', 'Bond Yield Calculator — Free Online Tool')
@section('meta_description', 'Calculate current yield and yield to maturity from bond price, coupon, face value and maturity period')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Bond Yield Calculator</h1>
            <p class="lead small text-muted">Find the current yield and yield to maturity (YTM) of a bond or sukuk so you can compare the return of different papers.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="byPrice" class="form-label">Current Price (Rs)</label><input type="number" class="form-control" id="byPrice" value="95000" step="any"></div>
<div class="col-md-4"><label for="byFace" class="form-label">Face Value (Rs)</label><input type="number" class="form-control" id="byFace" value="100000" step="any"></div>
<div class="col-md-4"><label for="byCoupon" class="form-label">Coupon Rate (% per year)</label><input type="number" class="form-control" id="byCoupon" value="12" step="any"></div>
<div class="col-md-4"><label for="byYears" class="form-label">Years to Maturity</label><input type="number" class="form-control" id="byYears" value="5" step="any"></div>
<div class="col-md-4"><label for="byFreq" class="form-label">Coupon Payments Per Year</label><select class="form-select" id="byFreq"><option value="1">Yearly (1)</option><option value="2" selected>Half-yearly (2)</option><option value="4">Quarterly (4)</option></select></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Current Yield</div><div class="fs-5 fw-bold" id="byCurrent">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Yield to Maturity (YTM)</div><div class="fs-5 fw-bold" id="byYtm">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Annual Coupon Income</div><div class="fs-5 fw-bold" id="byCouponAmt">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the current market price and face value of the bond.</li>
                        <li>Enter the coupon rate, maturity years and coupon frequency.</li>
                        <li>Current yield only shows the coupon income; YTM also includes the gain or loss from the price change.</li>
                        <li>Compare both yields to make your decision.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">YTM is solved numerically and assumes all coupons are reinvested at the same yield and the bond is held to maturity. Sukuk rentals are treated like coupons here.</p>
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
    var price=v("byPrice"), F=v("byFace"), c=v("byCoupon"), years=v("byYears"), m=v("byFreq");
    if(price<=0||F<=0||years<=0||m<=0){msg("Enter price, face value, years and frequency.");return;}
    msg(""); var n=years*m, coupon=F*c/100/m;
    var curYield=(F*c/100)/price*100;
    function priceAt(r){return r===0?coupon*n+F:coupon*(1-Math.pow(1+r,-n))/r+F*Math.pow(1+r,-n);}
    var lo=-0.09, hi=1.0, mid=0;
    for(var i=0;i<200;i++){mid=(lo+hi)/2;if(priceAt(mid)>price)lo=mid;else hi=mid;}
    out("byCurrent",pct(curYield));out("byYtm",pct(mid*m*100));out("byCouponAmt",fmt(F*c/100));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
