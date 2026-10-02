@extends('layouts.app')

@section('title', 'JWT Encoder — Free Online Tool')
@section('meta_description', 'Create and sign JSON Web Tokens with header payload and secret')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">JWT Encoder</h1>
            <p class="lead small text-muted">Build and sign a JSON Web Token with HMAC (HS256, HS384 or HS512) using the Web Crypto API — signing happens entirely in your browser, and the secret never leaves this page.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="jeHeader">Header (JSON)</label><textarea class="form-control font-monospace" id="jeHeader" rows="4">{&quot;alg&quot;:&quot;HS256&quot;,&quot;typ&quot;:&quot;JWT&quot;}</textarea></div>
                <div class="col-md-6"><label class="form-label" for="jePayload">Payload (JSON)</label><textarea class="form-control font-monospace" id="jePayload" rows="4">{&quot;sub&quot;:&quot;1234567890&quot;,&quot;name&quot;:&quot;Ali Khan&quot;}</textarea></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6"><label class="form-label" for="jeSecret">Secret</label><input type="text" class="form-control font-monospace" id="jeSecret" value="your-256-bit-secret" autocomplete="off"></div>
                <div class="col-md-3"><label class="form-label" for="jeAlg">Algorithm</label><select class="form-select" id="jeAlg"><option value="HS256">HS256</option><option value="HS384">HS384</option><option value="HS512">HS512</option></select></div>
                <div class="col-md-3 d-flex align-items-end"><button type="button" class="btn btn-primary w-100" id="jeBtn">Sign token</button></div>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="jeErr"></div>
            <label class="form-label mt-3" for="jeOut">Signed JWT</label>
            <textarea class="form-control font-monospace" id="jeOut" rows="4" readonly></textarea>
            <button type="button" class="btn btn-success btn-sm mt-2" id="jeCopy">Copy token</button>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Edit the header and payload JSON — change the alg field if you pick a different algorithm below.</li><li>Enter the signing secret and choose HS256, HS384 or HS512.</li><li>Click Sign token and copy the resulting JWT.</li></ol>
            <p class="small text-muted mb-0">Note: HMAC secrets must stay secret — anyone holding the secret can forge tokens. This tool is for development and testing; production tokens should be signed on a server.</p>
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
    function b64url(bytes) { var bin = ""; bytes.forEach(function (b) { bin += String.fromCharCode(b); }); return btoa(bin).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, ""); }
    function b64urlText(text) { return b64url(new TextEncoder().encode(text)); }
    el("jeAlg").addEventListener("change", function () { try { var h = JSON.parse(el("jeHeader").value); h.alg = el("jeAlg").value; el("jeHeader").value = JSON.stringify(h); } catch (e) {} });
    el("jeBtn").addEventListener("click", function () {
        var err = el("jeErr"); err.classList.add("d-none"); el("jeOut").value = "";
        var header, payload;
        try { header = JSON.parse(el("jeHeader").value); payload = JSON.parse(el("jePayload").value); }
        catch (e) { err.textContent = "Invalid JSON: " + e.message; err.classList.remove("d-none"); return; }
        if (!window.crypto || !crypto.subtle) { err.textContent = "Web Crypto is not available in this browser context."; err.classList.remove("d-none"); return; }
        header.alg = el("jeAlg").value;
        var hash = { HS256: "SHA-256", HS384: "SHA-384", HS512: "SHA-512" }[el("jeAlg").value];
        var input = b64urlText(JSON.stringify(header)) + "." + b64urlText(JSON.stringify(payload));
        var enc = new TextEncoder();
        crypto.subtle.importKey("raw", enc.encode(el("jeSecret").value), { name: "HMAC", hash: hash }, false, ["sign"]).then(function (key) {
            return crypto.subtle.sign("HMAC", key, enc.encode(input));
        }).then(function (sig) {
            el("jeOut").value = input + "." + b64url(new Uint8Array(sig));
        }).catch(function (e2) { err.textContent = "Signing failed: " + e2.message; err.classList.remove("d-none"); });
    });
    el("jeCopy").addEventListener("click", function () { copyText("jeOut", el("jeCopy")); });
})();
</script>
@endsection
