@extends('layouts.app')

@section('title', 'Yarn Count Converter — Free Online Tool')
@section('meta_description', 'Enter the yarn count and convert instantly to tex, denier and English cotton count Ne.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Yarn Count Converter</h1>
            <p class="lead small text-muted mb-4">Enter the yarn count and convert instantly to tex, denier and English cotton count Ne.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="ycVal" class="form-label fw-semibold">Count Value</label><input type="number" class="form-control" id="ycVal" value="30" step="any"></div>
                        <div class="col-md-6 mb-3"><label for="ycSys" class="form-label fw-semibold">System</label>
                            <select class="form-select" id="ycSys"><option value="ne" selected>English Cotton Count (Ne)</option><option value="tex">Tex</option><option value="denier">Denier</option><option value="nm">Metric Count (Nm)</option></select></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="ycOut">—</div>
                        <div class="small" id="ycDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live at once — no button needed.</li>
                        <li>Change the value or unit and the new result appears by itself.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">Relations: Tex = 590.54 / Ne, Denier = Tex x 9, Nm = 1000 / Tex. In Ne and Nm a higher value means finer yarn, while in Tex and Denier a higher value means thicker yarn.</p>
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
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    function calc() {
        var v = parseFloat(document.getElementById("ycVal").value);
        var sys = document.getElementById("ycSys").value;
        var o = document.getElementById("ycOut"), det = document.getElementById("ycDetail");
        if (isNaN(v) || v <= 0) { o.textContent = "—"; det.textContent = "Enter a valid count."; return; }
        var tex;
        if (sys === "tex") { tex = v; }
        else if (sys === "denier") { tex = v / 9; }
        else if (sys === "ne") { tex = 590.54 / v; }
        else { tex = 1000 / v; }
        var ne = 590.54 / tex, denier = tex * 9, nm = 1000 / tex;
        o.textContent = "Tex: " + fmt(tex) + " — Denier: " + fmt(denier);
        det.textContent = "English Cotton Count (Ne): " + fmt(ne) + " — Metric Count (Nm): " + fmt(nm) + " — Decitex (dtex): " + fmt(tex * 10);
    }
    document.getElementById("ycVal").addEventListener("input", calc);
    document.getElementById("ycSys").addEventListener("change", calc);
    calc();
})();
</script>
@endsection
