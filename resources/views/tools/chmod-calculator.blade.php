@extends('layouts.app')

@section('title', 'Chmod Calculator — Free Online Tool')
@section('meta_description', 'Calculate Linux file permissions and get the chmod command instantly')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Chmod Calculator</h1>
            <p class="lead small text-muted">Tick the read, write and execute boxes for owner, group and others — the octal value, symbolic notation and ready chmod command update live.</p>
            <div class="row g-3">
                <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="fw-semibold mb-2">Owner</div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cO4" checked><label class="form-check-label" for="cO4">Read (4)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cO2" checked><label class="form-check-label" for="cO2">Write (2)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cO1" checked><label class="form-check-label" for="cO1">Execute (1)</label></div></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="fw-semibold mb-2">Group</div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cG4" checked><label class="form-check-label" for="cG4">Read (4)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cG2"><label class="form-check-label" for="cG2">Write (2)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cG1" checked><label class="form-check-label" for="cG1">Execute (1)</label></div></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="fw-semibold mb-2">Others</div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cU4" checked><label class="form-check-label" for="cU4">Read (4)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cU2"><label class="form-check-label" for="cU2">Write (2)</label></div><div class="form-check"><input class="form-check-input ch-in" type="checkbox" id="cU1" checked><label class="form-check-label" for="cU1">Execute (1)</label></div></div></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6"><label class="form-label" for="chFile">File or folder name</label><input type="text" class="form-control" id="chFile" value="file.txt"></div>
                <div class="col-md-6"><label class="form-label">Common presets</label><div class="d-flex gap-2 flex-wrap"><button type="button" class="btn btn-outline-primary btn-sm ch-preset" data-v="755">755</button><button type="button" class="btn btn-outline-primary btn-sm ch-preset" data-v="644">644</button><button type="button" class="btn btn-outline-primary btn-sm ch-preset" data-v="777">777</button><button type="button" class="btn btn-outline-primary btn-sm ch-preset" data-v="600">600</button></div></div>
            </div>
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-4"><div class="text-muted small">Octal</div><div class="fs-4 fw-bold" id="chOctal">755</div></div>
                    <div class="col-md-4"><div class="text-muted small">Symbolic</div><div class="fs-4 fw-bold font-monospace" id="chSym">rwxr-xr-x</div></div>
                    <div class="col-md-4"><div class="text-muted small">Command</div><div class="fs-5 fw-bold font-monospace" id="chCmd">chmod 755 file.txt</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Tick the permission boxes for owner, group and others.</li><li>Read the octal value and symbolic notation.</li><li>Copy the chmod command and run it on your server.</li></ol>
            <p class="small text-muted mb-0">Note: 755 (folders and scripts) and 644 (regular files) are the most common safe defaults on web servers. Avoid 777 on production servers because it lets anyone write to the file.</p>
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
    var groups = [["cO4","cO2","cO1"],["cG4","cG2","cG1"],["cU4","cU2","cU1"]];
    var letters = ["r","w","x"];
    function calc() {
        var oct = "", sym = "";
        groups.forEach(function (g) { var sum = 0; g.forEach(function (id, i) { var on = el(id).checked; if (on) { sum += [4,2,1][i]; sym += letters[i]; } else { sym += "-"; } }); oct += sum; });
        el("chOctal").textContent = oct;
        el("chSym").textContent = sym;
        var f = el("chFile").value.trim() || "file.txt";
        el("chCmd").textContent = "chmod " + oct + " " + f;
    }
    document.querySelectorAll(".ch-in").forEach(function (c) { c.addEventListener("change", calc); });
    el("chFile").addEventListener("input", calc);
    document.querySelectorAll(".ch-preset").forEach(function (b) { b.addEventListener("click", function () { var v = b.getAttribute("data-v"); var bits = { "7": [1,1,1], "6": [1,1,0], "5": [1,0,1], "4": [1,0,0], "0": [0,0,0] }; groups.forEach(function (g, gi) { var set = bits[v.charAt(gi)]; g.forEach(function (id, i) { el(id).checked = !!set[i]; }); }); calc(); }); });
    calc();
})();
</script>
@endsection
