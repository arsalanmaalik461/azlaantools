@extends('layouts.app')
@section('title', 'SVG Optimizer and Minifier — Free Online Tool')
@section('meta_description', 'Shrink SVG files by removing comments metadata and extra whitespace')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">SVG Optimizer and Minifier</h1>
            <p class="lead small text-muted">Paste or upload an SVG, remove comments, metadata and editor clutter, and download a smaller optimized SVG file.</p>

                    <label class="form-label fw-semibold" for="soFile">Upload an SVG file (optional)</label>
                    <input type="file" id="soFile" class="form-control" accept="image/svg+xml,.svg">
                    <label class="form-label mt-3" for="soIn">SVG code</label>
                    <textarea id="soIn" class="form-control font-monospace" rows="7" placeholder="Paste SVG markup here"></textarea>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="form-check"><input type="checkbox" id="soComments" class="form-check-input" checked><label class="form-check-label" for="soComments">Remove comments</label></div>
                        <div class="form-check"><input type="checkbox" id="soMeta" class="form-check-input" checked><label class="form-check-label" for="soMeta">Remove metadata, title and desc</label></div>
                        <div class="form-check"><input type="checkbox" id="soEditor" class="form-check-input" checked><label class="form-check-label" for="soEditor">Remove editor data (Inkscape etc)</label></div>
                        <div class="form-check"><input type="checkbox" id="soSpace" class="form-check-input" checked><label class="form-check-label" for="soSpace">Collapse whitespace</label></div>
                    </div>
                    <button type="button" id="soGo" class="btn btn-primary btn-lg w-100 mt-3">Optimize SVG</button>
                    <label class="form-label mt-3" for="soOut">Optimized output</label>
                    <textarea id="soOut" class="form-control font-monospace" rows="7" readonly></textarea>
                    <p class="small text-muted mt-2 mb-0" id="soStats">No output yet.</p>
                    <div class="d-flex gap-2 mt-2"><button type="button" id="soCopy" class="btn btn-outline-secondary">Copy Output</button><button type="button" id="soDl" class="btn btn-success" disabled>Download Optimized SVG</button></div>

            <div id="soMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload an SVG file or paste its markup.</li>
                    <li>Choose what to strip and click Optimize SVG.</li>
                    <li>Compare sizes, then copy or download the optimized file.</li>
            </ol>
            <p class="small text-muted mb-0">Optimization here removes non-rendering content only. It never changes shapes, so the image looks identical, just smaller.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("soMsg");
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

    document.getElementById("soFile").addEventListener("change", function () {
        var f = this.files && this.files[0]; if (!f) return;
        f.text().then(function (t) { document.getElementById("soIn").value = t; showMsg("Loaded " + f.name + " (" + fmtBytes(f.size) + ")."); });
    });
    document.getElementById("soGo").addEventListener("click", function () {
        var src = document.getElementById("soIn").value;
        if (!src.trim() || src.indexOf("<svg") === -1) { showMsg("Paste valid SVG markup that contains an svg tag first.", false); return; }
        var before = new Blob([src]).size, out = src;
        try {
            var doc = new DOMParser().parseFromString(out, "image/svg+xml");
            if (doc.querySelector("parsererror")) throw new Error("parse");
            if (document.getElementById("soMeta").checked) {
                doc.querySelectorAll("metadata, title, desc").forEach(function (el) { el.remove(); });
            }
            if (document.getElementById("soEditor").checked) {
                doc.querySelectorAll("*").forEach(function (el) {
                    Array.prototype.slice.call(el.attributes || []).forEach(function (at) {
                        if (at.name.indexOf("inkscape:") === 0 || at.name.indexOf("sodipodi:") === 0 || at.name.indexOf("xmlns:inkscape") === 0 || at.name.indexOf("xmlns:sodipodi") === 0) el.removeAttribute(at.name);
                    });
                    if (el.tagName && (el.tagName.indexOf("sodipodi:") === 0 || el.tagName.indexOf("inkscape:") === 0)) el.remove();
                });
            }
            out = new XMLSerializer().serializeToString(doc);
        } catch (e) { /* fall back to text cleanup below */ }
        if (document.getElementById("soComments").checked) out = out.replace(/<!--[\s\S]*?-->/g, "");
        if (document.getElementById("soSpace").checked) { out = out.replace(/>\s+</g, "><").replace(/\s{2,}/g, " ").trim(); }
        document.getElementById("soOut").value = out;
        var after = new Blob([out]).size, saved = before ? Math.max(0, (before - after) / before * 100) : 0;
        document.getElementById("soStats").textContent = "Before: " + fmtBytes(before) + "  ·  After: " + fmtBytes(after) + "  ·  Saved: " + saved.toFixed(1) + "%";
        document.getElementById("soDl").disabled = false;
        showMsg("Optimized. Saved " + saved.toFixed(1) + "% of the file size.");
    });
    document.getElementById("soCopy").addEventListener("click", function () {
        var v = document.getElementById("soOut").value; if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v); showMsg("Optimized SVG copied.");
    });
    document.getElementById("soDl").addEventListener("click", function () {
        var v = document.getElementById("soOut").value; if (v) dl(new Blob([v], { type: "image/svg+xml" }), "optimized.svg");
    });

})();
</script>
@endsection
