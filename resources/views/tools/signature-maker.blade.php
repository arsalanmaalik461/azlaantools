@extends('layouts.app')

@section('title', 'Signature Maker - Draw or Type & Download PNG | Azlaan Tools')
@section('meta_description', 'Free signature maker. Draw your signature or type your name in stylish fonts and download a transparent PNG instantly. No signup needed.')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Dancing+Script:wght@600&family=Allura&display=swap" rel="stylesheet">
<style>
#drawCanvas, #typeCanvas { background: repeating-conic-gradient(#f1f1f1 0% 25%, #fff 0% 50%) 50% / 20px 20px; touch-action: none; cursor: crosshair; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center"><div class="col-lg-8">
        <h1 class="mb-3">Signature Maker</h1>
        <p class="lead text-muted">Draw or type your signature and download it as a transparent PNG — free, no signup, nothing is uploaded.</p>
        <ul class="nav nav-tabs">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#drawPane" type="button">Draw</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#typePane" type="button">Type</button></li>
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm mb-4">
            <div class="tab-pane fade show active" id="drawPane">
                <canvas id="drawCanvas" width="700" height="250" class="border rounded w-100"></canvas>
                <div class="row g-2 mt-3 align-items-end">
                    <div class="col-md-3"><label class="form-label small">Pen Color</label><input type="color" class="form-control form-control-color w-100" id="penColor" value="#111111"></div>
                    <div class="col-md-3"><label class="form-label small">Stroke Width</label><input type="range" class="form-range" id="penWidth" min="1" max="10" value="3"></div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-secondary" id="undoBtn">Undo</button>
                        <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                        <button type="button" class="btn btn-success" id="dlDraw">Download PNG</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="typePane">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label">Your Name</label><input class="form-control" id="typeName" placeholder="e.g. Malik Arslan" value="Your Name"></div>
                    <div class="col-md-3"><label class="form-label">Font Style</label><select class="form-select" id="fontSel"><option value="'Great Vibes', cursive">Great Vibes</option><option value="'Dancing Script', cursive">Dancing Script</option><option value="'Allura', cursive">Allura</option><option value="'Brush Script MT', cursive">Brush Script</option><option value="cursive">Cursive</option></select></div>
                    <div class="col-md-3"><label class="form-label">Color</label><input type="color" class="form-control form-control-color w-100" id="typeColor" value="#111111"></div>
                </div>
                <canvas id="typeCanvas" width="700" height="250" class="border rounded w-100 mt-3"></canvas>
                <div class="text-end mt-3"><button type="button" class="btn btn-success" id="dlType">Download PNG</button></div>
            </div>
        </div>
        <div class="card shadow-sm"><div class="card-body">
            <h2>How to use</h2>
            <ol><li>Draw tab: sign with your mouse or finger, adjust color and width, undo or clear if needed.</li><li>Type tab: enter your name and pick a stylish font.</li><li>Click Download PNG — the signature saves with a transparent background, ready for documents.</li></ol>
        </div></div>
    </div></div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function download(canvas, name) { var a = document.createElement('a'); a.download = name; a.href = canvas.toDataURL('image/png'); a.click(); }
    // DRAW
    var canvas = document.getElementById('drawCanvas'), ctx = canvas.getContext('2d'), drawing = false, history = [];
    function pos(e) { var r = canvas.getBoundingClientRect(); var x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left, y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top; return { x: x * canvas.width / r.width, y: y * canvas.height / r.height }; }
    function start(e) { e.preventDefault(); history.push(ctx.getImageData(0, 0, canvas.width, canvas.height)); if (history.length > 20) history.shift(); drawing = true; var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
    function move(e) { if (!drawing) return; e.preventDefault(); var p = pos(e); ctx.strokeStyle = document.getElementById('penColor').value; ctx.lineWidth = document.getElementById('penWidth').value; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.lineTo(p.x, p.y); ctx.stroke(); }
    function stop() { drawing = false; }
    canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', stop);
    canvas.addEventListener('touchstart', start, { passive: false }); canvas.addEventListener('touchmove', move, { passive: false }); canvas.addEventListener('touchend', stop);
    document.getElementById('clearBtn').addEventListener('click', function () { history.push(ctx.getImageData(0, 0, canvas.width, canvas.height)); ctx.clearRect(0, 0, canvas.width, canvas.height); });
    document.getElementById('undoBtn').addEventListener('click', function () { if (history.length) ctx.putImageData(history.pop(), 0, 0); });
    document.getElementById('dlDraw').addEventListener('click', function () { download(canvas, 'signature.png'); });
    // TYPE
    var tc = document.getElementById('typeCanvas'), tctx = tc.getContext('2d');
    function renderType() {
        tctx.clearRect(0, 0, tc.width, tc.height);
        var name = document.getElementById('typeName').value || 'Your Name';
        tctx.fillStyle = document.getElementById('typeColor').value;
        tctx.font = '72px ' + document.getElementById('fontSel').value;
        tctx.textAlign = 'center'; tctx.textBaseline = 'middle';
        // shrink font if text too wide
        var size = 72; while (tctx.measureText(name).width > tc.width - 40 && size > 20) { size -= 4; tctx.font = size + 'px ' + document.getElementById('fontSel').value; }
        tctx.fillText(name, tc.width / 2, tc.height / 2);
    }
    ['typeName', 'fontSel', 'typeColor'].forEach(function (id) { document.getElementById(id).addEventListener('input', renderType); document.getElementById(id).addEventListener('change', renderType); });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(renderType);
    document.getElementById('dlType').addEventListener('click', function () { download(tc, 'signature.png'); });
    renderType();
})();
</script>
@endsection
