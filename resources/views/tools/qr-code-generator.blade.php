@extends('layouts.app')

@section('title', 'QR Code Generator - Azlaan Tools')
@section('meta_description', 'Free QR code generator. Turn any text or URL into a QR code instantly and download it as a PNG image. No signup required.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">QR Code Generator</h1>
            <p class="lead text-muted">Create a QR code for any text or website link in seconds, then download it as a PNG image for printing or sharing.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="qrText" class="form-label fw-semibold">Text or URL</label>
                        <textarea class="form-control" id="qrText" rows="3" placeholder="Type text or paste a link here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="qrSize" class="form-label fw-semibold">Size</label>
                        <select class="form-select" id="qrSize">
                            <option value="128">Small (128 x 128)</option>
                            <option value="256" selected>Medium (256 x 256)</option>
                            <option value="512">Large (512 x 512)</option>
                            <option value="1024">Extra Large (1024 x 1024)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="generateBtn">Generate QR Code</button>
                    <div class="alert alert-danger mt-3 d-none" id="qrError" role="alert"></div>
                    <div id="qrWrap" class="d-none mt-4 text-center">
                        <div id="qrOutput" class="d-inline-block border rounded p-3 bg-white"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="downloadBtn">Download PNG</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your text or paste a URL in the box.</li>
                <li>Choose a size for your QR code.</li>
                <li>Click <strong>Generate QR Code</strong>.</li>
                <li>Click <strong>Download PNG</strong> to save the QR code image to your device.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    var qrInstance = null;
    document.getElementById('generateBtn').addEventListener('click', function () {
        var text = document.getElementById('qrText').value.trim();
        var size = parseInt(document.getElementById('qrSize').value, 10) || 256;
        var err = document.getElementById('qrError');
        var wrap = document.getElementById('qrWrap');
        var output = document.getElementById('qrOutput');
        err.classList.add('d-none');
        if (!text) { err.textContent = 'Please enter some text or a URL first.'; err.classList.remove('d-none'); wrap.classList.add('d-none'); return; }
        if (typeof QRCode === 'undefined') { err.textContent = 'QR library failed to load. Please check your internet connection and try again.'; err.classList.remove('d-none'); return; }
        output.innerHTML = '';
        try {
            qrInstance = new QRCode(output, { text: text, width: size, height: size, correctLevel: QRCode.CorrectLevel.M });
        } catch (e) {
            err.textContent = 'That text is too long to fit in a single QR code. Please shorten it and try again.';
            err.classList.remove('d-none'); wrap.classList.add('d-none'); return;
        }
        wrap.classList.remove('d-none');
    });

    document.getElementById('downloadBtn').addEventListener('click', function () {
        var output = document.getElementById('qrOutput');
        var canvas = output.querySelector('canvas');
        var img = output.querySelector('img');
        var url = null;
        if (canvas) { url = canvas.toDataURL('image/png'); }
        else if (img && img.src) { url = img.src; }
        if (!url) { alert('Please generate a QR code first.'); return; }
        var a = document.createElement('a');
        a.href = url;
        a.download = 'qr-code.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
