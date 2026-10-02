@extends('layouts.app')

@section('title', 'Capital Gains Tax Calculator — Free Online Tool')
@section('meta_description', 'Calculate capital gains tax on property, shares or other assets from gain amount and the tax rate entered')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Capital Gains Tax Calculator</h1>
            <p class="lead small text-muted">Estimate the capital gains tax on the sale of a plot, shares or any asset — you enter the tax rate yourself so the result matches the current slab.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="cgtBuy" class="form-label">Purchase Price (Rs)</label><input type="number" class="form-control" id="cgtBuy" value="5000000" step="any"></div>
<div class="col-md-4"><label for="cgtSell" class="form-label">Selling Price (Rs)</label><input type="number" class="form-control" id="cgtSell" value="7000000" step="any"></div>
<div class="col-md-4"><label for="cgtExp" class="form-label">Selling Expenses / Commission (Rs)</label><input type="number" class="form-control" id="cgtExp" value="100000" step="any"></div>
<div class="col-md-4"><label for="cgtRate" class="form-label">Capital Gains Tax Rate (%) — editable</label><input type="number" class="form-control" id="cgtRate" value="15" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Capital Gain</div><div class="fs-5 fw-bold" id="cgtGain">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Taxable Gain (after expenses)</div><div class="fs-5 fw-bold" id="cgtTaxable">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Estimated Tax</div><div class="fs-5 fw-bold" id="cgtTax">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Proceeds After Tax</div><div class="fs-5 fw-bold" id="cgtNet">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the purchase price and selling price.</li>
                        <li>Enter selling expenses such as commission and transfer fees.</li>
                        <li>Enter the CGT rate that applies to your asset and holding period.</li>
                        <li>See the tax and the net proceeds after tax.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">CGT rates in Pakistan depend on the asset type and holding period and change with the budget. Rates change — verify with the official source before relying on this.</p>
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
    var buy=v("cgtBuy"), sell=v("cgtSell"), exp=v("cgtExp"), rate=v("cgtRate");
    if(sell<=0||buy<0){msg("Enter the purchase and selling prices.");return;}
    msg(""); var gain=sell-buy; var taxable=Math.max(gain-exp,0); var tax=taxable*rate/100;
    out("cgtGain",fmt(gain));out("cgtTaxable",fmt(taxable));out("cgtTax",fmt(tax));out("cgtNet",fmt(sell-exp-tax));
    if(gain<=0)msg("Selling price is not above purchase price, so there is no capital gain to tax.");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
