@extends('layouts.app')
@section('title', 'OG Image Generator — Free Online Tool')
@section('meta_description', 'Design Open Graph social share images with text logo and colors')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">OG Image Generator</h1>
            <p class="lead small text-muted">Design a 1200 by 630 Open Graph share image with your title, subtitle, brand name and colors, then download the PNG.</p>

                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label" for="ogTitle">Title</label><input type="text" id="ogTitle" class="form-control" value="How to Save Money on Your Electricity Bill"></div>
                        <div class="col-md-4"><label class="form-label" for="ogBrand">Brand / site name</label><input type="text" id="ogBrand" class="form-control" value="arslanmalik.tech"></div>
                        <div class="col-md-8"><label class="form-label" for="ogSub">Subtitle</label><input type="text" id="ogSub" class="form-control" value="Simple steps anyone in Pakistan can follow today"></div>
                        <div class="col-md-4"><label class="form-label" for="ogLayout">Layout</label><select id="ogLayout" class="form-select"><option value="left">Left aligned</option><option value="center">Centered</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="ogBg">Background</label><input type="color" id="ogBg" class="form-control form-control-color w-100" value="#0f5132"></div>
                        <div class="col-md-4"><label class="form-label" for="ogFg">Text color</label><input type="color" id="ogFg" class="form-control form-control-color w-100" value="#ffffff"></div>
                        <div class="col-md-4"><label class="form-label" for="ogAccent">Accent color</label><input type="color" id="ogAccent" class="form-control form-control-color w-100" value="#ffc107"></div>
                    </div>
                    <div class="text-center mt-3"><canvas id="ogCanvas" width="1200" height="630" class="img-fluid border rounded"></canvas></div>
                    <button type="button" id="ogGo" class="btn btn-primary btn-lg w-100 mt-3">Download OG Image (1200 x 630 PNG)</button>

            <div id="ogMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Type your post title, subtitle and brand name.</li>
                    <li>Pick background, text and accent colors and a layout.</li>
                    <li>Download the PNG and set it as the og:image for your post.</li>
            </ol>
            <p class="small text-muted mb-0">1200 by 630 pixels is the standard recommended Open Graph size used by Facebook, LinkedIn, WhatsApp and X link previews.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ogMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    function wrap(ctx, text, maxW) {
        var words = String(text).split(/\s+/).filter(Boolean), lines = [], line = "";
        words.forEach(function (w) { var test = line ? line + " " + w : w; if (ctx.measureText(test).width > maxW && line) { lines.push(line); line = w; } else line = test; });
        if (line) lines.push(line); return lines.slice(0, 4);
    }
    function draw() {
        var cv = document.getElementById("ogCanvas"), ctx = cv.getContext("2d");
        var bg = document.getElementById("ogBg").value, fg = document.getElementById("ogFg").value, ac = document.getElementById("ogAccent").value;
        var center = document.getElementById("ogLayout").value === "center";
        ctx.fillStyle = bg; ctx.fillRect(0, 0, 1200, 630);
        ctx.fillStyle = ac; ctx.fillRect(0, 0, 1200, 14); ctx.fillRect(0, 616, 1200, 14);
        var brand = document.getElementById("ogBrand").value.trim();
        ctx.textAlign = center ? "center" : "left";
        var x = center ? 600 : 80;
        if (brand) {
            ctx.beginPath(); ctx.arc(center ? 600 : 118, 108, 38, 0, Math.PI * 2); ctx.fillStyle = ac; ctx.fill();
            ctx.fillStyle = bg; ctx.font = "bold 44px Arial"; ctx.textAlign = "center"; ctx.fillText(brand.charAt(0).toUpperCase(), center ? 600 : 118, 124);
            ctx.fillStyle = fg; ctx.font = "bold 30px Arial"; ctx.textAlign = center ? "center" : "left";
            ctx.fillText(brand, center ? 600 : 176, center ? 180 : 120);
        }
        ctx.fillStyle = fg; ctx.font = "bold 62px Arial"; ctx.textAlign = center ? "center" : "left";
        var lines = wrap(ctx, document.getElementById("ogTitle").value || "Your title here", 1040), y = 250;
        lines.forEach(function (ln) { ctx.fillText(ln, x, y); y += 76; });
        ctx.font = "30px Arial"; ctx.fillStyle = ac;
        wrap(ctx, document.getElementById("ogSub").value, 1040).slice(0, 2).forEach(function (ln) { ctx.fillText(ln, x, y + 16); y += 42; });
    }
    ["ogTitle", "ogSub", "ogBrand", "ogLayout", "ogBg", "ogFg", "ogAccent"].forEach(function (id) { document.getElementById(id).addEventListener("input", draw); document.getElementById(id).addEventListener("change", draw); });
    document.getElementById("ogGo").addEventListener("click", function () { draw(); document.getElementById("ogCanvas").toBlob(function (b) { if (b) { dl(b, "og-image.png"); showMsg("OG image downloaded at 1200 x 630."); } }, "image/png"); });
    draw();

})();
</script>
@endsection
