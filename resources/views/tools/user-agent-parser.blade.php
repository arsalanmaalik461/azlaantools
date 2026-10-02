@extends('layouts.app')

@section('title', 'User Agent Parser — Free Online Tool')
@section('meta_description', 'Parse any user agent string into browser OS and device details')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">User Agent Parser</h1>
            <p class="lead small text-muted">Paste any user-agent string to identify the browser, version, operating system and device type — or load your own browser string with one click.</p>
            <label class="form-label" for="uaIn">User agent string</label>
            <textarea class="form-control font-monospace" id="uaIn" rows="3"></textarea>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-primary" id="uaBtn">Parse</button>
                <button type="button" class="btn btn-outline-secondary" id="uaMine">Use my browser UA</button>
            </div>
            <div class="border rounded p-3 mt-3">
                <div class="row text-center g-2">
                    <div class="col-md-3"><div class="text-muted small">Browser</div><div class="fs-5 fw-bold" id="uaBrowser">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Operating system</div><div class="fs-5 fw-bold" id="uaOs">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Device type</div><div class="fs-5 fw-bold" id="uaDevice">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Rendering engine</div><div class="fs-5 fw-bold" id="uaEngine">—</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste a user-agent string, or click Use my browser UA.</li><li>Click Parse.</li><li>Read the browser, OS, device and engine results.</li></ol>
            <p class="small text-muted mb-0">Note: Detection uses ordered pattern matching — brand tokens are checked before generic ones because most browsers include several product names in one string. A UA string can be spoofed, so treat results as a hint, not proof.</p>
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
    function find(ua, rules) { for (var i = 0; i < rules.length; i++) { var m = ua.match(rules[i][0]); if (m) return rules[i][1] + (m[1] ? " " + m[1] : ""); } return "Unknown"; }
    function parse() {
        var ua = el("uaIn").value;
        var browser = find(ua, [[/Edg\/(\d+[\d.]*)/, "Microsoft Edge"], [/OPR\/(\d+[\d.]*)/, "Opera"], [/SamsungBrowser\/(\d+[\d.]*)/, "Samsung Internet"], [/Firefox\/(\d+[\d.]*)/, "Firefox"], [/Chrome\/(\d+[\d.]*)/, "Chrome"], [/Version\/(\d+[\d.]*).*Safari/, "Safari"], [/MSIE (\d+[\d.]*)/, "Internet Explorer"], [/Trident.*rv:(\d+[\d.]*)/, "Internet Explorer"]]);
        var os = find(ua, [[/Windows NT 10/, "Windows 10 / 11"], [/Windows NT 6.3/, "Windows 8.1"], [/Windows NT 6.1/, "Windows 7"], [/Android (\d+[\d.]*)/, "Android"], [/iPhone OS (\d+_\d+)/, "iOS"], [/iPad.*OS (\d+_\d+)/, "iPadOS"], [/Mac OS X (\d+[._]\d+)/, "macOS"], [/Linux/, "Linux"]]);
        var device = /bot|crawler|spider/i.test(ua) ? "Bot / crawler" : (/iPad|Tablet/i.test(ua) ? "Tablet" : (/Mobile|iPhone|Android.*Mobile/i.test(ua) ? "Mobile" : "Desktop"));
        var engine = /AppleWebKit/.test(ua) ? "WebKit / Blink" : (/Gecko\//.test(ua) ? "Gecko" : (/Trident/.test(ua) ? "Trident" : "Unknown"));
        el("uaBrowser").textContent = browser;
        el("uaOs").textContent = os.replace(/_/g, ".");
        el("uaDevice").textContent = device;
        el("uaEngine").textContent = engine;
    }
    el("uaBtn").addEventListener("click", parse);
    el("uaMine").addEventListener("click", function () { el("uaIn").value = navigator.userAgent; parse(); });
    el("uaIn").value = navigator.userAgent; parse();
})();
</script>
@endsection
