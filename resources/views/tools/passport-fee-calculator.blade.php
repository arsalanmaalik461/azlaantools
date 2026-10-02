@extends('layouts.app')

@section('title', 'Passport Fee Calculator — Free Online Tool')
@section('meta_description', 'Choose pages, years and Normal, Urgent or Executive type to estimate the passport fee.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Passport Fee Calculator</h1>
                    <p class="lead small text-muted">Choose the passport pages (36, 72, 108), validity (5 or 10 years) and service type (Normal, Urgent, Executive) — every rate in the fee table is editable.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ppPages">Pages</label><select class="form-select" id="ppPages"><option value="0">36 pages</option><option value="1">72 pages</option><option value="2">108 pages</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="ppYears">Validity</label><select class="form-select" id="ppYears"><option value="0">5 years</option><option value="1">10 years</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="ppType">Service type</label><select class="form-select" id="ppType"><option value="0">Normal</option><option value="1">Urgent</option><option value="2">Executive</option></select></div>
                    </div>
                    <h2 class="h6 mt-4">Fee table (every rate is editable)</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ppF0">36 pages — 5 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF0" value="4500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF1">36 pages — 5 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF1" value="7500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF2">36 pages — 5 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF2" value="12100" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF3">36 pages — 10 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF3" value="6700" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF4">36 pages — 10 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF4" value="11200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF5">36 pages — 10 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF5" value="16200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF6">72 pages — 5 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF6" value="8200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF7">72 pages — 5 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF7" value="13500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF8">72 pages — 5 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF8" value="19300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF9">72 pages — 10 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF9" value="12400" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF10">72 pages — 10 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF10" value="20200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF11">72 pages — 10 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF11" value="27600" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF12">108 pages — 5 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF12" value="11100" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF13">108 pages — 5 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF13" value="18000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF14">108 pages — 5 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF14" value="23700" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF15">108 pages — 10 year Normal (Rs)</label><input type="number" class="form-control pp-fee" id="ppF15" value="16600" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF16">108 pages — 10 year Urgent (Rs)</label><input type="number" class="form-control pp-fee" id="ppF16" value="27000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="ppF17">108 pages — 10 year Executive (Rs)</label><input type="number" class="form-control pp-fee" id="ppF17" value="32400" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="ppOut">Choose your selection — the fee will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose the pages, validity and Normal / Urgent / Executive type.</li>
                        <li>The selected fee appears right below — if the official fee changes, you can change that rate in the table yourself.</li>
                        <li>This fee is for a machine readable passport only; lost passport and modification fees are different.</li>
                    </ol>
                    <p class="small text-muted mb-0">The values below are taken from the commonly published official fee table and all fields are editable. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup. Official source: Directorate General of Immigration and Passports (DGIP).</p>
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
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) || v < 0 ? 0 : v; }
    function money(n) { return "Rs " + Math.round(n).toLocaleString("en-PK"); }
    function calc() {
        var idx = (parseInt(el("ppPages").value, 10) * 2 + parseInt(el("ppYears").value, 10)) * 3 + parseInt(el("ppType").value, 10);
        var fee = num("ppF" + idx);
        var label = el("ppF" + idx).previousElementSibling ? "" : "";
        el("ppOut").innerHTML = "<strong>Passport fee (your selection): " + money(fee) + "</strong><br>Pages: " + el("ppPages").options[el("ppPages").selectedIndex].text + " &nbsp; Validity: " + el("ppYears").options[el("ppYears").selectedIndex].text + " &nbsp; Type: " + el("ppType").options[el("ppType").selectedIndex].text + ". This is only an estimate — confirm the official challan rate before paying.";
    }
    ["ppPages", "ppYears", "ppType"].forEach(function (id) { el(id).addEventListener("change", calc); });
    var fees = document.querySelectorAll(".pp-fee");
    for (var i = 0; i < fees.length; i++) { fees[i].addEventListener("input", calc); }
    calc();
})();
</script>
@endsection
