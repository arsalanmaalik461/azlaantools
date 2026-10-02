@extends('layouts.app')

@section('title', 'CSS Gradient Generator Online Free - Linear, Radial & Conic | Azlaan Tools')
@section('meta_description', 'Free CSS gradient generator: create linear, radial and conic gradients with up to 4 color stops, angle control and presets. Copy ready CSS code instantly.')

@section('styles')
<style>
.preset-box{height:56px;border-radius:8px;cursor:pointer;border:2px solid #dee2e6}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">CSS Gradient Generator</h1>
            <p class="lead text-muted">Design beautiful CSS gradients visually and copy the code. Runs 100% in your browser.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="previewBox" class="rounded mb-3" style="height:220px;border:1px solid #dee2e6;"></div>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-semibold" for="gradType">Type</label><select id="gradType" class="form-select"><option value="linear">Linear</option><option value="radial">Radial</option><option value="conic">Conic</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold" for="angleRange">Angle: <span id="angleVal">90</span> deg</label><input type="range" id="angleRange" class="form-range" min="0" max="360" value="90"></div>
                        <div class="col-md-4 d-flex align-items-end"><button type="button" class="btn btn-outline-primary w-100" id="addStopBtn">Add Color Stop (max 4)</button></div>
                    </div>
                    <div id="stopsBox" class="mt-3"></div>
                    <label class="form-label fw-semibold mt-3">Presets</label>
                    <div class="row g-2" id="presetsBox"></div>
                    <label class="form-label fw-semibold mt-3" for="cssOut">Generated CSS</label>
                    <textarea id="cssOut" class="form-control font-monospace" rows="4" readonly></textarea>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="copyBtn">Copy CSS</button>
                </div>
            </div>
            <h2>How to use</h2>
            <ol>
                <li>Pick a gradient type (linear, radial or conic) and adjust the angle for linear/conic.</li>
                <li>Change stop colors with the color pickers and positions with the sliders. Add up to 4 stops.</li>
                <li>Or click any preset to load it instantly.</li>
                <li>Copy the generated CSS and paste it into your stylesheet.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var stops = [{ color: '#0d6efd', pos: 0 }, { color: '#6610f2', pos: 100 }];
    var presets = [
        { name: 'Ocean Blue', stops: [{ color: '#2193b0', pos: 0 }, { color: '#6dd5ed', pos: 100 }] },
        { name: 'Sunset', stops: [{ color: '#ff512f', pos: 0 }, { color: '#f09819', pos: 100 }] },
        { name: 'Purple Bliss', stops: [{ color: '#360033', pos: 0 }, { color: '#0b8793', pos: 100 }] },
        { name: 'Green Fresh', stops: [{ color: '#11998e', pos: 0 }, { color: '#38ef7d', pos: 100 }] },
        { name: 'Pink Candy', stops: [{ color: '#ff9a9e', pos: 0 }, { color: '#fecfef', pos: 100 }] },
        { name: 'Royal Gold', stops: [{ color: '#141e30', pos: 0 }, { color: '#f7971e', pos: 60 }, { color: '#ffd200', pos: 100 }] },
        { name: 'Pakistan Green', stops: [{ color: '#01411c', pos: 0 }, { color: '#0a7a3d', pos: 55 }, { color: '#7ac74f', pos: 100 }] }
    ];
    function gradientCss() {
        var type = document.getElementById('gradType').value; var angle = document.getElementById('angleRange').value;
        var parts = stops.map(function (s) { return s.color + ' ' + s.pos + '%'; }).join(', ');
        if (type === 'linear') return 'linear-gradient(' + angle + 'deg, ' + parts + ')';
        if (type === 'radial') return 'radial-gradient(circle, ' + parts + ')';
        return 'conic-gradient(from ' + angle + 'deg, ' + parts + ')';
    }
    function renderStops() {
        var box = document.getElementById('stopsBox'); box.innerHTML = '';
        stops.forEach(function (s, i) {
            var row = document.createElement('div'); row.className = 'row g-2 align-items-center mb-2';
            row.innerHTML = '';
            var c1 = document.createElement('div'); c1.className = 'col-3 col-md-2';
            var colorInp = document.createElement('input'); colorInp.type = 'color'; colorInp.className = 'form-control form-control-color w-100'; colorInp.value = s.color;
            colorInp.addEventListener('input', function () { stops[i].color = colorInp.value; update(); });
            c1.appendChild(colorInp);
            var c2 = document.createElement('div'); c2.className = 'col-6 col-md-8';
            var range = document.createElement('input'); range.type = 'range'; range.className = 'form-range'; range.min = '0'; range.max = '100'; range.value = s.pos;
            range.addEventListener('input', function () { stops[i].pos = parseInt(range.value, 10); label.textContent = range.value + '%'; update(); });
            c2.appendChild(range);
            var c3 = document.createElement('div'); c3.className = 'col-2 col-md-1'; var label = document.createElement('span'); label.className = 'small fw-semibold'; label.textContent = s.pos + '%'; c3.appendChild(label);
            var c4 = document.createElement('div'); c4.className = 'col-1';
            if (stops.length > 2) { var del = document.createElement('button'); del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger'; del.textContent = 'X'; del.addEventListener('click', function () { stops.splice(i, 1); renderStops(); renderPresetsActive(); update(); }); c4.appendChild(del); }
            row.appendChild(c1); row.appendChild(c2); row.appendChild(c3); row.appendChild(c4); box.appendChild(row);
        });
    }
    function renderPresetsActive() {}
    function update() {
        var css = gradientCss();
        document.getElementById('previewBox').style.background = css;
        document.getElementById('cssOut').value = 'background: ' + css + ';';
        document.getElementById('angleVal').textContent = document.getElementById('angleRange').value;
    }
    var presetsBox = document.getElementById('presetsBox');
    presets.forEach(function (p) {
        var col = document.createElement('div'); col.className = 'col-4 col-md-3';
        var box = document.createElement('div'); box.className = 'preset-box'; box.title = p.name;
        var parts = p.stops.map(function (s) { return s.color + ' ' + s.pos + '%'; }).join(', ');
        box.style.background = 'linear-gradient(90deg, ' + parts + ')';
        var cap = document.createElement('div'); cap.className = 'small text-center mt-1'; cap.textContent = p.name;
        box.addEventListener('click', function () { stops = p.stops.map(function (s) { return { color: s.color, pos: s.pos }; }); renderStops(); update(); });
        col.appendChild(box); col.appendChild(cap); presetsBox.appendChild(col);
    });
    document.getElementById('gradType').addEventListener('change', update);
    document.getElementById('angleRange').addEventListener('input', update);
    document.getElementById('addStopBtn').addEventListener('click', function () { if (stops.length >= 4) return; stops.push({ color: '#ffc107', pos: 50 }); stops.sort(function (a, b) { return a.pos - b.pos; }); renderStops(); update(); });
    document.getElementById('copyBtn').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(document.getElementById('cssOut').value); });
    renderStops(); update();
})();
</script>
@endsection
