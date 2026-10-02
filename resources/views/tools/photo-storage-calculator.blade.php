@extends('layouts.app')

@section('title', 'Photo Storage Calculator — Free Online Tool')
@section('meta_description', 'Estimate how many photos and videos fit on a memory card or phone storage.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Photo Storage Calculator</h1>
                    <p class="lead small text-muted">Enter your storage size, average photo size (or megapixels) and video settings to see how many photos fit, or how many minutes of video you can record.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="psSize">Storage size (GB)</label><input type="number" class="form-control" id="psSize" value="128" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="psMode">Photo size by</label><select class="form-select" id="psMode"><option value="mb">Average file size (MB)</option><option value="mp">Camera megapixels</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="psPhoto">Photo value (MB or megapixels)</label><input type="number" class="form-control" id="psPhoto" value="5" min="0" step="any"></div>
                        <div class="col-md-3"><label class="form-label" for="psFormat">Photo format</label><select class="form-select" id="psFormat"><option value="jpeg">JPEG</option><option value="raw">RAW</option></select></div>
                        <div class="col-md-3"><label class="form-label" for="psBitrate">Video bitrate (Mbps)</label><input type="number" class="form-control" id="psBitrate" value="45" min="0" step="any"></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="psOut">Enter storage and file sizes to estimate capacity.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Enter the card or storage size in GB (usable space is about 93 percent of the label).</li>
                        <li>Set the average photo size directly, or enter megapixels and format for an estimate.</li>
                        <li>Read how many photos fit, or how many minutes of video the same space holds.</li>
                    </ol>
                    <p class="small text-muted mb-0">A JPEG photo is roughly 0.35 MB per megapixel and a RAW file roughly 1.2 MB per megapixel on average, though detailed scenes compress less. Video uses bitrate in megabits per second: minutes = GB x 8 x 1000 / (Mbps x 60), adjusted for usable space.</p>
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
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function calc() {
        var gb = num("psSize"), val = num("psPhoto"), bitrate = num("psBitrate");
        var out = el("psOut");
        if (gb <= 0 || val <= 0) { out.textContent = "Please enter a storage size and photo value greater than zero."; return; }
        var usableMB = gb * 1000 * 0.93;
        var photoMB = el("psMode").value === "mp" ? val * (el("psFormat").value === "raw" ? 1.2 : 0.35) : val;
        var count = Math.floor(usableMB / photoMB);
        var html = "<strong>Photos that fit:</strong> about " + count.toLocaleString("en-US") + " photos at " + photoMB.toFixed(2) + " MB each on " + gb + " GB.";
        if (bitrate > 0) { var mins = usableMB * 8 / (bitrate * 60); html += "<br><strong>Video that fits:</strong> about " + Math.floor(mins).toLocaleString("en-US") + " minutes (" + (mins / 60).toFixed(1) + " hours) at " + bitrate + " Mbps."; }
        out.innerHTML = html;
    }
    ["psSize", "psMode", "psPhoto", "psFormat", "psBitrate"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
