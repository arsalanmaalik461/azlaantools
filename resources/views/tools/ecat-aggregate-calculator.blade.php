@extends('layouts.app')

@section('title', 'ECAT Aggregate Calculator - UET Merit - Azlaan Tools')
@section('meta_description', 'Calculate your UET ECAT aggregate online free. Enter Matric, FSc and ECAT scores to find your merit percentage.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">ECAT Aggregate Calculator</h1>
            <p class="lead text-muted">Calculate your UET merit — find the aggregate from your Matric, FSc and ECAT scores. Formula: Matric 25% + FSc 45% + ECAT 30%.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <h2 class="h6 fw-semibold text-muted">Matric (25%)</h2>
                        </div>
                        <div class="col-6">
                            <label for="matricObt" class="form-label fw-semibold">Obtained marks</label>
                            <input type="number" class="form-control" id="matricObt" placeholder="e.g. 980" min="0">
                        </div>
                        <div class="col-6">
                            <label for="matricTotal" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="matricTotal" placeholder="e.g. 1100" min="1">
                        </div>
                        <div class="col-12 mt-3">
                            <h2 class="h6 fw-semibold text-muted">FSc / Intermediate (45%)</h2>
                        </div>
                        <div class="col-6">
                            <label for="fscObt" class="form-label fw-semibold">Obtained marks</label>
                            <input type="number" class="form-control" id="fscObt" placeholder="e.g. 950" min="0">
                        </div>
                        <div class="col-6">
                            <label for="fscTotal" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="fscTotal" placeholder="e.g. 1100" min="1">
                        </div>
                        <div class="col-12 mt-3">
                            <h2 class="h6 fw-semibold text-muted">ECAT Test (30%)</h2>
                        </div>
                        <div class="col-6">
                            <label for="ecatObt" class="form-label fw-semibold">ECAT score</label>
                            <input type="number" class="form-control" id="ecatObt" placeholder="e.g. 320" min="0">
                        </div>
                        <div class="col-6">
                            <label for="ecatTotal" class="form-label fw-semibold">Total marks</label>
                            <input type="number" class="form-control" id="ecatTotal" placeholder="e.g. 400" min="1">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Calculate Aggregate</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="mb-1 text-muted">Your ECAT Aggregate</p>
                                <p class="display-4 fw-bold text-primary mb-1" id="aggResult">0%</p>
                                <div class="row text-center mt-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Matric (25%)</small>
                                        <strong id="mPart">-</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">FSc (45%)</small>
                                        <strong id="fPart">-</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">ECAT (30%)</small>
                                        <strong id="ePart">-</strong>
                                    </div>
                                </div>
                                <p class="small text-muted mt-3 mb-0" id="meritNote"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning small">
                Closing merit changes every year — for admission, check the UET official website and merit lists.
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the obtained and total marks for Matric, FSc and ECAT.</li>
                <li>Press "Calculate Aggregate".</li>
                <li>See the weight of each part and the total aggregate.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var matricObt = document.getElementById('matricObt');
    var matricTotal = document.getElementById('matricTotal');
    var fscObt = document.getElementById('fscObt');
    var fscTotal = document.getElementById('fscTotal');
    var ecatObt = document.getElementById('ecatObt');
    var ecatTotal = document.getElementById('ecatTotal');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var aggResult = document.getElementById('aggResult');
    var mPart = document.getElementById('mPart');
    var fPart = document.getElementById('fPart');
    var ePart = document.getElementById('ePart');
    var meritNote = document.getElementById('meritNote');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function pct(obt, total) {
        if (total <= 0 || obt < 0 || obt > total) return null;
        return (obt / total) * 100;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var mo = parseFloat(matricObt.value);
        var mt = parseFloat(matricTotal.value);
        var fo = parseFloat(fscObt.value);
        var ft = parseFloat(fscTotal.value);
        var eo = parseFloat(ecatObt.value);
        var et = parseFloat(ecatTotal.value);

        if (isNaN(mo) || isNaN(mt) || isNaN(fo) || isNaN(ft) || isNaN(eo) || isNaN(et)) {
            showError('Please fill in all marks fields.');
            return;
        }
        var mp = pct(mo, mt);
        var fp = pct(fo, ft);
        var ep = pct(eo, et);
        if (mp === null || fp === null || ep === null) {
            showError('Marks are wrong — obtained marks cannot be more than total marks.');
            return;
        }

        var mW = mp * 0.25;
        var fW = fp * 0.45;
        var eW = ep * 0.30;
        var agg = mW + fW + eW;

        aggResult.textContent = agg.toFixed(2) + '%';
        mPart.textContent = mp.toFixed(2) + '% (' + mW.toFixed(2) + ')';
        fPart.textContent = fp.toFixed(2) + '% (' + fW.toFixed(2) + ')';
        ePart.textContent = ep.toFixed(2) + '% (' + eW.toFixed(2) + ')';

        var note;
        if (agg >= 80) {
            note = 'Excellent aggregate! You are a strong candidate for top engineering programs.';
        } else if (agg >= 70) {
            note = 'Good aggregate — you have a chance in many engineering departments.';
        } else if (agg >= 60) {
            note = 'Average aggregate — check the merit lists, you may have a chance in some fields.';
        } else {
            note = 'Aggregate is low — consider retaking ECAT or looking at other options.';
        }
        meritNote.textContent = note;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
