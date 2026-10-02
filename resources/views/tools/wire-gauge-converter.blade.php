@extends('layouts.app')

@section('title', 'Wire Gauge Converter — Free Online Tool')
@section('meta_description', 'Enter a wire gauge and convert AWG to mm diameter, square mm and SWG instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Wire Gauge Converter</h1>
            <p class="lead small text-muted mb-4">Enter a wire gauge and convert AWG to mm diameter, square mm and SWG instantly.</p>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3"><label for="awgIn" class="form-label fw-semibold">AWG Size (write 0000 as -3, 000 as -2, 00 as -1)</label><input type="number" class="form-control" id="awgIn" value="12" step="1"></div>
                        <div class="col-md-6 mb-3"><label for="mmIn" class="form-label fw-semibold">Or enter diameter (mm)</label><input type="number" class="form-control" id="mmIn" placeholder="e.g. 2.05" step="any"></div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold" id="wgOut">—</div>
                        <div class="small" id="wgDetail"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — no need to press any button.</li>
                        <li>Change the value or unit and the new result will show automatically.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">AWG formula: diameter (mm) = 0.127 x 92^((36 - AWG)/39). SWG (British) numbers differ from AWG — the closest SWG is shown here based on diameter. Solar DC cable usually uses 4 mm2 and 6 mm2.</p>
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
    var swgTable = [[0.127, 50], [0.152, 48], [0.18, 46], [0.203, 44], [0.229, 43], [0.254, 42], [0.279, 41], [0.305, 40], [0.33, 39], [0.356, 38], [0.381, 37], [0.406, 36], [0.432, 35], [0.457, 34], [0.483, 33], [0.508, 32], [0.559, 31], [0.61, 30], [0.66, 29], [0.711, 28], [0.762, 27], [0.813, 26], [0.864, 25], [0.914, 24], [1.016, 23], [1.117, 22], [1.219, 21], [1.422, 20], [1.626, 19], [1.829, 18], [2.032, 17], [2.337, 16], [2.642, 15], [2.946, 14], [3.251, 13], [3.658, 12], [4.064, 11], [4.572, 10], [5.08, 9], [5.588, 8], [6.096, 7], [6.604, 6], [7.112, 5], [7.62, 4], [8.128, 3], [8.636, 2], [9.144, 1], [9.652, 0]];
    var awgEl = document.getElementById("awgIn"), mmEl = document.getElementById("mmIn");
    var o = document.getElementById("wgOut"), det = document.getElementById("wgDetail");
    function nearestSwg(d) {
        var best = swgTable[0], bd = 1e9;
        swgTable.forEach(function (r) { var diff = Math.abs(r[0] - d); if (diff < bd) { bd = diff; best = r; } });
        return best[1];
    }
    function showDia(d, label) {
        var area = Math.PI * d * d / 4;
        var awg = 36 - 39 * Math.log(d / 0.127) / Math.log(92);
        var res = 0.017241 / area * 1000;
        o.textContent = label + " — Diameter: " + fmt(d) + " mm (" + fmt(d / 25.4) + " inch)";
        det.textContent = "Area: " + fmt(area) + " mm2 — AWG (approx): " + fmt(Math.round(awg * 10) / 10) + " — Nearest SWG: " + nearestSwg(d) + " — Copper resistance (approx): " + fmt(res) + " ohm per km.";
    }
    function fromAwg() {
        var awg = parseFloat(awgEl.value);
        if (isNaN(awg) || awg < -3 || awg > 56) { o.textContent = "—"; det.textContent = "Enter AWG between -3 (0000) and 56."; return; }
        var d = 0.127 * Math.pow(92, (36 - awg) / 39);
        var label = awg === -3 ? "AWG 0000 (4/0)" : (awg === -2 ? "AWG 000 (3/0)" : (awg === -1 ? "AWG 00 (2/0)" : (awg === 0 ? "AWG 0 (1/0)" : "AWG " + awg)));
        showDia(d, label);
    }
    function fromMm() {
        var d = parseFloat(mmEl.value);
        if (isNaN(d) || d <= 0) { fromAwg(); return; }
        showDia(d, "Diameter " + fmt(d) + " mm");
    }
    awgEl.addEventListener("input", function () { mmEl.value = ""; fromAwg(); });
    mmEl.addEventListener("input", fromMm);
    fromAwg();
})();
</script>
@endsection
