@extends('layouts.app')

@section('title', 'Internet Speed Test - Check Download Speed & Ping | Azlaan Tools')
@section('meta_description', 'Free internet speed test: measure your download speed in Mbps and ping instantly in your browser. No signup, no app needed. Works on mobile and desktop.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Internet Speed Test</h1>
            <p class="lead text-muted">Check your internet download speed and ping right here in your browser — no app, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="display-4 fw-bold my-2"><span id="speedValue">--</span> <small class="fs-5 text-muted">Mbps</small></div>
                    <div class="text-muted mb-1" id="statusText">Press Start to begin the test.</div>
                    <div class="mb-3">Ping: <strong id="pingValue">--</strong> ms</div>
                    <div class="progress mb-3 d-none" id="progressWrap" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg" id="startBtn">Start Speed Test</button>
                    <div class="alert alert-danger mt-3 mb-0 d-none text-start" id="errorBox"></div>
                    <div class="alert alert-success mt-3 mb-0 d-none text-start" id="finalBox"></div>
                    <p class="small text-muted mt-3 mb-0">Note: Results vary with your network, Wi-Fi signal, distance to the test server and other devices using your connection. For best results, test on a stable connection and run the test 2-3 times.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Click <strong>Start Speed Test</strong>.</li>
                        <li>The tool first measures your ping, then downloads test data in increasing sizes while showing live speed.</li>
                        <li>Your final average download speed in Mbps will be displayed at the end.</li>
                        <li>Click Start again anytime to re-run the test.</li>
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
    var startBtn = document.getElementById('startBtn');
    var speedValue = document.getElementById('speedValue');
    var pingValue = document.getElementById('pingValue');
    var statusText = document.getElementById('statusText');
    var errorBox = document.getElementById('errorBox');
    var finalBox = document.getElementById('finalBox');
    var progressWrap = document.getElementById('progressWrap');
    var progressBar = document.getElementById('progressBar');
    var running = false;

    function cacheBust(url) {
        return url + (url.indexOf('?') === -1 ? '?' : '&') + 'cb=' + Date.now() + Math.floor(Math.random() * 1000000);
    }

    async function measurePing() {
        var url = cacheBust('https://speed.cloudflare.com/__down?bytes=0');
        var samples = [];
        for (var i = 0; i < 3; i++) {
            var t0 = performance.now();
            var res = await fetch(url, { cache: 'no-store' });
            if (!res.ok) throw new Error('ping failed');
            samples.push(performance.now() - t0);
        }
        samples.sort(function (a, b) { return a - b; });
        return Math.round(samples[0]);
    }

    async function downloadChunk(bytes) {
        var url = 'https://speed.cloudflare.com/__down?bytes=' + bytes + '&cb=' + Date.now() + Math.floor(Math.random() * 1000000);
        var t0 = performance.now();
        var res = await fetch(url, { cache: 'no-store' });
        if (!res.ok) throw new Error('download failed');
        if (res.body && res.body.getReader) {
            var reader = res.body.getReader();
            var received = 0;
            while (true) {
                var part = await reader.read();
                if (part.done) break;
                received += part.value.length;
                var elapsed = (performance.now() - t0) / 1000;
                if (elapsed > 0.1) {
                    var liveMbps = (received * 8 / elapsed) / 1000000;
                    speedValue.textContent = liveMbps.toFixed(2);
                }
            }
            var totalSeconds = (performance.now() - t0) / 1000;
            return (received * 8 / totalSeconds) / 1000000; // Mbps
        }
        var blob = await res.blob();
        var secs = (performance.now() - t0) / 1000;
        return (blob.size * 8 / secs) / 1000000;
    }

    async function runTest() {
        if (running) return;
        running = true;
        startBtn.disabled = true;
        startBtn.textContent = 'Testing...';
        errorBox.classList.add('d-none');
        finalBox.classList.add('d-none');
        speedValue.textContent = '--';
        pingValue.textContent = '--';
        progressWrap.classList.remove('d-none');
        progressBar.style.width = '5%';

        try {
            statusText.textContent = 'Measuring ping...';
            var ping = await measurePing();
            pingValue.textContent = ping;
            progressBar.style.width = '15%';

            // Progressive sizes: 0.5 MB, 2 MB, 5 MB, 10 MB
            var sizes = [500000, 2000000, 5000000, 10000000];
            var totalWeightedMbps = 0, totalBytes = 0;
            for (var i = 0; i < sizes.length; i++) {
                statusText.textContent = 'Downloading test data (' + (i + 1) + ' of ' + sizes.length + ')...';
                var mbps = await downloadChunk(sizes[i]);
                speedValue.textContent = mbps.toFixed(2);
                totalWeightedMbps += mbps * sizes[i];
                totalBytes += sizes[i];
                progressBar.style.width = (15 + ((i + 1) / sizes.length) * 85) + '%';
            }
            var finalMbps = totalWeightedMbps / totalBytes;
            speedValue.textContent = finalMbps.toFixed(2);
            statusText.textContent = 'Test complete.';
            finalBox.innerHTML = '<strong>Your download speed: ' + finalMbps.toFixed(2) + ' Mbps</strong> &nbsp;|&nbsp; Ping: ' + ping + ' ms<br><span class="small">Run the test again for confirmation — results vary from moment to moment.</span>';
            finalBox.classList.remove('d-none');
        } catch (e) {
            statusText.textContent = 'Test failed.';
            errorBox.textContent = 'The speed test could not be completed. This can happen if the test server is blocked by your network, an ad-blocker, or you are offline. Please check your connection and try again.';
            errorBox.classList.remove('d-none');
        }

        progressBar.style.width = '100%';
        setTimeout(function () { progressWrap.classList.add('d-none'); }, 800);
        startBtn.disabled = false;
        startBtn.textContent = 'Start Speed Test Again';
        running = false;
    }

    startBtn.addEventListener('click', runTest);
})();
</script>
@endsection
