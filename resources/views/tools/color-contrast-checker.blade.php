@extends('layouts.app')
@section('title', 'Color Contrast Checker — Free Online Tool')
@section('meta_description', 'Check WCAG color contrast ratios for accessible text and backgrounds')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Color Contrast Checker</h1>
            <p class="lead small text-muted">Enter a text color and a background color to get the WCAG contrast ratio and clear AA and AAA pass or fail results.</p>

                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="ccFg">Text color</label><div class="input-group"><input type="color" id="ccFg" class="form-control form-control-color" value="#111111"><input type="text" id="ccFgHex" class="form-control font-monospace" value="#111111"></div></div>
                        <div class="col-md-6"><label class="form-label" for="ccBg">Background color</label><div class="input-group"><input type="color" id="ccBg" class="form-control form-control-color" value="#ffffff"><input type="text" id="ccBgHex" class="form-control font-monospace" value="#ffffff"></div></div>
                    </div>
                    <div class="d-flex gap-2 mt-3"><button type="button" id="ccSwap" class="btn btn-outline-secondary">Swap Colors</button></div>
                    <div id="ccPreview" class="rounded p-4 mt-3 border"><div class="fs-4 fw-bold">Large sample heading</div><div>Normal sample text. The quick brown fox jumps over the lazy dog, shown exactly in your chosen colors.</div></div>
                    <div class="text-center mt-3"><span class="fs-2 fw-bold" id="ccRatio">-</span><div class="text-muted small">Contrast ratio</div></div>
                    <div class="row g-3 mt-1 text-center">
                        <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="fw-bold" id="ccAaN">-</div><div class="small text-muted">AA normal text (4.5)</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="fw-bold" id="ccAaL">-</div><div class="small text-muted">AA large text (3.0)</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="fw-bold" id="ccAaaN">-</div><div class="small text-muted">AAA normal text (7.0)</div></div></div>
                        <div class="col-6 col-md-3"><div class="border rounded p-3"><div class="fw-bold" id="ccAaaL">-</div><div class="small text-muted">AAA large text (4.5)</div></div></div>
                    </div>

            <div id="ccMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Pick or type the text color and the background color.</li>
                    <li>Read the contrast ratio and the four WCAG pass or fail boxes.</li>
                    <li>Adjust colors until your text size passes the level you need.</li>
            </ol>
            <p class="small text-muted mb-0">Ratios use the WCAG 2 relative luminance formula. Large text means at least 24 px, or 19 px bold. This is a guide, not a full accessibility audit.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ccMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function parseHex(v) {
        v = String(v).trim(); if (v.charAt(0) !== "#") v = "#" + v;
        if (/^#[0-9a-fA-F]{3}$/.test(v)) v = "#" + v[1] + v[1] + v[2] + v[2] + v[3] + v[3];
        if (!/^#[0-9a-fA-F]{6}$/.test(v)) return null;
        return [parseInt(v.slice(1, 3), 16), parseInt(v.slice(3, 5), 16), parseInt(v.slice(5, 7), 16)];
    }
    function lum(rgb) {
        function f(c) { c = c / 255; return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); }
        return 0.2126 * f(rgb[0]) + 0.7152 * f(rgb[1]) + 0.0722 * f(rgb[2]);
    }
    function badge(id, pass) { var el = document.getElementById(id); el.textContent = pass ? "PASS" : "FAIL"; el.className = "fw-bold " + (pass ? "text-success" : "text-danger"); }
    function update() {
        var fg = document.getElementById("ccFg").value, bg = document.getElementById("ccBg").value;
        document.getElementById("ccFgHex").value = fg.toUpperCase(); document.getElementById("ccBgHex").value = bg.toUpperCase();
        var pv = document.getElementById("ccPreview"); pv.style.background = bg; pv.style.color = fg;
        var f = parseHex(fg), b = parseHex(bg); if (!f || !b) return;
        var l1 = lum(f), l2 = lum(b), ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
        document.getElementById("ccRatio").textContent = ratio.toFixed(2) + " : 1";
        badge("ccAaN", ratio >= 4.5); badge("ccAaL", ratio >= 3); badge("ccAaaN", ratio >= 7); badge("ccAaaL", ratio >= 4.5);
    }
    function syncFromHex(which) {
        var inp = document.getElementById(which === "fg" ? "ccFgHex" : "ccBgHex");
        var rgb = parseHex(inp.value);
        if (!rgb) { showMsg("Enter a valid HEX color like #1A2B3C.", false); return; }
        function h(v) { var n = v.toString(16); return n.length === 1 ? "0" + n : n; }
        document.getElementById(which === "fg" ? "ccFg" : "ccBg").value = "#" + h(rgb[0]) + h(rgb[1]) + h(rgb[2]);
        var fg = document.getElementById("ccFg").value, bg = document.getElementById("ccBg").value, pv = document.getElementById("ccPreview");
        pv.style.background = bg; pv.style.color = fg;
        var f = parseHex(fg), b = parseHex(bg), l1 = lum(f), l2 = lum(b), ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
        document.getElementById("ccRatio").textContent = ratio.toFixed(2) + " : 1";
        badge("ccAaN", ratio >= 4.5); badge("ccAaL", ratio >= 3); badge("ccAaaN", ratio >= 7); badge("ccAaaL", ratio >= 4.5);
        showMsg("Contrast ratio updated.");
    }
    document.getElementById("ccFg").addEventListener("input", update);
    document.getElementById("ccBg").addEventListener("input", update);
    document.getElementById("ccFgHex").addEventListener("change", function () { syncFromHex("fg"); });
    document.getElementById("ccBgHex").addEventListener("change", function () { syncFromHex("bg"); });
    document.getElementById("ccSwap").addEventListener("click", function () {
        var a = document.getElementById("ccFg").value; document.getElementById("ccFg").value = document.getElementById("ccBg").value; document.getElementById("ccBg").value = a; update();
    });
    update();

})();
</script>
@endsection
