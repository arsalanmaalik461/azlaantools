@extends('layouts.app')

@section('title', 'Formula Sheet Generator - Azlaan Tools')
@section('meta_description', 'Generate a printable physics and maths formula sheet for board exams. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Formula Sheet Generator</h1>
            <p class="lead text-muted">Make a printable sheet of physics and maths formulas. Select topics, see the preview, then print or save as PDF — for FSc / board exam revision.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllBtn">Clear All</button>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <h5>Physics</h5>
                            <div id="physicsTopics"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h5>Maths</h5>
                            <div id="mathsTopics"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="sheetTitle" class="form-label fw-semibold">Sheet title</label>
                        <input type="text" class="form-control" id="sheetTitle" value="My Formula Sheet" placeholder="e.g. FSc Physics Chapter 1-5 Revision">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Formula Sheet</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h5 class="mb-0">Preview</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">Copy Text</button>
                                <button type="button" class="btn btn-sm btn-success" id="printBtn">Print / PDF</button>
                            </div>
                        </div>
                        <div class="border rounded p-3 bg-light" id="sheetBox" style="max-height: 480px; overflow-y: auto;"></div>
                        <div class="form-text mt-2">Press Print and select "Save as PDF".</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Tick the topics whose formulas you need.</li>
                <li>Write the sheet title and press "Generate Formula Sheet".</li>
                <li>Check the preview, then print or save as PDF.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var DATA = {
        physics: [
            { t: 'Mechanics', f: [
                'v = u + at',
                's = ut + (1/2)at^2',
                'v^2 = u^2 + 2as',
                'F = ma',
                'W = Fd cos(theta)',
                'KE = (1/2)mv^2',
                'PE = mgh',
                'P = W/t = Fv',
                'p = mv   (momentum)',
                'a_c = v^2/r   (centripetal)'
            ] },
            { t: 'Waves & Oscillations', f: [
                'v = f x lambda',
                'T = 1/f',
                'T = 2pi x sqrt(l/g)   (pendulum)',
                'T = 2pi x sqrt(m/k)   (spring)',
                'f_n = nv/2L   (open pipe)',
                'Beat frequency = |f1 - f2|'
            ] },
            { t: 'Electricity & Magnetism', f: [
                'V = IR   (Ohm\'s law)',
                'P = VI = I^2 R = V^2/R',
                'Q = It',
                'R_series = R1 + R2 + ...',
                '1/R_parallel = 1/R1 + 1/R2 + ...',
                'E = F/q',
                'F = BIL sin(theta)',
                'V = -N (dPhi/dt)   (Faraday)',
                'C = Q/V',
                '1/C_series = 1/C1 + 1/C2'
            ] },
            { t: 'Optics', f: [
                'n = c/v',
                'n1 sin(theta1) = n2 sin(theta2)   (Snell)',
                '1/f = 1/v + 1/u   (mirror/lens)',
                'm = v/u = h_image/h_object',
                'sin(theta_c) = 1/n   (critical angle)'
            ] },
            { t: 'Modern Physics', f: [
                'E = mc^2',
                'E = hf = hc/lambda   (photon)',
                'lambda = h/p   (de Broglie)',
                'KE_max = hf - phi   (photoelectric)',
                'N = N0 (1/2)^(t/T_half)   (decay)'
            ] },
            { t: 'Heat & Thermo', f: [
                'Q = mc x Delta_T',
                'Q = mL   (latent heat)',
                'PV = nRT   (ideal gas)',
                'W = P x Delta_V',
                'Delta_U = Q - W   (1st law)',
                'efficiency = W_out / Q_in'
            ] }
        ],
        maths: [
            { t: 'Algebra', f: [
                'x = (-b +- sqrt(b^2 - 4ac)) / 2a',
                'D = b^2 - 4ac   (discriminant)',
                'a^2 - b^2 = (a-b)(a+b)',
                '(a+b)^2 = a^2 + 2ab + b^2',
                'a^m x a^n = a^(m+n)',
                '(a^m)^n = a^(mn)',
                'log(ab) = log a + log b',
                'log(a/b) = log a - log b',
                'log(a^n) = n log a'
            ] },
            { t: 'Trigonometry', f: [
                'sin^2(theta) + cos^2(theta) = 1',
                'tan(theta) = sin(theta)/cos(theta)',
                'sin(A+B) = sinA cosB + cosA sinB',
                'cos(A+B) = cosA cosB - sinA sinB',
                'sin(2A) = 2 sinA cosA',
                'cos(2A) = cos^2 A - sin^2 A',
                'a/sinA = b/sinB = c/sinC   (sine rule)',
                'c^2 = a^2 + b^2 - 2ab cosC   (cosine rule)'
            ] },
            { t: 'Calculus', f: [
                'd/dx (x^n) = n x^(n-1)',
                'd/dx (sin x) = cos x',
                'd/dx (cos x) = -sin x',
                'd/dx (e^x) = e^x',
                'd/dx (ln x) = 1/x',
                'Product rule: (uv)\' = u\'v + uv\'',
                'Quotient rule: (u/v)\' = (u\'v - uv\')/v^2',
                'Chain rule: dy/dx = dy/du x du/dx',
                'Integral x^n dx = x^(n+1)/(n+1) + C'
            ] },
            { t: 'Geometry & Mensuration', f: [
                'Circle: A = pi r^2, C = 2 pi r',
                'Triangle: A = (1/2) x base x height',
                'Sphere: V = (4/3) pi r^3, A = 4 pi r^2',
                'Cylinder: V = pi r^2 h',
                'Cone: V = (1/3) pi r^2 h',
                'Pythagoras: c^2 = a^2 + b^2',
                'Distance = sqrt((x2-x1)^2 + (y2-y1)^2)'
            ] },
            { t: 'Sequences & Series', f: [
                'AP: a_n = a + (n-1)d',
                'AP sum: S_n = n/2 (2a + (n-1)d)',
                'GP: a_n = a r^(n-1)',
                'GP sum: S_n = a(r^n - 1)/(r - 1)',
                'GP infinite (|r|<1): S = a/(1-r)'
            ] }
        ]
    };

    var physicsTopics = document.getElementById('physicsTopics');
    var mathsTopics = document.getElementById('mathsTopics');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var sheetBox = document.getElementById('sheetBox');
    var lastPlain = '';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function buildTopicList(container, list, prefix) {
        list.forEach(function (topic, i) {
            var id = prefix + '-' + i;
            var div = document.createElement('div');
            div.className = 'form-check mb-1';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input topic-cb';
            cb.id = id;
            cb.checked = true;
            cb.dataset.prefix = prefix;
            cb.dataset.index = i;
            var lb = document.createElement('label');
            lb.className = 'form-check-label';
            lb.htmlFor = id;
            lb.textContent = topic.t + ' (' + topic.f.length + ')';
            div.appendChild(cb);
            div.appendChild(lb);
            container.appendChild(div);
        });
    }
    buildTopicList(physicsTopics, DATA.physics, 'phy');
    buildTopicList(mathsTopics, DATA.maths, 'mat');

    function setAll(v) {
        var cbs = document.querySelectorAll('.topic-cb');
        for (var i = 0; i < cbs.length; i++) cbs[i].checked = v;
    }
    document.getElementById('selectAllBtn').addEventListener('click', function () { setAll(true); });
    document.getElementById('clearAllBtn').addEventListener('click', function () { setAll(false); });

    goBtn.addEventListener('click', function () {
        hideError();
        var cbs = document.querySelectorAll('.topic-cb');
        var chosen = [];
        for (var i = 0; i < cbs.length; i++) {
            if (cbs[i].checked) {
                var list = cbs[i].dataset.prefix === 'phy' ? DATA.physics : DATA.maths;
                chosen.push(list[parseInt(cbs[i].dataset.index, 10)]);
            }
        }
        if (!chosen.length) { showError('Please select at least one topic.'); return; }
        var title = document.getElementById('sheetTitle').value.trim() || 'My Formula Sheet';
        var html = '<h4 class="mb-3">' + title.replace(/</g, '&lt;') + '</h4>';
        var plain = title.toUpperCase() + '\n' + new Array(title.length + 1).join('=') + '\n';
        chosen.forEach(function (topic) {
            html += '<h6 class="mt-3 mb-1 text-primary">' + topic.t + '</h6><ul class="mb-2">';
            plain += '\n' + topic.t + '\n';
            topic.f.forEach(function (f) {
                html += '<li><code>' + f.replace(/</g, '&lt;') + '</code></li>';
                plain += '  - ' + f + '\n';
            });
            html += '</ul>';
        });
        var total = chosen.reduce(function (s, t) { return s + t.f.length; }, 0);
        html += '<p class="text-muted small">Total ' + total + ' formulas — ' + chosen.length + ' topics</p>';
        plain += '\nTotal ' + total + ' formulas, ' + chosen.length + ' topics.';
        sheetBox.innerHTML = html;
        lastPlain = plain;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var btn = document.getElementById('copyBtn');
        if (navigator.clipboard && lastPlain) {
            navigator.clipboard.writeText(lastPlain).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy Text'; }, 1500);
            });
        }
    });
    document.getElementById('printBtn').addEventListener('click', function () { window.print(); });
})();
</script>
@endsection
