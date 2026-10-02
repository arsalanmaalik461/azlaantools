@extends('layouts.app')

@section('title', 'CGPA to Percentage Converter - 4.0 and 10 Scale | Azlaan Tools')
@section('meta_description', 'Free CGPA to percentage converter: convert CGPA on 10 scale (x9.5) and 4.0 scale to percentage, and percentage back to CGPA. HEC-style formulas clearly labelled. No signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">CGPA to Percentage Converter</h1>
            <p class="lead text-muted">Convert CGPA to percentage and percentage back to CGPA — both 10-point and 4.0 scales, with formulas clearly labelled.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Convert CGPA → Percentage</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cgpa10" class="form-label fw-semibold">CGPA on 10 Scale</label>
                            <input type="number" class="form-control" id="cgpa10" min="0" max="10" step="any" placeholder="e.g. 8.5">
                            <div class="form-text">Formula: Percentage = CGPA x 9.5 (HEC / CBSE-style common formula)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Percentage (from 10 scale)</label>
                            <div class="border rounded p-3 bg-light text-center"><div class="fs-3 fw-bold text-primary" id="pctFrom10Out">—</div></div>
                        </div>
                        <div class="col-md-6">
                            <label for="cgpa4" class="form-label fw-semibold">CGPA on 4.0 Scale</label>
                            <input type="number" class="form-control" id="cgpa4" min="0" max="4" step="any" placeholder="e.g. 3.50">
                            <div class="form-text">Formula: Percentage = (CGPA / 4) x 100 — linear formula. Note: many Pakistani universities use a grade table instead (see below).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Percentage (from 4.0 scale, linear)</label>
                            <div class="border rounded p-3 bg-light text-center"><div class="fs-3 fw-bold text-success" id="pctFrom4Out">—</div></div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="cgpaMsg"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Convert Percentage → CGPA</h2>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="pctInput" class="form-label fw-semibold">Percentage (%)</label>
                            <input type="number" class="form-control" id="pctInput" min="0" max="100" step="any" placeholder="e.g. 80">
                        </div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">CGPA on 10 Scale (Pct / 9.5)</div><div class="fs-4 fw-bold" id="cgpa10Out">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">CGPA on 4.0 Scale ((Pct / 100) x 4)</div><div class="fs-4 fw-bold" id="cgpa4Out">—</div></div></div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="pctMsg"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Common 4.0 Scale Conversion Table (Indicative)</h2>
                    <p class="small text-muted">This is a commonly used indicative table — every university official formula can be different, so always check your university transcript rules.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light"><tr><th>CGPA (4.0)</th><th>Percentage (approx.)</th><th>Grade</th></tr></thead>
                            <tbody>
                                <tr><td>4.00</td><td>85% and above</td><td>A+ / A</td></tr>
                                <tr><td>3.70</td><td>80 – 84%</td><td>A-</td></tr>
                                <tr><td>3.30</td><td>75 – 79%</td><td>B+</td></tr>
                                <tr><td>3.00</td><td>70 – 74%</td><td>B</td></tr>
                                <tr><td>2.70</td><td>65 – 69%</td><td>B-</td></tr>
                                <tr><td>2.30</td><td>60 – 64%</td><td>C+</td></tr>
                                <tr><td>2.00</td><td>55 – 59%</td><td>C</td></tr>
                                <tr><td>1.70</td><td>50 – 54%</td><td>C-</td></tr>
                                <tr><td>1.00</td><td>40 – 49%</td><td>D</td></tr>
                                <tr><td>0.00</td><td>Below 40%</td><td>F</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">Formulas clearly labelled: for the 10-scale, Percentage = CGPA x 9.5 and reverse CGPA = Percentage / 9.5. For the 4.0-scale linear, Percentage = (CGPA / 4) x 100 and reverse CGPA = (Percentage / 100) x 4. The table conversion can differ from the linear formula.</div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>If you have a 10-scale CGPA, enter it in the first box — the percentage will come from the x9.5 formula.</li>
                        <li>If you have a 4.0-scale CGPA, enter it in the second box — the linear percentage will show, and you can also compare with the table.</li>
                        <li>To get CGPA from a percentage, enter the percentage below — you will get CGPA on both scales.</li>
                        <li>For official work, always confirm your university conversion formula.</li>
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
    function calcForward() {
        var msg = document.getElementById('cgpaMsg');
        var c10 = parseFloat(document.getElementById('cgpa10').value);
        var c4 = parseFloat(document.getElementById('cgpa4').value);
        var err = '';
        if (!isNaN(c10)) {
            if (c10 < 0 || c10 > 10) { err = '10-scale CGPA must be between 0 and 10.'; document.getElementById('pctFrom10Out').textContent = '—'; }
            else { document.getElementById('pctFrom10Out').textContent = (c10 * 9.5).toFixed(2) + '%'; }
        } else { document.getElementById('pctFrom10Out').textContent = '—'; }
        if (!isNaN(c4)) {
            if (c4 < 0 || c4 > 4) { err = '4.0-scale CGPA must be between 0 and 4.'; document.getElementById('pctFrom4Out').textContent = '—'; }
            else { document.getElementById('pctFrom4Out').textContent = ((c4 / 4) * 100).toFixed(2) + '%'; }
        } else { document.getElementById('pctFrom4Out').textContent = '—'; }
        if (err) { msg.textContent = err; msg.classList.remove('d-none'); } else { msg.classList.add('d-none'); }
    }
    function calcReverse() {
        var msg = document.getElementById('pctMsg');
        var pct = parseFloat(document.getElementById('pctInput').value);
        if (isNaN(pct)) { document.getElementById('cgpa10Out').textContent = '—'; document.getElementById('cgpa4Out').textContent = '—'; msg.classList.add('d-none'); return; }
        if (pct < 0 || pct > 100) { msg.textContent = 'Percentage must be between 0 and 100.'; msg.classList.remove('d-none'); return; }
        msg.classList.add('d-none');
        document.getElementById('cgpa10Out').textContent = Math.min(pct / 9.5, 10).toFixed(2);
        document.getElementById('cgpa4Out').textContent = ((pct / 100) * 4).toFixed(2);
    }
    document.getElementById('cgpa10').addEventListener('input', calcForward);
    document.getElementById('cgpa4').addEventListener('input', calcForward);
    document.getElementById('pctInput').addEventListener('input', calcReverse);
    calcForward(); calcReverse();
})();
</script>
@endsection
