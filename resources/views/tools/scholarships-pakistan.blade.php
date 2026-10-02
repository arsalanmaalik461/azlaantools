@extends('layouts.app')

@section('title', 'Scholarships in Pakistan - Azlaan Tools')
@section('meta_description', 'List of scholarships in Pakistan with official links — HEC, provincial and need-based scholarships for students. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Scholarships in Pakistan</h1>
            <p class="lead text-muted">List of popular scholarships in Pakistan — with links to official portals only.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search, e.g. need based, PhD, Punjab...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="levelFilter">
                                <option value="all">All levels</option>
                                <option value="School">School</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Undergraduate">Undergraduate</option>
                                <option value="Masters">Masters / MPhil</option>
                                <option value="PhD">PhD</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none"></div>
                </div>
            </div>

            <div class="row" id="schList"></div>

            <div class="alert alert-warning mt-4">
                <strong>Important note:</strong> Deadlines and conditions change every year — always confirm on the official website before applying. This page does not verify any scholarship's live status, and do not pay money to any agent.
            </div>

            <h2>Checklist before applying</h2>
            <ol>
                <li>Read the eligibility (marks, domicile, income limit) in the official ad.</li>
                <li>Keep your CNIC/B-Form, domicile and academic documents ready.</li>
                <li>Apply only on the official portal — stay away from WhatsApp or Facebook agents.</li>
                <li>Apply before the deadline; late applications are not accepted.</li>
            </ol>
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
    var schList = document.getElementById('schList');
    var searchInput = document.getElementById('searchInput');
    var levelFilter = document.getElementById('levelFilter');

    var scholarships = [
        { name: 'HEC Need Based Scholarship', level: 'Undergraduate', who: 'Deserving undergraduate students (partner universities)', link: 'https://www.hec.gov.pk', desc: 'HEC need-based scholarship — apply through the financial aid offices of partner universities.' },
        { name: 'USAID Merit and Need Based Scholarship', level: 'Undergraduate', who: 'Deserving students with merit', link: 'https://www.hec.gov.pk', desc: 'USAID funded program given on merit + need at HEC partner universities.' },
        { name: 'HEC Indigenous PhD Fellowship', level: 'PhD', who: 'Pakistani PhD scholars', link: 'https://www.hec.gov.pk', desc: 'HEC fellowship program for those doing a PhD at Pakistani universities.' },
        { name: 'HEC Overseas Scholarships', level: 'Masters', who: 'Those studying abroad for Masters / PhD', link: 'https://www.hec.gov.pk', desc: 'Scholarship programs for higher studies abroad through HEC (schedule on the official site).' },
        { name: 'PEEF Scholarships (Punjab)', level: 'Intermediate', who: 'Deserving students with Punjab domicile and 60%+ marks', link: 'https://www.peef.org.pk', desc: 'Punjab Educational Endowment Fund — merit/need scholarships from Intermediate to Masters.' },
        { name: 'PEEF Special Quota Scholarship', level: 'Intermediate', who: 'Orphan, special-needs and minority students (Punjab)', link: 'https://www.peef.org.pk', desc: 'Special PEEF scholarship for special quota categories — apply online.' },
        { name: 'BISP Taleemi Wazaif', level: 'School', who: 'Children of BISP deserving families', link: 'https://www.bisp.gov.pk', desc: 'Quarterly education stipends for school-going children (BISP program).' },
        { name: 'Ehsaas Undergraduate Scholarship', level: 'Undergraduate', who: 'Deserving undergraduate students', link: 'https://www.bisp.gov.pk', desc: 'Stipends for undergraduate students under the Ehsaas program — details on the official portal.' },
        { name: 'University Financial Aid Offices', level: 'Undergraduate', who: 'Students of each university', link: '', desc: 'Many universities give need-based aid from their own funds — contact your university financial aid office.' }
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function render() {
        hideError();
        var q = searchInput.value.trim().toLowerCase();
        var lvl = levelFilter.value;
        var shown = 0;
        schList.innerHTML = '';
        scholarships.forEach(function (s) {
            var hay = (s.name + ' ' + s.who + ' ' + s.desc + ' ' + s.level).toLowerCase();
            if (lvl !== 'all' && s.level !== lvl) { return; }
            if (q && hay.indexOf(q) === -1) { return; }
            shown++;
            var col = document.createElement('div');
            col.className = 'col-md-6 mb-3';
            var card = document.createElement('div');
            card.className = 'card h-100 shadow-sm';
            var body = document.createElement('div');
            body.className = 'card-body';
            var badge = document.createElement('span');
            badge.className = 'badge bg-primary mb-2';
            badge.textContent = s.level;
            var h = document.createElement('h5');
            h.className = 'card-title';
            h.textContent = s.name;
            var who = document.createElement('p');
            who.className = 'card-text mb-1';
            var strong = document.createElement('strong');
            strong.textContent = 'Who can apply: ';
            who.appendChild(strong);
            who.appendChild(document.createTextNode(s.who));
            var desc = document.createElement('p');
            desc.className = 'card-text text-muted small';
            desc.textContent = s.desc;
            body.appendChild(badge);
            body.appendChild(h);
            body.appendChild(who);
            body.appendChild(desc);
            if (s.link) {
                var a = document.createElement('a');
                a.href = s.link;
                a.target = '_blank';
                a.rel = 'noopener';
                a.className = 'btn btn-sm btn-outline-primary';
                a.textContent = 'Official Website';
                body.appendChild(a);
            } else {
                var span = document.createElement('span');
                span.className = 'text-muted small';
                span.textContent = 'Contact your university financial aid office.';
                body.appendChild(span);
            }
            card.appendChild(body);
            col.appendChild(card);
            schList.appendChild(col);
        });
        results.classList.remove('d-none');
        if (shown === 0) {
            showError('No scholarships found. Change the search or filter and try again.');
        }
    }

    searchInput.addEventListener('input', render);
    levelFilter.addEventListener('change', render);

    render();
})();
</script>
@endsection
