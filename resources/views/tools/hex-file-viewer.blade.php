@extends('layouts.app')

@section('title', 'Hex File Viewer — Free Online Tool')
@section('meta_description', 'View any file as a hex dump with ASCII side by side inspection')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Hex File Viewer</h1>
            <p class="lead small text-muted">Open any file to inspect it as a classic hex dump — offset, hex bytes and ASCII side by side — paged so even larger files stay fast. The file never leaves your device.</p>
            <label class="form-label" for="hvFile">Choose a file</label>
            <input type="file" class="form-control" id="hvFile">
            <div class="d-flex gap-2 align-items-center mt-3 flex-wrap">
                <button type="button" class="btn btn-outline-primary btn-sm" id="hvPrev">Previous page</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="hvNext">Next page</button>
                <span class="small text-muted" id="hvInfo">No file loaded. Page size: 512 bytes.</span>
            </div>
            <pre class="border rounded p-3 mt-3 font-monospace small" id="hvOut" style="overflow:auto;white-space:pre">Hex dump will appear here.</pre>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose any file from your device.</li><li>Read each row: offset, 16 hex bytes, then the ASCII view.</li><li>Use Previous and Next to page through the file.</li></ol>
            <p class="small text-muted mb-0">Note: Non-printable bytes are shown as a dot in the ASCII column, exactly like classic hex editors. Nothing is uploaded — the file is read with the browser FileReader API.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    var bytes = null, page = 0, PAGE = 512;
    function render() {
        if (!bytes) return;
        var start = page * PAGE, end = Math.min(bytes.length, start + PAGE), lines = [], i, j;
        for (i = start; i < end; i += 16) {
            var hex = "", asc = "";
            for (j = 0; j < 16; j++) {
                if (i + j < end) { var b = bytes[i + j]; hex += (b < 16 ? "0" : "") + b.toString(16) + " "; asc += (b >= 32 && b < 127) ? String.fromCharCode(b) : "."; }
                else { hex += "   "; }
                if (j === 7) hex += " ";
            }
            var off = ("00000000" + i.toString(16)).slice(-8);
            lines.push(off + "  " + hex + " |" + asc + "|");
        }
        el("hvOut").textContent = lines.join("\n");
        el("hvInfo").textContent = "Bytes " + (start + 1) + " to " + end + " of " + bytes.length + " (page " + (page + 1) + " of " + Math.max(1, Math.ceil(bytes.length / PAGE)) + ").";
    }
    el("hvFile").addEventListener("change", function () { var f = el("hvFile").files[0]; if (!f) return; var r = new FileReader(); r.onload = function () { bytes = new Uint8Array(r.result); page = 0; render(); }; r.readAsArrayBuffer(f); });
    el("hvPrev").addEventListener("click", function () { if (page > 0) { page--; render(); } });
    el("hvNext").addEventListener("click", function () { if (bytes && (page + 1) * PAGE < bytes.length) { page++; render(); } });
})();
</script>
@endsection
