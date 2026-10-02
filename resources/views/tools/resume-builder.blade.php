@extends('layouts.app')

@section('title', 'Resume / CV Builder - Free CV Maker & PDF | Azlaan Tools')
@section('meta_description', 'Free resume and CV builder. Fill the form, see a live preview and print or save as PDF. No signup, your data is never saved on any server.')

@section('styles')
<style>
#cvPreview { background: #fff; color: #222; font-family: Georgia, serif; }
#cvPreview h2 { border-bottom: 2px solid #0d6efd; padding-bottom: 6px; }
@media print {
    body * { visibility: hidden; }
    #cvPreview, #cvPreview * { visibility: visible; }
    #cvPreview { position: absolute; top: 0; left: 0; width: 100%; box-shadow: none !important; }
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Resume / CV Builder</h1>
    <p class="lead text-muted">Fill the form and get a clean, professional CV — print it or save as PDF, free with no signup. Your data is NOT saved; it clears on refresh.</p>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label">Full Name</label><input class="form-control cv-in" id="cvName" placeholder="Your name"></div>
                    <div class="col-md-6"><label class="form-label">Job Title</label><input class="form-control cv-in" id="cvTitle" placeholder="e.g. Electrician"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control cv-in" id="cvEmail" placeholder="you@example.com"></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control cv-in" id="cvPhone" placeholder="03xx-xxxxxxx"></div>
                    <div class="col-12"><label class="form-label">City</label><input class="form-control cv-in" id="cvCity" placeholder="City, Pakistan"></div>
                    <div class="col-12"><label class="form-label">Summary / Objective</label><textarea class="form-control cv-in" id="cvSummary" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">Skills (comma separated)</label><input class="form-control cv-in" id="cvSkills" placeholder="Solar installation, Wiring, CCTV"></div>
                    <div class="col-12"><label class="form-label">Languages</label><input class="form-control cv-in" id="cvLang" placeholder="Urdu, English"></div>
                </div>
                <h3 class="h6 mt-4">Education</h3><div id="eduRows"></div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addEdu">+ Add Education</button>
                <h3 class="h6 mt-4">Experience</h3><div id="expRows"></div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addExp">+ Add Experience</button>
                <div class="mt-4"><button type="button" class="btn btn-success" onclick="window.print()">Print / Save PDF</button></div>
            </div></div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow" id="cvPreview"><div class="card-body p-4">
                <h2 class="mb-0" id="pvName">Your Name</h2>
                <div class="text-primary fw-semibold" id="pvTitle">Job Title</div>
                <div class="small text-muted" id="pvContact"></div>
                <h3 class="h6 text-uppercase mt-4">Summary</h3><p id="pvSummary" class="small"></p>
                <h3 class="h6 text-uppercase mt-3">Education</h3><div id="pvEdu" class="small"></div>
                <h3 class="h6 text-uppercase mt-3">Experience</h3><div id="pvExp" class="small"></div>
                <h3 class="h6 text-uppercase mt-3">Skills</h3><p id="pvSkills" class="small"></p>
                <h3 class="h6 text-uppercase mt-3">Languages</h3><p id="pvLang" class="small"></p>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4"><div class="card-body">
        <h2>How to use</h2>
        <ol><li>Fill your personal details, summary, skills and languages.</li><li>Add education and experience rows as needed — the preview updates live.</li><li>Click Print / Save PDF and choose Save as PDF in the print dialog.</li></ol>
    </div></div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function esc(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
    function rowHtml(kind) { return '<div class="row g-2 mb-2 ' + kind + '-row"><div class="col-5"><input class="form-control form-control-sm f1" placeholder="' + (kind === 'edu' ? 'Degree / School' : 'Job Title / Company') + '"></div><div class="col-3"><input class="form-control form-control-sm f2" placeholder="Years e.g. 2020-2024"></div><div class="col-3"><input class="form-control form-control-sm f3" placeholder="Details"></div><div class="col-1"><button type="button" class="btn btn-outline-danger btn-sm rm">×</button></div></div>'; }
    function addRow(kind) { var div = document.createElement('div'); div.innerHTML = rowHtml(kind); var el = div.firstChild; el.querySelector('.rm').addEventListener('click', function () { el.remove(); render(); }); el.querySelectorAll('input').forEach(function (i) { i.addEventListener('input', render); }); document.getElementById(kind === 'edu' ? 'eduRows' : 'expRows').appendChild(el); }
    function collect(kind) { var out = ''; document.querySelectorAll('.' + kind + '-row').forEach(function (r) { var a = r.querySelector('.f1').value, b = r.querySelector('.f2').value, c = r.querySelector('.f3').value; if (a || b || c) out += '<p class="mb-1"><strong>' + esc(a) + '</strong> ' + esc(b) + (c ? ' — ' + esc(c) : '') + '</p>'; }); return out || '<p class="text-muted">—</p>'; }
    function render() {
        document.getElementById('pvName').textContent = document.getElementById('cvName').value || 'Your Name';
        document.getElementById('pvTitle').textContent = document.getElementById('cvTitle').value || 'Job Title';
        var contact = [document.getElementById('cvEmail').value, document.getElementById('cvPhone').value, document.getElementById('cvCity').value].filter(Boolean).join(' | ');
        document.getElementById('pvContact').textContent = contact;
        document.getElementById('pvSummary').textContent = document.getElementById('cvSummary').value;
        document.getElementById('pvSkills').textContent = document.getElementById('cvSkills').value;
        document.getElementById('pvLang').textContent = document.getElementById('cvLang').value;
        document.getElementById('pvEdu').innerHTML = collect('edu');
        document.getElementById('pvExp').innerHTML = collect('exp');
        try { localStorage.setItem('cvDraft', JSON.stringify({ n: document.getElementById('cvName').value })); } catch (e) {}
    }
    document.querySelectorAll('.cv-in').forEach(function (i) { i.addEventListener('input', render); });
    document.getElementById('addEdu').addEventListener('click', function () { addRow('edu'); });
    document.getElementById('addExp').addEventListener('click', function () { addRow('exp'); });
    addRow('edu'); addRow('exp'); render();
})();
</script>
@endsection
