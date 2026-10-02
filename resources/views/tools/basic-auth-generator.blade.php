@extends('layouts.app')

@section('title', 'Basic Auth Header Generator — Free Online Tool')
@section('meta_description', 'Generate HTTP Basic Authorization headers from username and password')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Basic Auth Header Generator</h1>
            <p class="lead small text-muted">Enter a username and password to build the HTTP Basic Authorization header used by API tools and curl. Everything runs locally in your browser — the password never leaves this page.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="baUser">Username</label><input type="text" class="form-control" id="baUser" placeholder="e.g. admin" autocomplete="off"></div>
                <div class="col-md-6"><label class="form-label" for="baPass">Password</label><input type="password" class="form-control" id="baPass" placeholder="Password" autocomplete="off"></div>
            </div>
            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="baShow"><label class="form-check-label" for="baShow">Show password</label></div>
            <div class="alert alert-warning mt-3 d-none" id="baMsg"></div>
            <label class="form-label mt-2" for="baOut">Authorization header</label>
            <textarea class="form-control font-monospace" id="baOut" rows="2" readonly></textarea>
            <label class="form-label mt-3" for="baCurl">curl example</label>
            <textarea class="form-control font-monospace" id="baCurl" rows="2" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="baCopy">Copy header</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter the username and password.</li><li>Copy the generated Authorization header into your API client or code.</li><li>Use the curl example to test the credentials from a terminal.</li></ol>
            <p class="small text-muted mb-0">Note: Basic Auth is only Base64 encoding, not encryption — always use it over HTTPS. Credentials are processed only in your browser and are never sent anywhere.</p>
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
    function b64(text) { var bytes = new TextEncoder().encode(text); var bin = ""; bytes.forEach(function (b) { bin += String.fromCharCode(b); }); return btoa(bin); }
    function calc() {
        var u = el("baUser").value, p = el("baPass").value, msg = el("baMsg");
        if (!u) { msg.textContent = "Enter a username to generate the header."; msg.classList.remove("d-none"); el("baOut").value = ""; el("baCurl").value = ""; return; }
        msg.classList.add("d-none");
        var enc = b64(u + ":" + p);
        el("baOut").value = "Authorization: Basic " + enc;
        el("baCurl").value = "curl -H \"Authorization: Basic " + enc + "\" https://example.com/api";
    }
    ["baUser", "baPass"].forEach(function (id) { el(id).addEventListener("input", calc); });
    el("baShow").addEventListener("change", function () { el("baPass").type = el("baShow").checked ? "text" : "password"; });
    el("baCopy").addEventListener("click", function () { copyText("baOut", el("baCopy")); });
    calc();
})();
</script>
@endsection
