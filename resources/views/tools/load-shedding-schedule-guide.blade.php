@extends('layouts.app')

@section('title', 'Load Shedding Schedule Guide - Azlaan Tools')
@section('meta_description', 'How to check the load shedding schedule for your area: find the feeder code from your bill and use official DISCO portal links.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Load Shedding Schedule Guide</h1>
            <p class="lead text-muted">The right official way to check the power schedule (load shedding timing) for your area — learn how to find the feeder code from your bill and open your DISCO portal.</p>

            <div class="alert alert-warning">
                <strong>Important note:</strong> The load shedding schedule can change daily or weekly (weather, demand, maintenance). This page does not show a live schedule — always check the latest schedule from the official portals below.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Step 1: Choose your DISCO</h2>
                    <p class="text-muted small">DISCO = the company that gives electricity to your area. The company name is written on your bill.</p>
                    <div class="mb-3">
                        <label for="discoSelect" class="form-label fw-semibold">Your electricity company</label>
                        <select class="form-select" id="discoSelect">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-3"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Step 2: Find the feeder name / code from your bill</h2>
                    <ol class="mb-0">
                        <li>Open your <strong>electricity bill</strong> (paper or online).</li>
                        <li>The bill shows a <strong>feeder name</strong> or <strong>sub-division</strong> — this is the schedule group for your area.</li>
                        <li>Some bills also show a feeder code near the <strong>reference number</strong>.</li>
                        <li>Remember or note this name — use it to find your timing in the DISCO feeder-wise schedule list.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Step 3: See the schedule on the official portal</h2>
                    <ol class="mb-0">
                        <li>Open the <strong>official website</strong> of the DISCO you selected above (link is given below).</li>
                        <li>On the website, find the <strong>"Load Management"</strong>, <strong>"Shutdown Schedule"</strong> or <strong>"Feeder-wise Schedule"</strong> section.</li>
                        <li>Note your area timing using your feeder name.</li>
                        <li>If you cannot find the schedule, ask on the DISCO official <strong>Facebook page</strong> or helpline — do not trust unofficial WhatsApp forwards.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Common questions</h2>
                    <p class="mb-2"><strong>Does the schedule change daily?</strong><br>
                    <span class="text-muted">Yes, timing can change because of heat/cold, demand and grid maintenance. Check once a week.</span></p>
                    <p class="mb-2"><strong>What is a feeder?</strong><br>
                    <span class="text-muted">The electricity line coming from a grid station. Many streets share one feeder — the schedule is made per feeder, not per house.</span></p>
                    <p class="mb-0"><strong>Cannot find the feeder name on your bill?</strong><br>
                    <span class="text-muted">Call your DISCO helpline with your reference number, or ask at the nearest sub-division office.</span></p>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your electricity company (DISCO) above.</li>
                <li>Get the official website link and steps.</li>
                <li>Find your feeder name from your bill and search the schedule list.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var discoSelect = document.getElementById('discoSelect');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    var discos = [
        { id: 'lesco', name: 'LESCO — Lahore Electric Supply Company', area: 'Lahore, Kasur, Sheikhupura, Okara, Nankana Sahib', site: 'https://www.lesco.gov.pk', tip: 'See the feeder-wise schedule in the "Customer Services" or "Load Management Schedule" section of the website. Helpline: 118.' },
        { id: 'iesco', name: 'IESCO — Islamabad Electric Supply Company', area: 'Islamabad, Rawalpindi, Attock, Jhelum, Chakwal', site: 'https://www.iesco.com.pk', tip: 'Open the "Shutdown Schedule" or "Load Management" page on the website and select your circle / sub-division.' },
        { id: 'mepco', name: 'MEPCO — Multan Electric Power Company', area: 'Multan, Bahawalpur, D.G. Khan, Muzaffargarh, Rahim Yar Khan', site: 'https://www.mepco.com.pk', tip: 'The "Load Shedding Schedule" section has a circle-wise feeder list.' },
        { id: 'fesco', name: 'FESCO — Faisalabad Electric Supply Company', area: 'Faisalabad, Jhang, Toba Tek Singh, Sargodha, Mianwali', site: 'https://www.fesco.com.pk', tip: 'See the "Feeder Wise Load Management Schedule" on the website or call helpline 0800-66554.' },
        { id: 'gepco', name: 'GEPCO — Gujranwala Electric Power Company', area: 'Gujranwala, Sialkot, Narowal, Gujrat, Hafizabad', site: 'https://www.gepco.com.pk', tip: 'Check your operation circle schedule on the "Load Management Schedule" page.' },
        { id: 'pesco', name: 'PESCO — Peshawar Electric Supply Company', area: 'Peshawar, Mardan, Swabi, Kohat, Bannu, D.I. Khan', site: 'https://www.pesco.com.pk', tip: 'See the feeder-wise timing in the "Shutdown Program" section of the website.' },
        { id: 'hesco', name: 'HESCO — Hyderabad Electric Supply Company', area: 'Hyderabad, Jamshoro, Badin, Thatta, Sanghar, Mirpurkhas', site: 'https://www.hesco.gov.pk', tip: 'See the "Load Shedding Schedule" or "Shutdown" notices on the website.' },
        { id: 'sepco', name: 'SEPCO — Sukkur Electric Power Company', area: 'Sukkur, Larkana, Jacobabad, Shikarpur, Khairpur', site: 'https://www.sepco.com.pk', tip: 'Find your circle schedule in the "Load Management" section of the website.' },
        { id: 'qesco', name: 'QESCO — Quetta Electric Supply Company', area: 'Quetta and most areas of Balochistan', site: 'https://www.qesco.com.pk', tip: 'Check the "Load Shedding Schedule" page on the website.' },
        { id: 'tesco', name: 'TESCO — Tribal Electric Supply Company', area: 'Merged tribal districts (ex-FATA)', site: 'https://www.tesco.gov.pk', tip: 'See the announcements / schedule section on the website.' },
        { id: 'ke', name: 'K-Electric', area: 'Karachi and nearby areas', site: 'https://www.ke.com.pk', tip: 'The "Load Shed Schedule" is available area-wise on the K-Electric website and app. SMS service: send your account number to 8119 (service charges may apply).' }
    ];

    discos.forEach(function (d) {
        var opt = document.createElement('option');
        opt.value = d.id;
        opt.textContent = d.name;
        discoSelect.appendChild(opt);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    discoSelect.addEventListener('change', function () {
        hideError();
        var id = discoSelect.value;
        if (!id) { results.classList.add('d-none'); results.innerHTML = ''; return; }
        var d = null;
        for (var i = 0; i < discos.length; i++) { if (discos[i].id === id) { d = discos[i]; break; } }
        if (!d) { showError('DISCO not found. Please select again.'); return; }
        results.innerHTML =
            '<div class="border rounded p-3 bg-light">' +
            '<h3 class="h6">' + d.name + '</h3>' +
            '<p class="small text-muted mb-2">Coverage: ' + d.area + '</p>' +
            '<p class="small mb-2"><strong>Official website:</strong> <a href="' + d.site + '" target="_blank" rel="noopener">' + d.site.replace('https://www.', '') + '</a></p>' +
            '<p class="small mb-0">' + d.tip + '</p>' +
            '</div>' +
            '<p class="small text-muted mt-2">This link only opens the official portal — the DISCO updates the schedule itself; this tool does not verify live timing.</p>';
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
