@extends('layouts.app')
@section('title', 'Image Average Color Finder — Free Online Tool')
@section('meta_description', 'Find the average and dominant color of any uploaded image')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Image Average Color Finder</h1>
            <p class="lead small text-muted">Upload an image to get its average color and its dominant color, with HEX and RGB values ready to copy.</p>

                    <label class="form-label fw-semibold" for="ac2File">Choose an image</label>
                    <input type="file" id="ac2File" class="form-control" accept="image/*">
                    <div class="text-center mt-3"><img id="ac2Img" class="img-fluid rounded border d-none" style="max-height:260px" alt="Uploaded image preview"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><div class="border rounded p-3 text-center"><div id="ac2AvgSw" style="height:70px" class="rounded mb-2"></div><div class="fw-semibold">Average color</div><div id="ac2Avg" class="small text-muted">-</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 text-center"><div id="ac2DomSw" style="height:70px" class="rounded mb-2"></div><div class="fw-semibold">Dominant color</div><div id="ac2Dom" class="small text-muted">-</div></div></div>
                    </div>

            <div id="ac2Msg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload any image.</li>
                    <li>Read the average color and the dominant color with HEX and RGB values.</li>
                    <li>Use the colors as a background, placeholder or design starting point.</li>
            </ol>
            <p class="small text-muted mb-0">The image is sampled on a small canvas in your browser. Dominant color is found by grouping similar pixels into color buckets.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ac2Msg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function toHex(r, g, b) { function h(v) { var n = Math.round(v).toString(16); return n.length === 1 ? "0" + n : n; } return ("#" + h(r) + h(g) + h(b)).toUpperCase(); }
    document.getElementById("ac2File").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        var url = URL.createObjectURL(f), im = new Image();
        im.onload = function () {
            document.getElementById("ac2Img").src = url; document.getElementById("ac2Img").classList.remove("d-none");
            var cv = document.createElement("canvas"); var scale = Math.min(1, 120 / Math.max(im.naturalWidth, im.naturalHeight));
            cv.width = Math.max(1, Math.round(im.naturalWidth * scale)); cv.height = Math.max(1, Math.round(im.naturalHeight * scale));
            var ctx = cv.getContext("2d"); ctx.drawImage(im, 0, 0, cv.width, cv.height);
            var d; try { d = ctx.getImageData(0, 0, cv.width, cv.height).data; } catch (e) { showMsg("This image could not be sampled in the browser.", false); return; }
            var rS = 0, gS = 0, bS = 0, n = 0, buckets = {};
            for (var i = 0; i < d.length; i += 16) {
                if (d[i + 3] < 125) continue;
                rS += d[i]; gS += d[i + 1]; bS += d[i + 2]; n++;
                var key = (d[i] >> 5) + "-" + (d[i + 1] >> 5) + "-" + (d[i + 2] >> 5);
                if (!buckets[key]) buckets[key] = { c: 0, r: 0, g: 0, b: 0 };
                buckets[key].c++; buckets[key].r += d[i]; buckets[key].g += d[i + 1]; buckets[key].b += d[i + 2];
            }
            if (!n) { showMsg("No visible pixels found in this image.", false); return; }
            var avg = toHex(rS / n, gS / n, bS / n), best = null;
            Object.keys(buckets).forEach(function (k) { if (!best || buckets[k].c > best.c) best = buckets[k]; });
            var dom = toHex(best.r / best.c, best.g / best.c, best.b / best.c);
            document.getElementById("ac2AvgSw").style.background = avg;
            document.getElementById("ac2DomSw").style.background = dom;
            document.getElementById("ac2Avg").textContent = avg + "  ·  RGB " + Math.round(rS / n) + ", " + Math.round(gS / n) + ", " + Math.round(bS / n);
            document.getElementById("ac2Dom").textContent = dom + "  ·  RGB " + Math.round(best.r / best.c) + ", " + Math.round(best.g / best.c) + ", " + Math.round(best.b / best.c);
            showMsg("Analyzed " + f.name + ".");
        };
        im.onerror = function () { showMsg("That file could not be read as an image.", false); };
        im.src = url;
    });

})();
</script>
@endsection
