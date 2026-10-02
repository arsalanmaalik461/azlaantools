@extends('layouts.app')

@section('title', 'Driving Licence Fee Estimator — Free Online Tool')
@section('meta_description', 'Choose a licence category and validity years, and estimate the total test and licence fees.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Driving Licence Fee Estimator</h1>
                    <p class="lead small text-muted">Choose a licence category — car, jeep, motorcycle or commercial — all fee fields are editable, so you can match the estimate to your city's actual fees.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="dlCat">Licence category</label><select class="form-select" id="dlCat"><option value="car">Car / Jeep</option><option value="bike">Motorcycle</option><option value="rickshaw">Motorcycle Rickshaw</option><option value="ltv">LTV (commercial)</option><option value="htv">HTV (commercial)</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="dlType">Application type</label><select class="form-select" id="dlType"><option value="new">New licence (learner + test)</option><option value="renewal">Renewal only</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="dlYears">Validity (years)</label><input type="number" class="form-control" id="dlYears" value="5" min="1" step="1"></div>
                        <div class="col-md-4"><label class="form-label" for="dlLearner">Learner permit fee (Rs, editable)</label><input type="number" class="form-control" id="dlLearner" value="500" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dlMedical">Medical certificate fee (Rs, editable)</label><input type="number" class="form-control" id="dlMedical" value="200" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dlTest">Test fee (Rs, editable)</label><input type="number" class="form-control" id="dlTest" value="300" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dlLicence">Licence fee for full validity (Rs, editable)</label><input type="number" class="form-control" id="dlLicence" value="1800" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dlMisc">Postage / smart card / misc (Rs, editable)</label><input type="number" class="form-control" id="dlMisc" value="480" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="dlLate">Late renewal fine (Rs, editable)</label><input type="number" class="form-control" id="dlLate" value="0" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="dlOut">Choose a category — the estimated total fee will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Choose a licence category and application type (new or renewal).</li>
                        <li>You can change each fee field to match your city's licensing office published fee — when you switch category, the common published values fill in automatically.</li>
                        <li>The total estimate appears below with a breakdown of every part.</li>
                    </ol>
                    <p class="small text-muted mb-0">Fees differ by province and city (Punjab, Sindh, KP and Islamabad have separate schedules) and keep changing over time. Rates change — verify with the official source before relying on this. All rate fields on this page are editable estimates prefilled with commonly published values, not a live official lookup.</p>
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
    var presets = {
        car: { learner: 500, medical: 200, test: 300, licence: 1800, misc: 480 },
        bike: { learner: 500, medical: 200, test: 300, licence: 1000, misc: 480 },
        rickshaw: { learner: 500, medical: 200, test: 300, licence: 1200, misc: 480 },
        ltv: { learner: 600, medical: 300, test: 400, licence: 2500, misc: 480 },
        htv: { learner: 600, medical: 300, test: 400, licence: 3000, misc: 480 }
    };
    el("dlCat").addEventListener("change", function () {
        var p = presets[el("dlCat").value];
        el("dlLearner").value = p.learner; el("dlMedical").value = p.medical; el("dlTest").value = p.test; el("dlLicence").value = p.licence; el("dlMisc").value = p.misc;
        calc();
    });
    function calc() {
        var isNew = el("dlType").value === "new";
        var learner = isNew ? num("dlLearner") : 0;
        var test = isNew ? num("dlTest") : 0;
        var total = learner + num("dlMedical") + test + num("dlLicence") + num("dlMisc") + num("dlLate");
        el("dlOut").innerHTML = "<strong>Total estimate: " + money(total) + "</strong> (" + num("dlYears") + " year validity)<br>Learner: " + money(learner) + " &nbsp; Medical: " + money(num("dlMedical")) + " &nbsp; Test: " + money(test) + " &nbsp; Licence fee: " + money(num("dlLicence")) + " &nbsp; Misc: " + money(num("dlMisc")) + " &nbsp; Late fine: " + money(num("dlLate"));
    }
    ["dlCat", "dlType", "dlYears", "dlLearner", "dlMedical", "dlTest", "dlLicence", "dlMisc", "dlLate"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
