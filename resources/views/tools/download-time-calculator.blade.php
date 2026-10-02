@extends('layouts.app')

@section('title', 'Download Time Calculator - How Long Will It Take? | Azlaan Tools')
@section('meta_description', 'Free download time calculator: enter file size and internet speed to estimate download time in hours, minutes and seconds. Includes 4G and WiFi presets. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Download Time Calculator</h1>
            <p class="lead text-muted">Find out how long a file will take to download on your connection — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fileSize" class="form-label fw-semibold">File Size</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="fileSize" placeholder="e.g. 2" min="0" step="any">
                                <select class="form-select" id="sizeUnit" style="max-width:110px;">
                                    <option value="MB" selected>MB</option>
                                    <option value="GB">GB</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="speedVal" class="form-label fw-semibold">Internet Speed</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="speedVal" placeholder="e.g. 20" min="0" step="any">
                                <select class="form-select" id="speedUnit" style="max-width:120px;">
                                    <option value="Mbps" selected>Mbps</option>
                                    <option value="Kbps">Kbps</option>
                                    <option value="MBps">MB/s</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="small fw-semibold d-block mb-2">Quick presets:</span>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-speed="5" data-unit="Mbps">3G - 5 Mbps</button>
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-speed="20" data-unit="Mbps">4G - 20 Mbps</button>
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-speed="50" data-unit="Mbps">WiFi - 50 Mbps</button>
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-speed="100" data-unit="Mbps">Fiber - 100 Mbps</button>
                            <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-speed="300" data-unit="Kbps">Slow - 300 Kbps</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="calcBtn">Calculate Time</button>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div id="resultWrap" class="d-none mt-3 text-center">
                        <div class="display-6 fw-bold" id="resTime">-</div>
                        <div class="text-muted" id="resDetail">-</div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: real download speed varies with server load, WiFi signal and other devices on your network, so actual time may be longer. Remember: 8 bits = 1 byte, so a 20 Mbps line downloads at most about 2.5 MB per second.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the file size and choose MB or GB.</li>
                        <li>Enter your internet speed, or tap a preset like 4G or WiFi.</li>
                        <li>Click <strong>Calculate Time</strong> to see the estimate in hours, minutes and seconds.</li>
                        <li>Use MB/s if your download manager shows speed in megabytes per second.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var errorBox = document.getElementById('errorBox');
    document.querySelectorAll('.preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('speedVal').value = btn.getAttribute('data-speed');
            document.getElementById('speedUnit').value = btn.getAttribute('data-unit');
        });
    });
    function formatDuration(totalSec) {
        if (totalSec < 60) { return Math.ceil(totalSec) + ' seconds'; }
        var h = Math.floor(totalSec / 3600);
        var m = Math.floor((totalSec % 3600) / 60);
        var s = Math.round(totalSec % 60);
        if (s === 60) { s = 0; m += 1; }
        if (m === 60) { m = 0; h += 1; }
        var parts = [];
        if (h > 0) { parts.push(h + (h === 1 ? ' hour' : ' hours')); }
        if (m > 0) { parts.push(m + (m === 1 ? ' minute' : ' minutes')); }
        if (s > 0 && h === 0) { parts.push(s + ' seconds'); }
        return parts.join(', ');
    }
    document.getElementById('calcBtn').addEventListener('click', function () {
        errorBox.classList.add('d-none');
        document.getElementById('resultWrap').classList.add('d-none');
        var size = parseFloat(document.getElementById('fileSize').value);
        var sizeUnit = document.getElementById('sizeUnit').value;
        var speed = parseFloat(document.getElementById('speedVal').value);
        var speedUnit = document.getElementById('speedUnit').value;
        if (isNaN(size) || size <= 0) {
            errorBox.textContent = 'Please enter a valid file size greater than zero.';
            errorBox.classList.remove('d-none');
            return;
        }
        if (isNaN(speed) || speed <= 0) {
            errorBox.textContent = 'Please enter a valid internet speed greater than zero.';
            errorBox.classList.remove('d-none');
            return;
        }
        var sizeMB = sizeUnit === 'GB' ? size * 1024 : size;
        var sizeMbits = sizeMB * 8;
        var speedMbps = speed;
        if (speedUnit === 'Kbps') { speedMbps = speed / 1000; }
        if (speedUnit === 'MBps') { speedMbps = speed * 8; }
        var secs = sizeMbits / speedMbps;
        document.getElementById('resTime').textContent = 'About ' + formatDuration(secs);
        document.getElementById('resDetail').textContent = 'Exact estimate: ' + secs.toFixed(1) + ' seconds for ' + size + ' ' + sizeUnit + ' at ' + speed + ' ' + speedUnit + '.';
        document.getElementById('resultWrap').classList.remove('d-none');
    });
})();
</script>
@endsection
