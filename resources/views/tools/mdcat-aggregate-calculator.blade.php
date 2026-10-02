@extends('layouts.app')

@section('title', 'MDCAT Aggregate Calculator - Azlaan Tools')
@section('meta_description', 'Calculate your MDCAT aggregate online for free. Matric 10, FSc 40, MDCAT 50 formula for MBBS and BDS merit.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">MDCAT Aggregate Calculator</h1>
            <p class="lead text-muted">Calculate your aggregate from Matric 10%, FSc 40%, MDCAT 50% — MBBS/BDS merit calculation, as per the PMDC formula.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Matric (10%)</h5>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="mObt" class="form-label fw-semibold">Obtained marks</label>
                            <input type="number" class="form-control" id="mObt" placeholder="e.g. 980" min="0">
                        </div>
                        <div class="col-6">
                            <label for="mTot" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="mTot" placeholder="e.g. 1100" min="1" value="1100">
                        </div>
                    </div>
                    <h5 class="card-title">FSc Pre-Medical (40%)</h5>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="fObt" class="form-label fw-semibold">Obtained marks</label>
                            <input type="number" class="form-control" id="fObt" placeholder="e.g. 950" min="0">
                        </div>
                        <div class="col-6">
                            <label for="fTot" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="fTot" placeholder="e.g. 1100" min="1" value="1100">
                        </div>
                    </div>
                    <h5 class="card-title">MDCAT (50%)</h5>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="mdObt" class="form-label fw-semibold">MDCAT marks</label>
                            <input type="number" class="form-control" id="mdObt" placeholder="e.g. 180" min="0" max="200">
                        </div>
                        <div class="col-6">
                            <label for="mdTot" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="mdTot" value="200" min="1">
                            <div class="form-text">MDCAT is usually of 200 marks</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Calculate Aggregate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="text-center mb-3">
                            <div class="small text-muted">Your Aggregate</div>
                            <div class="display-4 fw-bold text-primary" id="aggOut">0%</div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Component</th><th class="text-end">Percentage</th><th class="text-end">Weightage</th><th class="text-end">Contribution</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Matric</td><td class="text-end" id="mPct">-</td><td class="text-end">10%</td><td class="text-end" id="mCon">-</td></tr>
                                <tr><td>FSc</td><td class="text-end" id="fPct">-</td><td class="text-end">40%</td><td class="text-end" id="fCon">-</td></tr>
                                <tr><td>MDCAT</td><td class="text-end" id="mdPct">-</td><td class="text-end">50%</td><td class="text-end" id="mdCon">-</td></tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info" id="verdictBox" role="status"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the obtained and total marks of Matric, FSc and MDCAT.</li>
                <li>Click <strong>Calculate Aggregate</strong> — the weightage of all three is applied to make the final aggregate.</li>
            </ol>
            <div class="alert alert-warning small">
                Formula: (Matric % × 10 + FSc % × 40 + MDCAT % × 50) ÷ 100 — as per the current PMDC policy.
                Merit changes every year and for each university — for final information check <strong>pmdc.pk</strong> and the official website of the relevant university.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var mObt = document.getElementById('mObt');
    var mTot = document.getElementById('mTot');
    var fObt = document.getElementById('fObt');
    var fTot = document.getElementById('fTot');
    var mdObt = document.getElementById('mdObt');
    var mdTot = document.getElementById('mdTot');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var aggOut = document.getElementById('aggOut');
    var mPct = document.getElementById('mPct');
    var fPct = document.getElementById('fPct');
    var mdPct = document.getElementById('mdPct');
    var mCon = document.getElementById('mCon');
    var fCon = document.getElementById('fCon');
    var mdCon = document.getElementById('mdCon');
    var verdictBox = document.getElementById('verdictBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function num(el) {
        var v = parseFloat(el.value);
        return isNaN(v) ? NaN : v;
    }
    function pct2(x) {
        return (Math.round(x * 100) / 100).toFixed(2) + '%';
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var mo = num(mObt), mt = num(mTot);
        var fo = num(fObt), ft = num(fTot);
        var mdo = num(mdObt), mdt = num(mdTot);
        if (isNaN(mo) || isNaN(mt) || mt <= 0) { showError('Enter Matric obtained and total marks correctly.'); return; }
        if (isNaN(fo) || isNaN(ft) || ft <= 0) { showError('Enter FSc obtained and total marks correctly.'); return; }
        if (isNaN(mdo) || isNaN(mdt) || mdt <= 0) { showError('Enter MDCAT obtained and total marks correctly.'); return; }
        if (mo < 0 || mo > mt) { showError('Matric obtained marks cannot be more than total marks.'); return; }
        if (fo < 0 || fo > ft) { showError('FSc obtained marks cannot be more than total marks.'); return; }
        if (mdo < 0 || mdo > mdt) { showError('MDCAT obtained marks cannot be more than total marks.'); return; }

        var mp = mo / mt * 100;
        var fp = fo / ft * 100;
        var mdp = mdo / mdt * 100;
        var mc = mp * 0.10;
        var fc = fp * 0.40;
        var mdc = mdp * 0.50;
        var agg = mc + fc + mdc;

        mPct.textContent = pct2(mp);
        fPct.textContent = pct2(fp);
        mdPct.textContent = pct2(mdp);
        mCon.textContent = pct2(mc);
        fCon.textContent = pct2(fc);
        mdCon.textContent = pct2(mdc);
        aggOut.textContent = pct2(agg);

        var msg;
        if (agg >= 90) {
            msg = 'Excellent! ' + pct2(agg) + ' aggregate usually comes within the merit of government medical colleges. Still, be sure to check the last closing merit of your province.';
        } else if (agg >= 85) {
            msg = pct2(agg) + ' aggregate is good — government merit may be borderline, chances in private colleges are strong.';
        } else if (agg >= 80) {
            msg = pct2(agg) + ' aggregate gives a good chance of admission in private medical colleges.';
        } else {
            msg = pct2(agg) + ' aggregate — look at BDS or allied health program options, and check the merit of private colleges.';
        }
        verdictBox.textContent = msg + ' (This is only an estimate — actual merit is set by PMDC and universities.)';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
