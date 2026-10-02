@extends('layouts.app')
@section('title', 'Color Palette Generator — Free Online Tool')
@section('meta_description', 'Generate harmonious color palettes from a base color in one click')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Color Palette Generator</h1>
            <p class="lead small text-muted">Pick any base color, choose a harmony rule, and get a ready palette with HEX codes you can copy in one click.</p>

                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="cpBase">Base color</label><input type="color" id="cpBase" class="form-control form-control-color w-100" value="#0d6efd"></div>
                        <div class="col-md-4"><label class="form-label" for="cpMode">Harmony</label><select id="cpMode" class="form-select"><option value="complementary">Complementary</option><option value="analogous">Analogous</option><option value="triadic">Triadic</option><option value="tetradic">Tetradic (square)</option><option value="split">Split complementary</option><option value="mono">Monochromatic shades</option></select></div>
                        <div class="col-md-4 d-flex align-items-end gap-2"><button type="button" id="cpGo" class="btn btn-primary flex-grow-1">Generate Palette</button><button type="button" id="cpRandom" class="btn btn-outline-secondary">Random</button></div>
                    </div>
                    <div id="cpOut" class="row g-3 mt-2"></div>
                    <p class="small text-muted mt-2 mb-0">Click any swatch to copy its HEX code.</p>

            <div id="cpMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Pick a base color or press Random.</li>
                    <li>Choose a harmony rule such as complementary or triadic.</li>
                    <li>Click any color swatch to copy its HEX code for your design tool.</li>
            </ol>
            <p class="small text-muted mb-0">Palettes are built with HSL harmony math in your browser. Monochromatic mode varies lightness of the same hue from dark to light.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("cpMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function hexToHsl(hex) {
        var r = parseInt(hex.slice(1, 3), 16) / 255, g = parseInt(hex.slice(3, 5), 16) / 255, b = parseInt(hex.slice(5, 7), 16) / 255;
        var mx = Math.max(r, g, b), mn = Math.min(r, g, b), h = 0, s = 0, l = (mx + mn) / 2;
        if (mx !== mn) { var d = mx - mn; s = l > 0.5 ? d / (2 - mx - mn) : d / (mx + mn);
            if (mx === r) h = (g - b) / d + (g < b ? 6 : 0); else if (mx === g) h = (b - r) / d + 2; else h = (r - g) / d + 4; h *= 60; }
        return [h, s * 100, l * 100];
    }
    function hslToHex(h, s, l) {
        h = ((h % 360) + 360) % 360; s = Math.max(0, Math.min(100, s)) / 100; l = Math.max(0, Math.min(100, l)) / 100;
        var c = (1 - Math.abs(2 * l - 1)) * s, x = c * (1 - Math.abs((h / 60) % 2 - 1)), m = l - c / 2, r = 0, g = 0, b = 0;
        if (h < 60) { r = c; g = x; } else if (h < 120) { r = x; g = c; } else if (h < 180) { g = c; b = x; } else if (h < 240) { g = x; b = c; } else if (h < 300) { r = x; b = c; } else { r = c; b = x; }
        function hx(v) { var n = Math.round((v + m) * 255).toString(16); return n.length === 1 ? "0" + n : n; }
        return ("#" + hx(r) + hx(g) + hx(b)).toUpperCase();
    }
    function render() {
        var base = document.getElementById("cpBase").value, mode = document.getElementById("cpMode").value;
        var hsl = hexToHsl(base), h = hsl[0], sat = hsl[1], li = hsl[2], cols = [];
        if (mode === "complementary") cols = [[h, sat, li], [h + 180, sat, li], [h, sat * 0.6, li + 18], [h + 180, sat * 0.6, li - 14], [h, sat * 0.35, 88]];
        else if (mode === "analogous") cols = [[h - 40, sat, li], [h - 20, sat, li], [h, sat, li], [h + 20, sat, li], [h + 40, sat, li]];
        else if (mode === "triadic") cols = [[h, sat, li], [h + 120, sat, li], [h + 240, sat, li], [h, sat * 0.5, li + 20], [h + 120, sat * 0.5, li - 16]];
        else if (mode === "tetradic") cols = [[h, sat, li], [h + 90, sat, li], [h + 180, sat, li], [h + 270, sat, li], [h, sat * 0.4, 88]];
        else if (mode === "split") cols = [[h, sat, li], [h + 150, sat, li], [h + 210, sat, li], [h, sat * 0.55, li + 18], [h + 180, sat * 0.45, li - 12]];
        else cols = [[h, sat, 14], [h, sat, 30], [h, sat, 46], [h, sat, 64], [h, sat, 84]];
        var out = document.getElementById("cpOut"); out.innerHTML = "";
        cols.forEach(function (c) {
            var hex = hslToHex(c[0], c[1], c[2]);
            var col = document.createElement("div"); col.className = "col-6 col-md";
            var box = document.createElement("button"); box.type = "button"; box.className = "btn w-100 p-0 border rounded overflow-hidden";
            var sw = document.createElement("div"); sw.style.height = "90px"; sw.style.background = hex;
            var lb = document.createElement("div"); lb.className = "py-2 fw-semibold small"; lb.textContent = hex;
            box.appendChild(sw); box.appendChild(lb);
            box.addEventListener("click", function () { if (navigator.clipboard) navigator.clipboard.writeText(hex); showMsg("Copied " + hex + " to clipboard."); });
            col.appendChild(box); out.appendChild(col);
        });
    }
    document.getElementById("cpGo").addEventListener("click", render);
    document.getElementById("cpBase").addEventListener("input", render);
    document.getElementById("cpMode").addEventListener("change", render);
    document.getElementById("cpRandom").addEventListener("click", function () {
        var hex = hslToHex(Math.floor(Math.random() * 360), 55 + Math.random() * 35, 38 + Math.random() * 22);
        document.getElementById("cpBase").value = hex; render();
    });
    render();

})();
</script>
@endsection
