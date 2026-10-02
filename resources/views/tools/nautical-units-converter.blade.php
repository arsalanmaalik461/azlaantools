@extends('layouts.app')

@section('title', 'Nautical Units Converter — Free Online Tool')
@section('meta_description', 'Enter sea units and convert nautical miles, knots, fathoms and cables into common units.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-2">Nautical Units Converter</h1>
            <p class="lead small text-muted mb-4">Enter sea units and convert nautical miles, knots, fathoms and cables into common units.</p>
            <h2 class="h5 mb-2">Distance Units</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="dval" class="form-label fw-semibold">Value</label>
                        <input type="number" class="form-control form-control-lg" id="dval" value="1" step="any" placeholder="Enter value">
                    </div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-5">
                            <label for="dfromU" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="dfromU"></select>
                        </div>
                        <div class="col-2 text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" id="dswapBtn" title="Swap units">&#8646;</button>
                        </div>
                        <div class="col-5">
                            <label for="dtoU" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="dtoU"></select>
                        </div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-4 fw-bold" id="dout">—</div>
                        <div class="small" id="drate"></div>
                    </div>
                </div>
            </div>
            <h2 class="h5 mt-4 mb-2">Speed Units</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="sval" class="form-label fw-semibold">Value</label>
                        <input type="number" class="form-control form-control-lg" id="sval" value="1" step="any" placeholder="Enter value">
                    </div>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-5">
                            <label for="sfromU" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="sfromU"></select>
                        </div>
                        <div class="col-2 text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" id="sswapBtn" title="Swap units">&#8646;</button>
                        </div>
                        <div class="col-5">
                            <label for="stoU" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="stoU"></select>
                        </div>
                    </div>
                    <div class="alert alert-info text-center mb-0">
                        <div class="fs-4 fw-bold" id="sout">—</div>
                        <div class="small" id="srate"></div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol class="mb-3">
                        <li>Enter your value in the boxes above or select an option.</li>
                        <li>The result updates live — you do not need to press any button.</li>
                        <li>Change a value or unit and the new result appears on its own.</li>
                    </ol>
                    <h3 class="h6">Note</h3>
                    <p class="small text-muted mb-0">1 nautical mile = 1,852 metre exact. 1 knot = 1 nautical mile per hour. Fathom (6 feet) is used to measure sea depth and cable (0.1 nautical mile) is used for distance.</p>
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
    var units = {
            nmi: { label: "Nautical Mile", factor: 1852 },
            km: { label: "Kilometre (km)", factor: 1000 },
            m: { label: "Metre (m)", factor: 1 },
            mi: { label: "Statute Mile", factor: 1609.344 },
            ft: { label: "Foot (ft)", factor: 0.3048 },
            fathom: { label: "Fathom", factor: 1.8288 },
            cable: { label: "Cable (0.1 nmi)", factor: 185.2 },
            league: { label: "Nautical League (3 nmi)", factor: 5556 }
    };
    var valEl = document.getElementById("dval");
    var fromEl = document.getElementById("dfromU");
    var toEl = document.getElementById("dtoU");
    var outEl = document.getElementById("dout");
    var rateEl = document.getElementById("drate");
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    Object.keys(units).forEach(function (k) {
        var o1 = document.createElement("option"); o1.value = k; o1.textContent = units[k].label; fromEl.appendChild(o1);
        var o2 = document.createElement("option"); o2.value = k; o2.textContent = units[k].label; toEl.appendChild(o2);
    });
    var keys = Object.keys(units);
    fromEl.value = keys[0]; toEl.value = keys[1];
    function convert() {
        var v = parseFloat(valEl.value);
        if (isNaN(v)) { outEl.textContent = "—"; rateEl.textContent = "Enter a value."; return; }
        var r = v * units[fromEl.value].factor / units[toEl.value].factor;
        outEl.textContent = fmt(v) + " " + units[fromEl.value].label + " = " + fmt(r) + " " + units[toEl.value].label;
        rateEl.textContent = "1 " + units[fromEl.value].label + " = " + fmt(units[fromEl.value].factor / units[toEl.value].factor) + " " + units[toEl.value].label;
    }
    valEl.addEventListener("input", convert);
    fromEl.addEventListener("change", convert);
    toEl.addEventListener("change", convert);
    document.getElementById("dswapBtn").addEventListener("click", function () {
        var tmp = fromEl.value; fromEl.value = toEl.value; toEl.value = tmp; convert();
    });
    convert();
})();
(function () {
    "use strict";
    var units = {
            knot: { label: "Knot (kn)", factor: 0.514444444 },
            kmh: { label: "Kilometre per Hour (km/h)", factor: 0.277777778 },
            mph: { label: "Miles per Hour (mph)", factor: 0.44704 },
            ms: { label: "Metre per Second (m/s)", factor: 1 }
    };
    var valEl = document.getElementById("sval");
    var fromEl = document.getElementById("sfromU");
    var toEl = document.getElementById("stoU");
    var outEl = document.getElementById("sout");
    var rateEl = document.getElementById("srate");
    function fmt(n) {
        if (n === null || n === undefined || !isFinite(n)) return "—";
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString("en-US", { maximumFractionDigits: 8 });
    }
    Object.keys(units).forEach(function (k) {
        var o1 = document.createElement("option"); o1.value = k; o1.textContent = units[k].label; fromEl.appendChild(o1);
        var o2 = document.createElement("option"); o2.value = k; o2.textContent = units[k].label; toEl.appendChild(o2);
    });
    var keys = Object.keys(units);
    fromEl.value = keys[0]; toEl.value = keys[1];
    function convert() {
        var v = parseFloat(valEl.value);
        if (isNaN(v)) { outEl.textContent = "—"; rateEl.textContent = "Enter a value."; return; }
        var r = v * units[fromEl.value].factor / units[toEl.value].factor;
        outEl.textContent = fmt(v) + " " + units[fromEl.value].label + " = " + fmt(r) + " " + units[toEl.value].label;
        rateEl.textContent = "1 " + units[fromEl.value].label + " = " + fmt(units[fromEl.value].factor / units[toEl.value].factor) + " " + units[toEl.value].label;
    }
    valEl.addEventListener("input", convert);
    fromEl.addEventListener("change", convert);
    toEl.addEventListener("change", convert);
    document.getElementById("sswapBtn").addEventListener("click", function () {
        var tmp = fromEl.value; fromEl.value = toEl.value; toEl.value = tmp; convert();
    });
    convert();
})();
</script>
@endsection
