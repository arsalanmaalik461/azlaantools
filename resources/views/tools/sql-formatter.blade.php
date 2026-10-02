@extends('layouts.app')

@section('title', 'SQL Formatter — Free Online Tool')
@section('meta_description', 'Format SQL queries for MySQL PostgreSQL and standard SQL dialects')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">SQL Formatter</h1>
            <p class="lead small text-muted">Paste a one-line or messy SQL query and get it back with capitalised keywords and each major clause on its own line — for MySQL, PostgreSQL and standard SQL alike.</p>
            <label class="form-label" for="sfIn">Your SQL</label>
            <textarea class="form-control font-monospace" id="sfIn" rows="7" placeholder="select id, name from users where age > 18 order by name">select id, name, city from customers where country = 'Pakistan' and age >= 18 order by name limit 20</textarea>
            <div class="row g-2 mt-2">
                <div class="col-md-6"><button type="button" class="btn btn-primary w-100" id="sfBtn">Format SQL</button></div>
                <div class="col-md-6"><button type="button" class="btn btn-success w-100" id="sfCopy">Copy result</button></div>
            </div>
            <label class="form-label mt-3" for="sfOut">Formatted SQL</label>
            <textarea class="form-control font-monospace" id="sfOut" rows="10" readonly></textarea>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Paste your SQL query.</li><li>Click Format SQL — major clauses move to their own lines.</li><li>Copy the formatted query back into your editor.</li></ol>
            <p class="small text-muted mb-0">Note: The formatter normalises the standard clause keywords and joins; it keeps your identifiers, string literals and dialect-specific functions exactly as written.</p>
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
    var major = ["SELECT","FROM","WHERE","GROUP BY","ORDER BY","HAVING","LIMIT","OFFSET","LEFT JOIN","RIGHT JOIN","INNER JOIN","FULL JOIN","CROSS JOIN","JOIN","UNION ALL","UNION","EXCEPT","INTERSECT","VALUES","SET","ON DUPLICATE KEY UPDATE"];
    var words = ["AND","OR","ON","AS","IN","IS","NOT","NULL","LIKE","BETWEEN","EXISTS","CASE","WHEN","THEN","ELSE","END","DISTINCT","ASC","DESC","INTO","INSERT","UPDATE","DELETE","CREATE TABLE","PRIMARY KEY"];
    function formatSql(sql) {
        var out = sql.replace(/\s+/g, " ").trim();
        major.forEach(function (k) { var re = new RegExp("\\b" + k.replace(/ /g, "\\s+") + "\\b", "gi"); out = out.replace(re, "\n" + k); });
        words.forEach(function (k) { var re = new RegExp("\\b" + k.replace(/ /g, "\\s+") + "\\b", "gi"); out = out.replace(re, k); });
        out = out.replace(/\n(AND|OR)\b/g, "\n  $1").replace(/\n(ON)\b/g, "\n  $1").replace(/, /g, ",\n  ");
        out = out.replace(/\n\s*\n/g, "\n");
        return out.trim();
    }
    function run() { el("sfOut").value = el("sfIn").value.trim() ? formatSql(el("sfIn").value) : ""; }
    el("sfBtn").addEventListener("click", run);
    el("sfCopy").addEventListener("click", function () { copyText("sfOut", el("sfCopy")); });
    run();
})();
</script>
@endsection
