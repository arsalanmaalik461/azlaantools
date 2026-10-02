@extends('layouts.app')

@section('title', "Ohm's Law Calculator - Voltage, Current, Resistance & Power | Azlaan Tools")
@section('meta_description', "Calculate voltage, current, resistance and power with Ohm's law. Includes single-phase and three-phase AC mode with power factor. Free, no signup.")

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Ohm's Law Calculator</h1>
            <p class="lead text-muted">Enter any 2 values — the other 2 will be calculated right away. AC mode also includes power factor and three-phase.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="btn-group mb-3" role="group">
                        <button type="button" class="btn btn-primary modebtn" data-mode="dc">DC / Simple</button>
                        <button type="button" class="btn btn-outline-primary modebtn" data-mode="1p">AC Single-Phase</button>
                        <button type="button" class="btn btn-outline-primary modebtn" data-mode="3p">AC Three-Phase</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="v" class="form-label fw-semibold">Voltage (V)</label>
                            <input type="number" class="form-control form-control-lg calc-in" id="v" value="220" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="i" class="form-label fw-semibold">Current (A)</label>
                            <input type="number" class="form-control form-control-lg calc-in" id="i" value="5" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="r" class="form-label fw-semibold">Resistance (Ω)</label>
                            <input type="number" class="form-control form-control-lg calc-in" id="r" placeholder="leave empty" step="any">
                        </div>
                        <div class="col-md-6">
                            <label for="p" class="form-label fw-semibold">Power (W)</label>
                            <input type="number" class="form-control form-control-lg calc-in" id="p" placeholder="leave empty" step="any">
                        </div>
                        <div class="col-md-6 d-none" id="pfWrap">
                            <label for="pf" class="form-label fw-semibold">Power Factor (0–1)</label>
                            <input type="number" class="form-control form-control-lg" id="pf" value="0.8" min="0.1" max="1" step="any">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="calcBtn">Calculate</button>
                        <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear All</button>
                    </div>
                    <div class="alert alert-success mt-3 mb-0 d-none" id="result"></div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="error"></div>
                    <div class="mt-3 small text-muted">
                        Formulas: V = I × R · P = V × I · I = V ÷ R · R = V ÷ I · P = I² × R · P = V² ÷ R<br>
                        AC Single-Phase: P = V × I × PF · Three-Phase: P = √3 × V × I × PF
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select a mode: DC/Simple, AC Single-Phase or AC Three-Phase.</li>
                        <li>Fill any <strong>2</strong> of the four fields (V, A, Ω, W) and leave the rest empty.</li>
                        <li>Press <strong>Calculate</strong> — the other two values will appear right away. Press Clear All to calculate again.</li>
                    </ol>
                    <p class="small text-muted mb-0">For electrical wiring or fault work, contact Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var mode = 'dc';
    function num(id) { var x = parseFloat(document.getElementById(id).value); return isNaN(x) ? null : x; }
    function fmt(n) { return parseFloat(n.toFixed(4)).toLocaleString('en-PK', { maximumFractionDigits: 4 }); }
    document.querySelectorAll('.modebtn').forEach(function (b) {
        b.addEventListener('click', function () {
            mode = b.getAttribute('data-mode');
            document.querySelectorAll('.modebtn').forEach(function (x) { x.className = 'btn btn-outline-primary modebtn'; });
            b.className = 'btn btn-primary modebtn';
            document.getElementById('pfWrap').classList.toggle('d-none', mode === 'dc');
        });
    });
    document.getElementById('clearBtn').addEventListener('click', function () {
        ['v', 'i', 'r', 'p'].forEach(function (id) { document.getElementById(id).value = ''; });
        document.getElementById('result').classList.add('d-none');
        document.getElementById('error').classList.add('d-none');
    });
    document.getElementById('calcBtn').addEventListener('click', function () {
        var err = document.getElementById('error'), res = document.getElementById('result');
        err.classList.add('d-none'); res.classList.add('d-none');
        var V = num('v'), I = num('i'), R = num('r'), P = num('p');
        var pf = mode === 'dc' ? 1 : (num('pf') || 0.8);
        var k = mode === '3p' ? Math.sqrt(3) : 1;
        var filled = [V, I, R, P].filter(function (x) { return x !== null; }).length;
        if (filled !== 2) {
            err.textContent = 'Please enter exactly 2 values — keep the other fields empty.';
            err.classList.remove('d-none'); return;
        }
        if ((V !== null && V < 0) || (I !== null && I < 0) || (R !== null && R <= 0) || (P !== null && P < 0)) {
            err.textContent = 'Values must be positive (resistance must be above zero).';
            err.classList.remove('d-none'); return;
        }
        if (V !== null && I !== null) { R = V / I; P = k * V * I * pf; }
        else if (V !== null && R !== null) { I = V / R; P = k * V * I * pf; }
        else if (V !== null && P !== null) { I = P / (k * V * pf); R = V / I; }
        else if (I !== null && R !== null) { V = I * R; P = k * V * I * pf; }
        else if (I !== null && P !== null) { V = P / (k * I * pf); R = V / I; }
        else if (R !== null && P !== null) {
            if (mode === 'dc') { V = Math.sqrt(P * R); I = V / R; }
            else { err.textContent = 'R + P cannot be used in AC mode — enter either V or I (in AC there is impedance instead of resistance).'; err.classList.remove('d-none'); return; }
        }
        document.getElementById('v').value = parseFloat(V.toFixed(4));
        document.getElementById('i').value = parseFloat(I.toFixed(4));
        document.getElementById('r').value = parseFloat(R.toFixed(4));
        document.getElementById('p').value = parseFloat(P.toFixed(2));
        res.innerHTML = '<strong>Voltage:</strong> ' + fmt(V) + ' V &nbsp; <strong>Current:</strong> ' + fmt(I) + ' A &nbsp; <strong>Resistance:</strong> ' + fmt(R) + ' Ω &nbsp; <strong>Power:</strong> ' + fmt(P) + ' W';
        res.classList.remove('d-none');
    });
})();
</script>
@endsection
