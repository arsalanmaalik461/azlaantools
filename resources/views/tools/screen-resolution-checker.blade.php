@extends('layouts.app')

@section('title', 'Screen Resolution Checker - What Is My Screen Size? | Azlaan Tools')
@section('meta_description', 'Free screen resolution checker: see your screen size, window size, viewport, device pixel ratio, orientation and color depth live. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Screen Resolution Checker</h1>
            <p class="lead text-muted">See your exact screen and window details instantly. Resize your browser and watch the numbers update live — free, no signup.</p>

            <div class="card shadow-sm mb-4 text-center">
                <div class="card-body py-4">
                    <div class="text-muted small">Your Screen Resolution</div>
                    <div class="display-4 fw-bold" id="bigRes">- x -</div>
                    <div class="text-muted" id="deviceGuess">Detecting device...</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Screen Width x Height</div><div class="fw-bold fs-5" id="scrSize">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Available Screen</div><div class="fw-bold fs-5" id="availSize">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Window Inner Size</div><div class="fw-bold fs-5" id="winSize">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Viewport Size</div><div class="fw-bold fs-5" id="vpSize">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Device Pixel Ratio</div><div class="fw-bold fs-5" id="dprVal">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Orientation</div><div class="fw-bold fs-5" id="orientVal">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Color Depth</div><div class="fw-bold fs-5" id="colorDepth">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Pixel Depth</div><div class="fw-bold fs-5" id="pixelDepth">-</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="small text-muted">Touch Support</div><div class="fw-bold fs-5" id="touchVal">-</div></div></div>
                    </div>
                    <div class="mt-3">
                        <label for="reportBox" class="form-label fw-semibold">Full Report</label>
                        <textarea class="form-control" id="reportBox" rows="6" readonly></textarea>
                        <button type="button" class="btn btn-success btn-sm mt-2" id="copyBtn">Copy Report</button>
                        <span class="text-success small d-none" id="copyMsg">Copied!</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Your screen resolution is shown in big numbers at the top the moment the page loads.</li>
                        <li>Resize the browser window or rotate your phone to see window and viewport values update live.</li>
                        <li>Check device pixel ratio, orientation and color depth in the detail grid.</li>
                        <li>Click <strong>Copy Report</strong> to copy all details for sharing with support or a developer.</li>
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
    function update() {
        var sw = window.screen.width, sh = window.screen.height;
        var aw = window.screen.availWidth, ah = window.screen.availHeight;
        var iw = window.innerWidth, ih = window.innerHeight;
        var vw = document.documentElement.clientWidth, vh = document.documentElement.clientHeight;
        var dpr = window.devicePixelRatio || 1;
        var orient = 'Unknown';
        if (window.screen.orientation && window.screen.orientation.type) { orient = window.screen.orientation.type; }
        else { orient = iw >= ih ? 'landscape' : 'portrait'; }
        var touch = ('ontouchstart' in window || navigator.maxTouchPoints > 0) ? 'Yes' : 'No';
        document.getElementById('bigRes').textContent = sw + ' x ' + sh;
        document.getElementById('scrSize').textContent = sw + ' x ' + sh;
        document.getElementById('availSize').textContent = aw + ' x ' + ah;
        document.getElementById('winSize').textContent = iw + ' x ' + ih;
        document.getElementById('vpSize').textContent = vw + ' x ' + vh;
        document.getElementById('dprVal').textContent = dpr;
        document.getElementById('orientVal').textContent = orient;
        document.getElementById('colorDepth').textContent = window.screen.colorDepth + '-bit';
        document.getElementById('pixelDepth').textContent = window.screen.pixelDepth + '-bit';
        document.getElementById('touchVal').textContent = touch;
        var guess = 'Desktop / Laptop';
        if (sw <= 480) { guess = 'Likely a mobile phone'; }
        else if (sw <= 1024) { guess = 'Likely a tablet or small laptop'; }
        document.getElementById('deviceGuess').textContent = guess + ' - Physical pixels (CSS x DPR): ' + Math.round(vw * dpr) + ' x ' + Math.round(vh * dpr);
        var lines = [];
        lines.push('Screen Resolution: ' + sw + ' x ' + sh);
        lines.push('Available Screen: ' + aw + ' x ' + ah);
        lines.push('Window Inner Size: ' + iw + ' x ' + ih);
        lines.push('Viewport: ' + vw + ' x ' + vh);
        lines.push('Device Pixel Ratio: ' + dpr);
        lines.push('Orientation: ' + orient);
        lines.push('Color Depth: ' + window.screen.colorDepth + '-bit');
        lines.push('User Agent: ' + navigator.userAgent);
        document.getElementById('reportBox').value = lines.join('\n');
    }
    window.addEventListener('resize', update);
    window.addEventListener('orientationchange', update);
    document.getElementById('copyBtn').addEventListener('click', async function () {
        var box = document.getElementById('reportBox');
        try { await navigator.clipboard.writeText(box.value); }
        catch (e) { box.select(); document.execCommand('copy'); }
        var msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(function () { msg.classList.add('d-none'); }, 2000);
    });
    update();
})();
</script>
@endsection
