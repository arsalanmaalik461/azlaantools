@extends('layouts.app')

@section('title', 'URL Parser — Free Online Tool')
@section('meta_description', 'Break any URL into protocol host path port and query parts')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">URL Parser</h1>
            <p class="lead small text-muted">Paste any URL to break it into protocol, host, port, path, query parameters, fragment and origin — with every query parameter listed separately.</p>
            <label class="form-label" for="upIn">URL</label>
            <input type="text" class="form-control font-monospace" id="upIn" value="https://shop.example.com:8443/products/list?page=2&sort=price#reviews" autocomplete="off">
            <div class="alert alert-danger mt-3 d-none" id="upErr"></div>
            <div class="table-responsive mt-3"><table class="table table-sm"><tbody id="upRows"></tbody></table></div>
            <h2 class="h6 mt-3">Query parameters</h2>
            <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Key</th><th>Value (decoded)</th></tr></thead><tbody id="upParams"></tbody></table></div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste or type a full URL including the protocol.</li><li>Read each part in the table — protocol, host, port, path and more.</li><li>Check the decoded query parameter list below.</li></ol>
            <p class="small text-muted mb-0">Note: A URL without an explicit port uses the default for its protocol (80 for http, 443 for https), which is why the port row may show the default.</p>
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
    function row(k, v) { return "<tr><th style=\"width:180px\">" + k + "</th><td class=\"font-monospace\">" + v + "</td></tr>"; }
    function esc(s) { var d = document.createElement("div"); d.textContent = s; return d.innerHTML; }
    function calc() {
        var err = el("upErr"); err.classList.add("d-none");
        var u;
        try { u = new URL(el("upIn").value.trim()); } catch (e) { err.textContent = "That does not parse as a valid URL — include the protocol, for example https://"; err.classList.remove("d-none"); el("upRows").innerHTML = ""; el("upParams").innerHTML = ""; return; }
        var html = row("Protocol", esc(u.protocol)) + row("Host (with port)", esc(u.host)) + row("Hostname", esc(u.hostname)) + row("Port", esc(u.port || "(default)")) + row("Path", esc(u.pathname)) + row("Query string", esc(u.search || "—")) + row("Fragment / hash", esc(u.hash || "—")) + row("Origin", esc(u.origin)) + row("Username", esc(u.username || "—"));
        el("upRows").innerHTML = html;
        var ph = "";
        u.searchParams.forEach(function (v, k) { ph += "<tr><td class=\"font-monospace\">" + esc(k) + "</td><td class=\"font-monospace\">" + esc(v) + "</td></tr>"; });
        el("upParams").innerHTML = ph || "<tr><td colspan=\"2\" class=\"text-muted\">No query parameters.</td></tr>";
    }
    el("upIn").addEventListener("input", calc); calc();
})();
</script>
@endsection
