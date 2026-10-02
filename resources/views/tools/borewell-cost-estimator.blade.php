@extends('layouts.app')

@section('title', 'Borewell Cost Estimator — Free Online Tool')
@section('meta_description', 'Enter the bore depth and per foot rate to get the total bore cost, including pump and pipe.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Borewell Cost Estimator</h1>
                    <p class="lead small text-muted">Enter the bore depth and your area's per foot drilling rate — get the total cost including casing pipe, submersible pump, and labour.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="bwDepth">Bore depth (feet)</label><input type="number" class="form-control" id="bwDepth" value="150" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwRate">Drilling rate per foot (Rs, editable)</label><input type="number" class="form-control" id="bwRate" value="350" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwCasingRate">Casing pipe per foot (Rs, editable)</label><input type="number" class="form-control" id="bwCasingRate" value="250" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwCasingLen">Casing pipe length (feet)</label><input type="number" class="form-control" id="bwCasingLen" value="60" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwPump">Submersible pump / motor cost (Rs)</label><input type="number" class="form-control" id="bwPump" value="45000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwPipe">Delivery pipe, cable and fittings (Rs)</label><input type="number" class="form-control" id="bwPipe" value="15000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwLabour">Labour and installation (Rs)</label><input type="number" class="form-control" id="bwLabour" value="10000" min="0" step="any"></div>
                        <div class="col-md-4"><label class="form-label" for="bwMisc">Miscellaneous (Rs)</label><input type="number" class="form-control" id="bwMisc" value="5000" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="bwOut">Enter the values — the total cost will show here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Write the required bore depth in feet and enter your contractor's per foot rate.</li>
                        <li>Adjust the casing pipe length, pump, pipe/cable, labour, and miscellaneous costs as per your estimate.</li>
                        <li>See the cost of each part and the grand total below, along with the effective cost per foot.</li>
                    </ol>
                    <p class="small text-muted mb-0">Drilling rates vary a lot by area, soil hardness (rocky or soft), and bore diameter — always confirm rates with your contractor before finalizing. This is only an estimate; the actual water depth depends on the survey.</p>
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
        var depth = num("bwDepth");
        var drilling = depth * num("bwRate");
        var casing = num("bwCasingLen") * num("bwCasingRate");
        var total = drilling + casing + num("bwPump") + num("bwPipe") + num("bwLabour") + num("bwMisc");
        var out = el("bwOut");
        if (depth <= 0) { out.textContent = "Please enter the bore depth correctly."; return; }
        out.innerHTML = "<strong>Estimated total cost: " + money(total) + "</strong><br>Drilling: " + money(drilling) + " &nbsp; Casing pipe: " + money(casing) + " &nbsp; Pump: " + money(num("bwPump")) + " &nbsp; Pipe/cable: " + money(num("bwPipe")) + " &nbsp; Labour: " + money(num("bwLabour")) + " &nbsp; Misc: " + money(num("bwMisc")) + "<br>Effective cost per foot of depth: " + money(total / depth);
    }
    ["bwDepth", "bwRate", "bwCasingRate", "bwCasingLen", "bwPump", "bwPipe", "bwLabour", "bwMisc"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
