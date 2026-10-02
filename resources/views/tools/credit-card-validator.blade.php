@extends('layouts.app')

@section('title', 'Credit Card Validator — Free Online Tool')
@section('meta_description', 'Validate card numbers with Luhn check and detect the card brand')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Credit Card Validator</h1>
            <p class="lead small text-muted">Check a card number with the Luhn algorithm and detect its brand. Validation runs only in your browser — never enter a real card number on any online tool.</p>
            <label class="form-label" for="ccInput">Card number</label>
            <input type="text" class="form-control font-monospace" id="ccInput" placeholder="e.g. 4242 4242 4242 4242 (test number)" inputmode="numeric" autocomplete="off">
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-3"><div class="text-muted small">Luhn check</div><div class="fs-5 fw-bold" id="ccValid">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Brand</div><div class="fs-5 fw-bold" id="ccBrand">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Digits</div><div class="fs-5 fw-bold" id="ccLen">0</div></div>
                    <div class="col-md-3"><div class="text-muted small">Masked</div><div class="fs-5 fw-bold font-monospace" id="ccMask">—</div></div>
                </div>
                <p class="small text-muted mb-0 mt-2" id="ccNote">Use only test numbers such as 4242 4242 4242 4242 when developing checkout forms.</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type or paste a card number (spaces are ignored).</li><li>The Luhn result, brand and masked number update live.</li><li>Use test numbers only while building and testing payment forms.</li></ol>
            <p class="small text-muted mb-0">Note: A valid Luhn result only means the number is well formed — it does not mean the card exists or has funds. Never type a real card number into any website tool.</p>
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
    function brand(d) {
        if (/^4/.test(d)) return "Visa";
        if (/^(5[1-5]|2[2-7])/.test(d)) return "Mastercard";
        if (/^3[47]/.test(d)) return "American Express";
        if (/^6(011|5|4[4-9])/.test(d)) return "Discover";
        if (/^3(0[0-5]|[68])/.test(d)) return "Diners Club";
        if (/^35/.test(d)) return "JCB";
        if (/^62/.test(d)) return "UnionPay";
        return "Unknown";
    }
    function calc() {
        var d = el("ccInput").value.replace(/[^0-9]/g, "");
        el("ccLen").textContent = d.length;
        el("ccBrand").textContent = d.length >= 2 ? brand(d) : "—";
        el("ccMask").textContent = d.length > 4 ? "•••• " + d.slice(-4) : "—";
        if (d.length < 13) { el("ccValid").textContent = "Too short"; return; }
        var sum = 0, alt = false, i, n;
        for (i = d.length - 1; i >= 0; i--) { n = parseInt(d.charAt(i), 10); if (alt) { n = n * 2; if (n > 9) n -= 9; } sum += n; alt = !alt; }
        el("ccValid").textContent = sum % 10 === 0 ? "Valid ✓" : "Invalid ✗";
    }
    el("ccInput").addEventListener("input", calc); calc();
})();
</script>
@endsection
