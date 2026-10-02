@extends('layouts.app')

@section('title', 'Semantic Version Checker — Free Online Tool')
@section('meta_description', 'Compare semantic versions and check version range compatibility')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Semantic Version Checker</h1>
            <p class="lead small text-muted">Validate and compare semantic versions (major.minor.patch with pre-release rules), and test whether a version satisfies a range like ^1.2.0, ~2.0.0 or >=1.0.0 <2.0.0.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="svA">Version A</label><input type="text" class="form-control font-monospace" id="svA" value="1.2.3"></div>
                <div class="col-md-6"><label class="form-label" for="svB">Version B</label><input type="text" class="form-control font-monospace" id="svB" value="1.3.0-beta.1"></div>
            </div>
            <p class="mt-2 mb-0"><strong>Comparison:</strong> <span id="svCmp">—</span></p>
            <hr>
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label" for="svVer">Version to test</label><input type="text" class="form-control font-monospace" id="svVer" value="1.4.2"></div>
                <div class="col-md-4"><label class="form-label" for="svRange">Range</label><input type="text" class="form-control font-monospace" id="svRange" value="^1.2.0"></div>
                <div class="col-md-3"><div class="border rounded p-2 text-center"><div class="text-muted small">Satisfies range?</div><div class="fw-bold" id="svSat">—</div></div></div>
            </div>
            <div class="alert alert-danger mt-3 d-none" id="svErr"></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Enter two versions to see which is greater, following official semver precedence (a pre-release is lower than the release).</li><li>Enter a version and a range to test compatibility.</li><li>Ranges support exact, ^, ~, comparison operators, x wildcards, space-separated AND and double-pipe OR.</li></ol>
            <p class="small text-muted mb-0">Note: Build metadata after a plus sign is ignored in precedence, exactly as the semver specification requires. Range support covers the common npm-style operators listed above.</p>
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
    function parseV(s) {
        var m = String(s).trim().match(/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*)?$/);
        if (!m) return null;
        return { major: +m[1], minor: +m[2], patch: +m[3], pre: m[4] ? m[4].split(".") : [] };
    }
    function cmp(a, b) {
        var keys = ["major", "minor", "patch"], i;
        for (i = 0; i < 3; i++) { if (a[keys[i]] !== b[keys[i]]) return a[keys[i]] < b[keys[i]] ? -1 : 1; }
        if (!a.pre.length && !b.pre.length) return 0;
        if (!a.pre.length) return 1; if (!b.pre.length) return -1;
        for (i = 0; i < Math.max(a.pre.length, b.pre.length); i++) {
            if (a.pre[i] === undefined) return -1; if (b.pre[i] === undefined) return 1;
            var an = /^\d+$/.test(a.pre[i]), bn = /^\d+$/.test(b.pre[i]);
            if (an && bn) { if (+a.pre[i] !== +b.pre[i]) return +a.pre[i] < +b.pre[i] ? -1 : 1; }
            else if (an) return -1; else if (bn) return 1;
            else if (a.pre[i] !== b.pre[i]) return a.pre[i] < b.pre[i] ? -1 : 1;
        }
        return 0;
    }
    function parsePartial(s) {
        var parts = s.split("."); while (parts.length < 3) parts.push("x");
        return { major: parts[0], minor: parts[1], patch: parts[2] };
    }
    function toV(p, fill) { function n(x) { return (x === "x" || x === "*" || x === undefined) ? fill : parseInt(x, 10); } return { major: n(p.major), minor: n(p.minor), patch: n(p.patch), pre: [] }; }
    function satisfiesOne(v, token) {
        var m = token.match(/^(>=|<=|>|<|=|\^|~)?(.+)$/); if (!m) return false;
        var op = m[1] || "=", p = parsePartial(m[2]);
        if (/x|\*/.test(m[2]) && op === "=") {
            if (p.major === "x" || p.major === "*") return true;
            var lo = toV(p, 0), hiP = { major: p.major, minor: p.minor, patch: p.patch };
            var hi; if (p.minor === "x" || p.minor === "*") { hi = { major: +p.major + 1, minor: 0, patch: 0, pre: [] }; } else { hi = { major: +p.major, minor: +p.minor + 1, patch: 0, pre: [] }; }
            return cmp(v, lo) >= 0 && cmp(v, hi) < 0;
        }
        var target = parseV(m[2]) || toV(p, 0), c = cmp(v, target);
        if (op === "=") return c === 0;
        if (op === ">") return c > 0; if (op === ">=") return c >= 0; if (op === "<") return c < 0; if (op === "<=") return c <= 0;
        if (op === "^") { var hiC; if (target.major > 0) hiC = { major: target.major + 1, minor: 0, patch: 0, pre: [] }; else if (target.minor > 0) hiC = { major: 0, minor: target.minor + 1, patch: 0, pre: [] }; else hiC = { major: 0, minor: 0, patch: target.patch + 1, pre: [] }; return c >= 0 && cmp(v, hiC) < 0; }
        if (op === "~") { var hiT = { major: target.major, minor: target.minor + 1, patch: 0, pre: [] }; return c >= 0 && cmp(v, hiT) < 0; }
        return false;
    }
    function satisfies(v, range) {
        return range.split("||").some(function (group) { var toks = group.trim().split(/\s+/).filter(Boolean); return toks.every(function (t) { return satisfiesOne(v, t); }); });
    }
    function calc() {
        var err = el("svErr"); err.classList.add("d-none");
        var a = parseV(el("svA").value), b = parseV(el("svB").value);
        if (!a || !b) { el("svCmp").textContent = "One of the versions is not valid semver."; }
        else { var c = cmp(a, b); el("svCmp").textContent = c === 0 ? "A and B are equal in precedence." : (c < 0 ? "A is lower than B (B is newer)." : "A is higher than B (A is newer)."); }
        var v = parseV(el("svVer").value);
        if (!v) { el("svSat").textContent = "Invalid version"; return; }
        try { el("svSat").textContent = satisfies(v, el("svRange").value) ? "Yes ✓" : "No ✗"; }
        catch (e) { err.textContent = "Could not parse the range."; err.classList.remove("d-none"); }
    }
    ["svA","svB","svVer","svRange"].forEach(function (id) { el(id).addEventListener("input", calc); });
    calc();
})();
</script>
@endsection
