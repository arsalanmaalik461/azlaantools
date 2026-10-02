@extends('layouts.app')

@section('title', 'Committee BC Calculator — Free Online Tool')
@section('meta_description', 'Enter monthly committee amount, members and your number to see the total amount, payout and profit or loss.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Committee BC Calculator</h1>
            <p class="lead small text-muted">Full savings committee (BC) calculation — monthly share, members, your number, and the loss if you took the committee on a bid. It also shows the present value using a monthly profit rate for the time value of money.</p>
            <div class="row g-3">
<div class="col-md-4"><label for="cmMonthly" class="form-label">Monthly Share Per Member (Rs)</label><input type="number" class="form-control" id="cmMonthly" value="10000" step="any"></div>
<div class="col-md-4"><label for="cmMembers" class="form-label">Total Members</label><input type="number" class="form-control" id="cmMembers" value="20" step="1"></div>
<div class="col-md-4"><label for="cmNumber" class="form-label">Your Number (which month you receive)</label><input type="number" class="form-control" id="cmNumber" value="5" step="1"></div>
<div class="col-md-4"><label for="cmBid" class="form-label">Bid Discount on Payout (%)</label><input type="number" class="form-control" id="cmBid" value="0" step="any"></div>
<div class="col-md-4"><label for="cmRate" class="form-label">Monthly Profit Rate for Time Value (%) — editable</label><input type="number" class="form-control" id="cmRate" value="1" step="any"></div>
            </div>
            <div id="msg" class="d-none"></div>
            <div class="row g-3 mt-2">
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Total Pool (one month collection)</div><div class="fs-5 fw-bold" id="cmPool">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Your Total Contributions (full cycle)</div><div class="fs-5 fw-bold" id="cmPaid">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Payout You Receive (after bid)</div><div class="fs-5 fw-bold" id="cmReceive">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Cash Profit / Loss (received − paid)</div><div class="fs-5 fw-bold" id="cmNet">—</div></div></div>
<div class="col-md-4"><div class="border rounded p-3 text-center h-100"><div class="text-muted small">Present Value Benefit (time value adjusted)</div><div class="fs-5 fw-bold" id="cmPv">—</div></div></div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                        <li>Enter each member's monthly share and the total members.</li>
                        <li>Write your number — in which month you get the committee.</li>
                        <li>If you took the committee early on a bid, write the bid discount percentage.</li>
                        <li>See the total payment, the amount you receive, and the profit or loss. The present value line also counts the value of time — the benefit of an early number shows there.</li>
            </ol>
            <h3 class="h6 mt-3">Note</h3>
            <p class="small text-muted mb-0">In cash terms without a bid, the total paid and received are equal; the real difference comes from the timing of your number and the bid discount, which the present value line shows. Committee rules can be different in every group.</p>
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
    var share=v("cmMonthly"), members=Math.round(v("cmMembers")), num=Math.round(v("cmNumber")), bid=v("cmBid"), rate=v("cmRate")/100;
    if(share<=0||members<=0){msg("Enter the monthly share and number of members.");return;}
    if(num<1||num>members){msg("Your number must be between 1 and the total members.");return;}
    msg(""); var pool=share*members; var paid=share*members; var receive=pool*(1-bid/100);
    var pvPaid=0; for(var m=1;m<=members;m++){pvPaid+=share/Math.pow(1+rate,m);}
    var pvReceive=receive/Math.pow(1+rate,num);
    out("cmPool",fmt(pool));out("cmPaid",fmt(paid));out("cmReceive",fmt(receive));out("cmNet",fmt(receive-paid));out("cmPv",fmt(pvReceive-pvPaid));
}
    document.querySelectorAll("input, select").forEach(function (e) { e.addEventListener("input", calc); e.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
