@extends('layouts.app')

@section('title', 'Net Worth Calculator — Free Online Tool')
@section('meta_description', 'Add all assets and subtract all liabilities to calculate personal net worth with an asset breakdown chart')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Net Worth Calculator</h1>
            <p class="lead small text-muted">Find your net worth — add up all your assets, subtract all your debts, and see in the breakdown where your wealth is held.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="nwCash" class="form-label">Cash + Bank Balances (Rs)</label><input type="number" class="form-control" id="nwCash" value="200000" step="any"></div>
<div class="col-md-4"><label for="nwProperty" class="form-label">Property / Plots (Rs)</label><input type="number" class="form-control" id="nwProperty" value="8000000" step="any"></div>
<div class="col-md-4"><label for="nwVehicle" class="form-label">Vehicles (Rs)</label><input type="number" class="form-control" id="nwVehicle" value="2500000" step="any"></div>
<div class="col-md-4"><label for="nwGold" class="form-label">Gold / Jewellery (Rs)</label><input type="number" class="form-control" id="nwGold" value="1000000" step="any"></div>
<div class="col-md-4"><label for="nwInvest" class="form-label">Investments / Funds / Shares (Rs)</label><input type="number" class="form-control" id="nwInvest" value="500000" step="any"></div>
<div class="col-md-4"><label for="nwOtherA" class="form-label">Other Assets (Rs)</label><input type="number" class="form-control" id="nwOtherA" value="0" step="any"></div>
<div class="col-md-4"><label for="nwHome" class="form-label">Home Finance / Mortgage (Rs)</label><input type="number" class="form-control" id="nwHome" value="3000000" step="any"></div>
<div class="col-md-4"><label for="nwCar" class="form-label">Car Loan (Rs)</label><input type="number" class="form-control" id="nwCar" value="800000" step="any"></div>
<div class="col-md-4"><label for="nwCards" class="form-label">Credit Card Balances (Rs)</label><input type="number" class="form-control" id="nwCards" value="50000" step="any"></div>
<div class="col-md-4"><label for="nwPersonal" class="form-label">Personal Loans (Rs)</label><input type="number" class="form-control" id="nwPersonal" value="0" step="any"></div>
<div class="col-md-4"><label for="nwOtherL" class="form-label">Other Liabilities (Rs)</label><input type="number" class="form-control" id="nwOtherL" value="0" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Assets</div><div class="fs-5 fw-bold" id="nwAssets">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Liabilities</div><div class="fs-5 fw-bold" id="nwLiab">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Net Worth</div><div class="fs-5 fw-bold" id="nwWorth">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Debt to Asset Ratio</div><div class="fs-5 fw-bold" id="nwRatio">—</div></div></div>
            </div>
<div class="table-responsive mt-4" style="max-height:420px;overflow-y:auto;"><table class="table table-sm table-striped align-middle mb-0"><thead><tr><th>Asset</th><th>Amount</th><th>Share of Assets</th></tr></thead><tbody id="nwTable"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter the current market value of each asset — not the purchase price, but the value today.</li>
                        <li>Enter the remaining amount of each debt.</li>
                        <li>Net worth = total assets − total liabilities, and you can see each asset's share in the breakdown table.</li>
                        <li>Recalculate once a year to track your progress.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">Asset values are estimates — the price of plots and gold moves with the market. The trend of your net worth matters more than the exact figure.</p>
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
    var items=[["Cash + Bank",v("nwCash")],["Property / Plots",v("nwProperty")],["Vehicles",v("nwVehicle")],["Gold / Jewellery",v("nwGold")],["Investments",v("nwInvest")],["Other Assets",v("nwOtherA")]];
    var assets=items.reduce(function(s,x){return s+x[1];},0);
    var liab=v("nwHome")+v("nwCar")+v("nwCards")+v("nwPersonal")+v("nwOtherL");
    var rows=""; items.forEach(function(x){rows+="<tr><td>"+x[0]+"</td><td>"+fmt(x[1])+"</td><td>"+(assets>0?pct(x[1]/assets*100):"0%")+"</td></tr>";});
    html("nwTable",rows);
    if(assets<=0&&liab<=0){msg("Enter at least one asset or liability amount.");return;}
    msg("");
    out("nwAssets",fmt(assets));out("nwLiab",fmt(liab));out("nwWorth",fmt(assets-liab));out("nwRatio",assets>0?pct(liab/assets*100):"\u2014");
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
