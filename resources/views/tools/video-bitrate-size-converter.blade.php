@extends('layouts.app')

@section('title', 'Video Bitrate Size Converter — Free Online Tool')
@section('meta_description', 'Enter a video length and bitrate to get the file size in MB/GB, or get the bitrate from the file size.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Video Bitrate Size Converter</h1>
            <p class="lead small text-muted">Get the file size from a video duration and bitrate, or the needed bitrate from a target file size — both sections are below. Use it to plan recording, export or CCTV storage.</p>
<h2 class="h6">1 — File size from duration + bitrate</h2><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="vbDur">Duration (minutes)</label><input type="number" step="any" class="form-control" id="vbDur" placeholder="e.g. 60"></div><div class="col-md-4 mb-3"><label class="form-label" for="vbVid">Video bitrate (Mbps)</label><input type="number" step="any" class="form-control" id="vbVid" placeholder="e.g. 8"></div><div class="col-md-4 mb-3"><label class="form-label" for="vbAud">Audio bitrate (Mbps, optional)</label><input type="number" step="any" class="form-control" id="vbAud" placeholder="e.g. 0.128"></div></div><div class="alert alert-secondary mt-3 mb-0" id="vbRes1">Enter values — the result will show here live.</div><h2 class="h6 mt-4">2 — Bitrate from file size + duration</h2><div class="row"><div class="col-md-4 mb-3"><label class="form-label" for="vbSize">File size (GB)</label><input type="number" step="any" class="form-control" id="vbSize" placeholder="e.g. 4"></div><div class="col-md-4 mb-3"><label class="form-label" for="vbDur2">Duration (minutes)</label><input type="number" step="any" class="form-control" id="vbDur2" placeholder="e.g. 60"></div></div><div class="alert alert-secondary mt-3 mb-0" id="vbRes2">Enter values — the result will show here live.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                <li>In the first section, enter the duration (minutes) and total bitrate (Mbps) — the file size comes in MB and GB.</li>
                <li>In the second section, enter the target file size (GB) and duration — you get the needed total bitrate.</li>
                <li>Audio bitrate is usually 0.128 Mbps (128 kbps); leave audio empty if you only need video.</li>
            </ol>
            <p class="small text-muted mb-0">Note: Formula: Size (MB) = bitrate (Mbps) x seconds / 8. MB/GB here use decimal units (1 GB = 1000 MB), which is common for storage and streaming. The actual file may be slightly larger because of container overhead.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";

function fmt(n, d) { if (n === null || n === undefined || !isFinite(n)) { return "\u2014"; } var dec = (d === undefined ? 4 : d); return Number(n.toFixed(dec)).toLocaleString("en-US", { maximumFractionDigits: dec }); }
function num(id) { var e = document.getElementById(id); if (!e) { return null; } var v = parseFloat(e.value); return isNaN(v) ? null : v; }
function txt(id) { var e = document.getElementById(id); return e ? e.value : ""; }
function setT(id, t) { var e = document.getElementById(id); if (e) { e.textContent = t; } }
function setH(id, t) { var e = document.getElementById(id); if (e) { e.innerHTML = t; } }
function bind(ids, fn) { ids.forEach(function (id) { var e = document.getElementById(id); if (e) { e.addEventListener("input", fn); e.addEventListener("change", fn); } }); }
function parseList(s) { if (!s) { return []; } var parts = s.split(/[\s,;]+/); var out = []; for (var i = 0; i < parts.length; i++) { if (parts[i] === "") { continue; } var v = parseFloat(parts[i]); if (!isNaN(v) && isFinite(v)) { out.push(v); } } return out; }
function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }


function vbCalc1() {
    var d = num("vbDur"), v = num("vbVid"), a = num("vbAud");
    if (d === null || v === null) { setT("vbRes1", "Enter the duration and video bitrate."); return; }
    var total = v + (a || 0); var mb = total * d * 60 / 8;
    setT("vbRes1", "Total bitrate: " + fmt(total, 3) + " Mbps | File size: " + fmt(mb, 2) + " MB = " + fmt(mb / 1000, 3) + " GB | Per hour: " + fmt(total * 3600 / 8 / 1000, 3) + " GB");
}
function vbCalc2() {
    var gb = num("vbSize"), d = num("vbDur2");
    if (gb === null || d === null || d <= 0) { setT("vbRes2", "Enter the file size (GB) and duration (minutes, positive)."); return; }
    var mbps = gb * 1000 * 8 / (d * 60);
    setT("vbRes2", "Needed total bitrate: " + fmt(mbps, 3) + " Mbps (" + fmt(mbps * 1000, 0) + " kbps) | If audio is 0.128 Mbps, video bitrate: " + fmt(Math.max(0, mbps - 0.128), 3) + " Mbps");
}
bind(["vbDur","vbVid","vbAud"], vbCalc1); bind(["vbSize","vbDur2"], vbCalc2); vbCalc1(); vbCalc2();

})();
</script>
@endsection
