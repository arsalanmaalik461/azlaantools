@extends('layouts.app')

@section('title', 'Punjab Land Record Fard Guide - Azlaan Tools')
@section('meta_description', 'Step by step guide to check and download your fard from the PLRA land record portal online, free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Punjab Land Record Fard Guide</h1>
            <p class="lead text-muted">A complete step-by-step guide to check and download your land fard (ownership record) from the PLRA online portal in Punjab.</p>

            <div class="alert alert-warning" role="alert">
                This is only a guide — this tool does <strong>not</strong> do live verification. Always confirm the real fard details from the official PLRA portal or your nearest Land Record Center.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Official links (real government portals)</h2>
                    <ul class="mb-0">
                        <li><strong>PLRA Online Land Record:</strong> <a href="https://landrecord.punjab.gov.pk" target="_blank" rel="noopener">landrecord.punjab.gov.pk</a> — online record search</li>
                        <li><strong>PLRA (Punjab Land Records Authority):</strong> <a href="https://plra.punjab.gov.pk" target="_blank" rel="noopener">plra.punjab.gov.pk</a> — the official website of the authority</li>
                        <li><strong>PLRA Helpline:</strong> 042-111-222-277 (confirm from the official website)</li>
                    </ul>
                    <p class="small text-muted mt-2 mb-0">The portal record is for information only. For a verified (stamped) fard, get it from a Land Record Center (LRC).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Step-by-step: Check the online record</h2>
                    <ol>
                        <li>Open <strong>landrecord.punjab.gov.pk</strong>.</li>
                        <li>Select your <strong>District</strong>.</li>
                        <li>Then select <strong>Tehsil</strong> and <strong>Mauza</strong> (village/area).</li>
                        <li>Choose the search method: <strong>Khewat number</strong>, <strong>Khatoni number</strong>, or <strong>Khasra number</strong> — you can find these on your old fard or registry.</li>
                        <li>Enter the number and press search. The record will appear on screen — you will see the owner name, share, and khasra details.</li>
                        <li>Print this page or take a screenshot for your record. Remember: this is only an online copy; for legal work (registry, mutation) you need the verified fard from LRC.</li>
                    </ol>

                    <h2 class="h5 mt-4">To get a verified fard from LRC</h2>
                    <ol>
                        <li>Visit the nearest <strong>Land Record Center</strong> (in your district or tehsil).</li>
                        <li>Take a token and tell them your khasra/khatoni number.</li>
                        <li>Pay the required fee (fees can change — <strong>confirm on the official website or helpline</strong>).</li>
                        <li>You will get the computerized, stamped fard from there. Take your CNIC with you.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-2">Checklist before getting your fard</h2>
                    <p class="small text-muted">Tick as you go — your progress is saved in your browser.</p>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc1">
                        <label class="form-check-label" for="fc1">I know my district, tehsil and mauza name</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc2">
                        <label class="form-check-label" for="fc2">I have found my khewat, khatoni or khasra number</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc3">
                        <label class="form-check-label" for="fc3">I have checked the online record on landrecord.punjab.gov.pk</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc4">
                        <label class="form-check-label" for="fc4">I have confirmed the owner name and share in the record</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc5">
                        <label class="form-check-label" for="fc5">CNIC is ready (for the LRC visit)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input fard-check" type="checkbox" id="fc6">
                        <label class="form-check-label" for="fc6">Fee confirmed from the official helpline</label>
                    </div>
                    <div class="progress mt-3" style="height:22px">
                        <div class="progress-bar bg-success" id="fcProgress" role="progressbar" style="width:0%">0 / 6</div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="fcReset">Reset checklist</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-3"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Check your online record from the official portal link above.</li>
                <li>Tick the checklist as you go so no step is missed.</li>
                <li>For a verified fard, visit the nearest Land Record Center with your CNIC.</li>
            </ol>
            <p class="small text-muted">Rates/fee can change — confirm on the official website. This guide is for information only, not legal advice.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var checks = Array.prototype.slice.call(document.querySelectorAll('.fard-check'));
    var prog = document.getElementById('fcProgress');
    var resetBtn = document.getElementById('fcReset');
    var KEY = 'fard_checklist_v1';

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function update() {
        var done = checks.filter(function (c) { return c.checked; }).length;
        var pct = Math.round(done / checks.length * 100);
        prog.style.width = pct + '%';
        prog.textContent = done + ' / ' + checks.length;
        results.classList.remove('d-none');
        if (done === checks.length) {
            results.innerHTML = '<div class="alert alert-success mb-0">All done. Your preparation is complete — the LRC visit or online record check will be easy now.</div>';
        } else {
            results.innerHTML = '';
        }
    }
    function save() {
        try {
            var state = {};
            checks.forEach(function (c) { state[c.id] = c.checked; });
            localStorage.setItem(KEY, JSON.stringify(state));
        } catch (e) { showError('Could not save in the browser, but the checklist will still work.'); }
    }
    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (!raw) return;
            var state = JSON.parse(raw);
            checks.forEach(function (c) { if (state.hasOwnProperty(c.id)) c.checked = !!state[c.id]; });
        } catch (e) { /* ignore */ }
    }
    checks.forEach(function (c) {
        c.addEventListener('change', function () { hideError(); save(); update(); });
    });
    resetBtn.addEventListener('click', function () {
        hideError();
        checks.forEach(function (c) { c.checked = false; });
        try { localStorage.removeItem(KEY); } catch (e) {}
        update();
    });
    load();
    update();
})();
</script>
@endsection
