@extends('layouts.app')

@section('title', 'Plot Size Calculator - Marla, Kanal, Sq Ft, Gaz Converter Pakistan | Azlaan Tools')
@section('meta_description', 'Convert plot width and length in feet into sq ft, square yards (gaz), marla, kanal, acre and sarsai. Both 272.25 and 225 sq ft marla standards. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Plot Size Calculator</h1>
            <p class="lead text-muted">Enter the plot length and width in feet — get marla, kanal, gaz and acre instantly.</p>

            <div class="alert alert-info">
                <strong>There are 2 marla standards:</strong> Revenue / old standard = <strong>272.25 sq ft per marla</strong>, while many housing societies use <strong>225 sq ft per marla</strong>. Use the switch below to see both calculations — the marla count changes with the standard, so confirm the standard on your file / registry.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">A. Area from Dimensions</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="wid" class="form-label fw-semibold">Width (ft)</label>
                            <input type="number" class="form-control form-control-lg" id="wid" value="30" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="len" class="form-label fw-semibold">Length (ft)</label>
                            <input type="number" class="form-control form-control-lg" id="len" value="75" min="0" step="any">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Marla Standard</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-primary stdbtn" data-std="272.25">272.25 sq ft (Revenue)</button>
                                <button type="button" class="btn btn-outline-primary stdbtn" data-std="225">225 sq ft (Housing Society)</button>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Sq Ft</div><div class="fs-5 fw-bold" id="outSqft">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Sq Yards (Gaz)</div><div class="fs-5 fw-bold" id="outGaz">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-success text-white"><div class="small">Marla</div><div class="fs-5 fw-bold" id="outMarla">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Kanal (20 Marla)</div><div class="fs-5 fw-bold" id="outKanal">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Acre (8 Kanal)</div><div class="fs-5 fw-bold" id="outAcre">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">Sarsai (9 per Marla)</div><div class="fs-5 fw-bold" id="outSarsai">—</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">B. Dimensions from Marla (Reverse)</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="revMarla" class="form-label fw-semibold">Marla</label>
                            <input type="number" class="form-control form-control-lg" id="revMarla" value="10" min="0" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="revWidth" class="form-label fw-semibold">Front / Width (ft) — the width you want</label>
                            <input type="number" class="form-control form-control-lg" id="revWidth" value="35" min="0" step="any">
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="revOut">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>In card A, enter the plot width and length in feet.</li>
                        <li>Select the marla standard — Revenue (272.25) or Housing Society (225) — marla and kanal are recalculated instantly on the new standard.</li>
                        <li>In card B, enter marla and your desired front width — you will get an estimate of the required length.</li>
                    </ol>
                    <p class="small text-muted mb-0">Note: 1 kanal = 20 marla, 1 acre = 8 kanal, 1 marla = 9 sarsai, 1 sq yard (gaz) = 9 sq ft. For house construction, wiring or solar work on your plot, contact Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var std = 272.25;
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    function fmt(n, d) { return n.toLocaleString('en-PK', { maximumFractionDigits: d, minimumFractionDigits: 0 }); }
    document.querySelectorAll('.stdbtn').forEach(function (b) {
        b.addEventListener('click', function () {
            std = parseFloat(b.getAttribute('data-std'));
            document.querySelectorAll('.stdbtn').forEach(function (x) { x.className = 'btn btn-outline-primary stdbtn'; });
            b.className = 'btn btn-primary stdbtn';
            calc();
        });
    });
    function calc() {
        var sqft = val('wid') * val('len');
        var marla = sqft / std;
        document.getElementById('outSqft').textContent = fmt(sqft, 2);
        document.getElementById('outGaz').textContent = fmt(sqft / 9, 2);
        document.getElementById('outMarla').textContent = fmt(marla, 3);
        document.getElementById('outKanal').textContent = fmt(marla / 20, 4);
        document.getElementById('outAcre').textContent = fmt(marla / 160, 5);
        document.getElementById('outSarsai').textContent = fmt(marla * 9, 2);
        var rm = val('revMarla'), rw = val('revWidth');
        var rsqft = rm * std;
        if (rw > 0) {
            document.getElementById('revOut').innerHTML = '<strong>' + fmt(rm, 2) + ' marla</strong> (' + fmt(std, 2) + ' standard) = <strong>' + fmt(rsqft, 0) + ' sq ft</strong>. If the front is <strong>' + fmt(rw, 0) + ' ft</strong>, the length is about <strong>' + fmt(rsqft / rw, 1) + ' ft</strong>. (For a square plot, each side is ~' + fmt(Math.sqrt(rsqft), 1) + ' ft)';
        } else {
            document.getElementById('revOut').textContent = 'Enter the front width to estimate the length.';
        }
    }
    ['wid', 'len', 'revMarla', 'revWidth'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
