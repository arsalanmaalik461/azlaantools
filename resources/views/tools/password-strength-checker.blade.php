@extends('layouts.app')

@section('title', 'Password Strength Checker — Free Online Tool')
@section('meta_description', 'Check password strength with entropy score and crack time estimate')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Password Strength Checker</h1>
            <p class="lead small text-muted">See how strong a password really is: entropy in bits, an honest crack-time estimate and concrete suggestions. The password is analysed locally and never sent anywhere — still, test with a similar password, not your real one.</p>
            <label class="form-label" for="pwIn">Password to test</label>
            <input type="password" class="form-control font-monospace" id="pwIn" placeholder="Type a password to test" autocomplete="off">
            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="pwShow"><label class="form-check-label" for="pwShow">Show password</label></div>
            <div class="progress mt-3" style="height:12px"><div class="progress-bar" id="pwBar" style="width:0%"></div></div>
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-3"><div class="text-muted small">Strength</div><div class="fs-5 fw-bold" id="pwLabel">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Entropy</div><div class="fs-5 fw-bold" id="pwEntropy">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Length</div><div class="fs-5 fw-bold" id="pwLen">0</div></div>
                    <div class="col-md-3"><div class="text-muted small">Crack time (offline, fast GPU)</div><div class="fs-5 fw-bold" id="pwCrack">—</div></div>
                </div>
                <ul class="small mb-0 mt-2" id="pwTips"></ul>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Type the password you want to evaluate.</li><li>Read the entropy, strength label and crack-time estimate.</li><li>Follow the suggestions to make it stronger — length matters most.</li></ol>
            <p class="small text-muted mb-0">Note: Crack time assumes an offline attack at 10 billion guesses per second against a fast hash. Passwords reused from a leaked site fall instantly regardless of entropy — use a unique password per site and a password manager.</p>
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
    var common = ["password", "123456", "12345678", "qwerty", "letmein", "admin", "pakistan", "iloveyou", "123456789", "password1"];
    function humanTime(sec) {
        if (!isFinite(sec)) return "Effectively forever";
        if (sec < 1) return "Instant";
        if (sec < 60) return Math.round(sec) + " seconds";
        if (sec < 3600) return Math.round(sec / 60) + " minutes";
        if (sec < 86400) return Math.round(sec / 3600) + " hours";
        if (sec < 31536000) return Math.round(sec / 86400) + " days";
        var y = sec / 31536000;
        if (y < 1000) return Math.round(y) + " years";
        if (y < 1000000) return (y / 1000).toFixed(1) + " thousand years";
        if (y < 1000000000) return (y / 1000000).toFixed(1) + " million years";
        return (y / 1000000000).toFixed(1) + " billion years";
    }
    function calc() {
        var p = el("pwIn").value, tips = [];
        el("pwLen").textContent = p.length;
        if (!p) { el("pwLabel").textContent = "—"; el("pwEntropy").textContent = "—"; el("pwCrack").textContent = "—"; el("pwBar").style.width = "0%"; el("pwTips").innerHTML = ""; return; }
        var pool = 0;
        if (/[a-z]/.test(p)) pool += 26; else tips.push("Add lowercase letters.");
        if (/[A-Z]/.test(p)) pool += 26; else tips.push("Add uppercase letters.");
        if (/[0-9]/.test(p)) pool += 10; else tips.push("Add digits.");
        if (/[^A-Za-z0-9]/.test(p)) pool += 33; else tips.push("Add symbols such as ! @ # $.");
        if (p.length < 12) tips.push("Make it at least 12 characters — length beats complexity.");
        if (common.indexOf(p.toLowerCase()) >= 0) tips.push("This is a very common password — never use it.");
        if (/(.)\1{2,}/.test(p)) tips.push("Avoid repeated characters.");
        if (/012|123|234|345|456|567|678|789|abc|bcd|cde|qwerty/i.test(p)) tips.push("Avoid sequences like 123 or abc.");
        var entropy = pool ? p.length * (Math.log(pool) / Math.log(2)) : 0;
        if (common.indexOf(p.toLowerCase()) >= 0) entropy = Math.min(entropy, 8);
        var combos = Math.pow(2, entropy), seconds = combos / 2 / 10000000000;
        el("pwEntropy").textContent = entropy.toFixed(1) + " bits";
        el("pwCrack").textContent = humanTime(seconds);
        var score = entropy < 28 ? 0 : entropy < 40 ? 1 : entropy < 60 ? 2 : entropy < 80 ? 3 : 4;
        var labels = ["Very weak", "Weak", "Fair", "Strong", "Excellent"], colors = ["#dc3545", "#fd7e14", "#ffc107", "#198754", "#0d6efd"];
        el("pwLabel").textContent = labels[score];
        var bar = el("pwBar"); bar.style.width = ((score + 1) * 20) + "%"; bar.style.background = colors[score];
        el("pwTips").innerHTML = tips.length ? tips.map(function (t) { return "<li>" + t + "</li>"; }).join("") : "<li>No major issues found — keep it unique to this site.</li>";
    }
    el("pwIn").addEventListener("input", calc);
    el("pwShow").addEventListener("change", function () { el("pwIn").type = el("pwShow").checked ? "text" : "password"; });
    calc();
})();
</script>
@endsection
