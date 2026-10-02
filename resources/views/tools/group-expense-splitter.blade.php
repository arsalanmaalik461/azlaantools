@extends('layouts.app')

@section('title', 'Group Expense Splitter — Free Online Tool')
@section('meta_description', 'Split shared group costs fairly and see who owes whom after a trip or dinner.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Group Expense Splitter</h1>
            <p class="lead small text-muted">Split the shared costs of a trip or dinner fairly — enter the amount each person paid, and the settlement (who pays whom) will be calculated automatically.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-muted small">Enter each person name and how many Rs they spent. The calculator finds the average and shows who owes whom how much.</p>
                    <div id="grRows"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="grAdd">+ Add person</button>
                    <div id="result" class="mt-3"></div>
                    <div id="steps" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter each person name and their expense (add more people with the + button).</li><li>The total, per-person share and each person balance will be shown.</li><li>At the end the settlement list will show who owes whom how many Rs.</li></ol>
                    <p class="small text-muted mb-0">Note: This assumes an equal split — everyone had an equal share of everything. If someone did not use something, adjust that part separately.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    var el = function (id) { return document.getElementById(id); };
    function fmt(n, d) { if (typeof d === "undefined") { d = 6; } if (!isFinite(n)) { return "—"; } return Number(n.toFixed(d)).toLocaleString("en-US", { maximumFractionDigits: d }); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? null : v; }
    function intv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) ? null : v; }
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = b; b = a % b; a = t; } return a || 1; }
    function show(html) { el("result").innerHTML = html; }
    function showSteps(arr) { el("steps").innerHTML = arr.length ? "<h2 class=\"h6\">Steps / Breakdown</h2><ol>" + arr.map(function (s) { return "<li>" + s + "</li>"; }).join("") + "</ol>" : ""; }
    function err(msg) { show("<div class=\"alert alert-warning mb-0\">" + msg + "</div>"); showSteps([]); }
    function bind(fn) { document.querySelectorAll(".inp").forEach(function (i) { i.addEventListener("input", fn); i.addEventListener("change", fn); }); }

    function grRow(name, amt) {
        var div = document.createElement("div"); div.className = "row g-2 mt-1 gr-row";
        div.innerHTML = "<div class=\"col-md-6\"><input type=\"text\" class=\"form-control gr-name\" placeholder=\"Name\" value=\"" + name + "\"></div><div class=\"col-md-4\"><input type=\"number\" class=\"form-control gr-amt\" placeholder=\"Paid Rs\" step=\"any\" value=\"" + amt + "\"></div><div class=\"col-md-2\"><button type=\"button\" class=\"btn btn-outline-danger w-100 gr-del\">Remove</button></div>";
        el("grRows").appendChild(div);
        div.querySelectorAll("input").forEach(function (i) { i.addEventListener("input", calc); });
        div.querySelector(".gr-del").addEventListener("click", function () { div.remove(); calc(); });
    }
    function calc() {
        var names = document.querySelectorAll(".gr-name"), amts = document.querySelectorAll(".gr-amt");
        var people = [];
        names.forEach(function (nm, i) { var a = parseFloat(amts[i].value); people.push({ n: nm.value.trim() || ("Person " + (i + 1)), paid: isNaN(a) ? 0 : a }); });
        people = people.filter(function (p) { return p.paid > 0 || p.n; });
        if (people.length < 2) { err("Enter data for at least 2 people."); return; }
        var total = people.reduce(function (s, p) { return s + p.paid; }, 0);
        if (total <= 0) { err("Enter amounts to calculate the split."); return; }
        var share = total / people.length;
        var bals = people.map(function (p) { return { n: p.n, bal: p.paid - share, paid: p.paid }; });
        var rows = bals.map(function (b) { return "<tr><td>" + b.n + "</td><td>Rs " + fmt(b.paid, 2) + "</td><td>Rs " + fmt(share, 2) + "</td><td>" + (b.bal >= 0 ? "<span class=\"text-success\">to receive Rs " + fmt(b.bal, 2) + "</span>" : "<span class=\"text-danger\">to pay Rs " + fmt(-b.bal, 2) + "</span>") + "</td></tr>"; }).join("");
        var debtors = bals.filter(function (b) { return b.bal < -0.005; }).map(function (b) { return { n: b.n, amt: -b.bal }; });
        var creditors = bals.filter(function (b) { return b.bal > 0.005; }).map(function (b) { return { n: b.n, amt: b.bal }; });
        var settle = [], di = 0, ci = 0;
        while (di < debtors.length && ci < creditors.length) { var pay = Math.min(debtors[di].amt, creditors[ci].amt); settle.push("<li><strong>" + debtors[di].n + "</strong> → <strong>" + creditors[ci].n + "</strong>: Rs " + fmt(pay, 2) + "</li>"); debtors[di].amt -= pay; creditors[ci].amt -= pay; if (debtors[di].amt < 0.005) { di++; } if (creditors[ci].amt < 0.005) { ci++; } }
        show("<div class=\"table-responsive\"><table class=\"table table-sm\"><thead><tr><th>Person</th><th>Paid</th><th>Fair share</th><th>Balance</th></tr></thead><tbody>" + rows + "</tbody></table></div><p class=\"mb-0\">Total = <strong>Rs " + fmt(total, 2) + "</strong> &nbsp;|&nbsp; Per person = <strong>Rs " + fmt(share, 2) + "</strong></p>");
        el("steps").innerHTML = "<h2 class=\"h6\">Settlement — who owes whom</h2>" + (settle.length ? "<ul>" + settle.join("") + "</ul>" : "<p class=\"text-muted mb-0\">Everyone is settled — no settlement needed.</p>");
    }
    el("grAdd").addEventListener("click", function () { grRow("", ""); });
    grRow("", ""); grRow("", ""); grRow("", ""); calc();

})();
</script>
@endsection
