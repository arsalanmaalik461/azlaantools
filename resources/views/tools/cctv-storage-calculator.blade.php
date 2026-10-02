@extends('layouts.app')

@section('title', 'CCTV Storage Calculator - HDD Days & Recording Size | Azlaan Tools')
@section('meta_description', 'Calculate CCTV recording storage: GB per day, storage for 7/15/30 days and how long a 1TB, 2TB or 4TB hard drive will last. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">CCTV Storage Calculator</h1>
            <p class="lead text-muted">How much space will your cameras' recording use, and how many days will your HDD last? Calculate instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cams" class="form-label fw-semibold">Number of Cameras</label>
                            <input type="number" class="form-control form-control-lg" id="cams" value="4" min="1" step="1">
                        </div>
                        <div class="col-md-6">
                            <label for="res" class="form-label fw-semibold">Resolution</label>
                            <select class="form-select form-select-lg" id="res">
                                <option value="1">1 MP (720p) — ~1 Mbps</option>
                                <option value="2" selected>2 MP (1080p) — ~2 Mbps</option>
                                <option value="4">4 MP — ~4 Mbps</option>
                                <option value="6">5 MP — ~6 Mbps</option>
                                <option value="8">8 MP (4K) — ~8 Mbps</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="codec" class="form-label fw-semibold">Codec</label>
                            <select class="form-select form-select-lg" id="codec">
                                <option value="1" selected>H.264</option>
                                <option value="0.5">H.265 (about 50% less space)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="hours" class="form-label fw-semibold">Recording Hours / Day</label>
                            <input type="number" class="form-control form-control-lg" id="hours" value="24" min="1" max="24" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="motion" class="form-label fw-semibold">Motion-Only Recording (%)</label>
                            <input type="number" class="form-control form-control-lg" id="motion" value="100" min="1" max="100" step="any">
                            <div class="form-text">100% = continuous recording. For motion-only recording it is usually set to 30–50%.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3 text-center">
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">GB / Day</div><div class="fs-5 fw-bold" id="outDay">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">7 Days</div><div class="fs-5 fw-bold" id="out7">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">15 Days</div><div class="fs-5 fw-bold" id="out15">—</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3 bg-light"><div class="small text-muted">30 Days</div><div class="fs-5 fw-bold" id="out30">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">1 TB HDD Lasts</div><div class="fs-5 fw-bold" id="out1tb">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">2 TB HDD Lasts</div><div class="fs-5 fw-bold" id="out2tb">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="small text-muted">4 TB HDD Lasts</div><div class="fs-5 fw-bold" id="out4tb">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: a 1TB HDD actually gives about 931 GB of usable space — the calculation is based on that. The actual size can differ slightly depending on frame rate and scene activity.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select the number of cameras, resolution and codec.</li>
                        <li>Set how many hours per day are recorded and the motion-only percentage.</li>
                        <li>Instantly see GB/day, storage for 7/15/30 days, and the days each HDD size will last.</li>
                    </ol>
                    <p class="small text-muted mb-0">Formula: GB/day = Bitrate (Mbps) × Cameras × Hours × 3600 ÷ 8 ÷ 1000 × Motion%. To get CCTV installed: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function val(id) { return parseFloat(document.getElementById(id).value) || 0; }
    function gbText(gb) { return gb >= 1000 ? (gb / 1000).toFixed(2) + ' TB' : gb.toFixed(1) + ' GB'; }
    function calc() {
        var bitrate = val('res') * val('codec');
        var perDay = bitrate * val('cams') * val('hours') * 3600 / 8 / 1000 * (val('motion') / 100);
        document.getElementById('outDay').textContent = perDay.toFixed(1) + ' GB';
        document.getElementById('out7').textContent = gbText(perDay * 7);
        document.getElementById('out15').textContent = gbText(perDay * 15);
        document.getElementById('out30').textContent = gbText(perDay * 30);
        function days(tb) { return perDay > 0 ? Math.floor(tb * 1000 / perDay) + ' days' : '—'; }
        document.getElementById('out1tb').textContent = days(1);
        document.getElementById('out2tb').textContent = days(2);
        document.getElementById('out4tb').textContent = days(4);
    }
    ['cams', 'res', 'codec', 'hours', 'motion'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
        document.getElementById(id).addEventListener('change', calc);
    });
    calc();
})();
</script>
@endsection
