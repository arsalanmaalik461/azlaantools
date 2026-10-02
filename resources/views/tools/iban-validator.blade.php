@extends('layouts.app')

@section('title', 'IBAN Validator — Free Online Tool')
@section('meta_description', 'Validate IBAN numbers for banks in Pakistan and worldwide')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">IBAN Validator</h1>
            <p class="lead small text-muted">Check any IBAN with the official MOD-97 checksum and country length rules — including Pakistani IBANs (24 characters, starting with PK). Everything is checked locally in your browser.</p>
            <label class="form-label" for="ibIn">IBAN</label>
            <input type="text" class="form-control font-monospace" id="ibIn" placeholder="e.g. PK36 SCBL 0000 0011 2345 6702" autocomplete="off">
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-3"><div class="text-muted small">Result</div><div class="fs-5 fw-bold" id="ibValid">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Country</div><div class="fs-5 fw-bold" id="ibCountry">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Length</div><div class="fs-5 fw-bold" id="ibLen">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Check digits</div><div class="fs-5 fw-bold" id="ibCheck">—</div></div>
                </div>
                <p class="small mb-0 mt-2"><strong>Formatted:</strong> <span class="font-monospace" id="ibFmt">—</span></p>
                <p class="small text-muted mb-0 mt-1" id="ibNote"></p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type or paste the IBAN — spaces are removed automatically.</li><li>The tool checks the length for the country and runs the MOD-97 checksum.</li><li>A green valid result means the IBAN is correctly formed.</li></ol>
            <p class="small text-muted mb-0">Note: A valid checksum does not prove the account exists or belongs to a particular person — always confirm account details with your bank before sending money.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function num(id) { var v = parseFloat(el(id).value); return isNaN(v) ? 0 : v; }
    function fmt(n) { return Number(n).toLocaleString("en-US", { maximumFractionDigits: 2 }); }
    function rs(n) { return "Rs " + Math.round(n).toLocaleString("en-US"); }
    function copyText(id, btn) { var t = el(id); if (!t) return; var v = t.value !== undefined && t.tagName !== "DIV" ? t.value : t.textContent; if (navigator.clipboard) { navigator.clipboard.writeText(v); } if (btn) { var o = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = o; }, 1200); } }
    function download(name, text, type) { var b = new Blob([text], { type: type || "text/plain" }); var a = document.createElement("a"); a.href = URL.createObjectURL(b); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 500); }
    var lengths = { PK: 24, AE: 23, SA: 24, GB: 22, DE: 22, FR: 27, IT: 27, ES: 24, NL: 18, BE: 16, CH: 21, AT: 20, TR: 26, QA: 29, KW: 30, BH: 22, OM: 23, JO: 30, EG: 29, IN: 0 };
    function mod97(s) {
        var rearranged = s.slice(4) + s.slice(0, 4), numStr = "", i, ch;
        for (i = 0; i < rearranged.length; i++) { ch = rearranged.charAt(i); if (/[A-Z]/.test(ch)) numStr += (ch.charCodeAt(0) - 55); else numStr += ch; }
        var rem = 0;
        for (i = 0; i < numStr.length; i += 7) { rem = parseInt(String(rem) + numStr.slice(i, i + 7), 10) % 97; }
        return rem;
    }
    function calc() {
        var s = el("ibIn").value.replace(/\s+/g, "").toUpperCase();
        var note = el("ibNote");
        if (!s) { el("ibValid").textContent = "—"; return; }
        el("ibFmt").textContent = s.replace(/(.{4})/g, "$1 ").trim();
        el("ibCountry").textContent = s.slice(0, 2);
        el("ibCheck").textContent = s.slice(2, 4);
        var exp = lengths[s.slice(0, 2)];
        el("ibLen").textContent = s.length + (exp ? " / " + exp : "");
        if (!/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/.test(s)) { el("ibValid").textContent = "Bad format"; note.textContent = "An IBAN starts with two country letters, two check digits, then letters and digits only."; return; }
        if (exp && s.length !== exp) { el("ibValid").textContent = "Wrong length"; note.textContent = "IBANs from " + s.slice(0, 2) + " must be exactly " + exp + " characters."; return; }
        var ok = mod97(s) === 1;
        el("ibValid").textContent = ok ? "Valid ✓" : "Invalid ✗";
        note.textContent = ok ? "Length and MOD-97 checksum both pass." : "The MOD-97 checksum fails — a digit or letter is likely typed wrong.";
    }
    el("ibIn").addEventListener("input", calc); calc();
})();
</script>
@endsection
