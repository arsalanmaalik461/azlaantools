@extends('layouts.app')
@section('title', 'Citation Generator — Free Online Tool')
@section('meta_description', 'Create APA MLA and Chicago citations from book and web details')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Citation Generator</h1>
            <p class="lead small text-muted">Fill in the details of a book, website or journal article and get correctly formatted APA, MLA and Chicago citations.</p>

                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="ctType">Source type</label><select id="ctType" class="form-select"><option value="book">Book</option><option value="web">Website / web page</option><option value="journal">Journal article</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="ctLast">Author last name</label><input type="text" id="ctLast" class="form-control" placeholder="e.g. Ahmed"></div>
                        <div class="col-md-4"><label class="form-label" for="ctFirst">Author first name</label><input type="text" id="ctFirst" class="form-control" placeholder="e.g. Sara"></div>
                        <div class="col-md-8"><label class="form-label" for="ctTitle">Title of the work</label><input type="text" id="ctTitle" class="form-control" placeholder="Full title"></div>
                        <div class="col-md-4"><label class="form-label" for="ctYear">Year</label><input type="text" id="ctYear" class="form-control" placeholder="e.g. 2023"></div>
                        <div class="col-md-6"><label class="form-label" for="ctExtra1">Publisher (book) / Site name (web) / Journal name</label><input type="text" id="ctExtra1" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label" for="ctExtra2">URL or DOI (optional)</label><input type="text" id="ctExtra2" class="form-control" placeholder="https://..."></div>
                    </div>
                    <button type="button" id="ctGo" class="btn btn-primary btn-lg w-100 mt-3">Generate Citations</button>
                    <div class="mt-3"><h3 class="h6">APA 7</h3><div class="border rounded p-3" id="ctApa">-</div></div>
                    <div class="mt-3"><h3 class="h6">MLA 9</h3><div class="border rounded p-3" id="ctMla">-</div></div>
                    <div class="mt-3"><h3 class="h6">Chicago 17 (author-date)</h3><div class="border rounded p-3" id="ctChi">-</div></div>
                    <button type="button" id="ctCopy" class="btn btn-outline-secondary mt-3">Copy All Three</button>

            <div id="ctMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Choose the source type and fill in author, title and year.</li>
                    <li>Add the publisher, site or journal name plus a URL or DOI if you have one.</li>
                    <li>Click generate, then copy the style your teacher or journal requires.</li>
            </ol>
            <p class="small text-muted mb-0">Citations follow the common APA 7, MLA 9 and Chicago 17 author-date patterns for a single author. For multiple authors, edited volumes or unusual sources, confirm the fine details in the official style guide.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("ctMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function copyText(v, label) {
        if (!v) { showMsg("Nothing to copy yet.", false); return; }
        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { showMsg(label || "Copied to clipboard."); }).catch(function () { showMsg("Copy failed. Select the text and copy manually.", false); });
        else showMsg("Clipboard is not available in this browser.", false);
    }

    function clean(id) { return document.getElementById(id).value.trim(); }
    document.getElementById("ctGo").addEventListener("click", function () {
        var last = clean("ctLast"), first = clean("ctFirst"), title = clean("ctTitle"), year = clean("ctYear") || "n.d.", extra = clean("ctExtra1"), url = clean("ctExtra2"), type = document.getElementById("ctType").value;
        if (!last || !title) { showMsg("Author last name and title are required.", false); return; }
        var initials = first ? first.split(/\s+/).map(function (w) { return w.charAt(0).toUpperCase() + "."; }).join(" ") : "";
        var apaAuth = last + (initials ? ", " + initials : "");
        var apa = "", mla = "", chi = "";
        if (type === "book") {
            apa = apaAuth + " (" + year + "). " + title + ". " + extra + ".";
            mla = last + ", " + first + ". " + title + ". " + extra + ", " + year + ".";
            chi = last + ", " + first + ". " + year + ". " + title + ". " + extra + ".";
        } else if (type === "journal") {
            apa = apaAuth + " (" + year + "). " + title + ". " + extra + "." + (url ? " " + url : "");
            mla = last + ", " + first + ". \"" + title + ".\" " + extra + ", " + year + "." + (url ? " " + url + "." : "");
            chi = last + ", " + first + ". " + year + ". \"" + title + ".\" " + extra + "." + (url ? " " + url + "." : "");
        } else {
            apa = apaAuth + " (" + year + "). " + title + ". " + extra + "." + (url ? " " + url : "");
            mla = last + ", " + first + ". \"" + title + ".\" " + extra + ", " + year + "." + (url ? " " + url + "." : "");
            chi = last + ", " + first + ". " + year + ". \"" + title + ".\" " + extra + "." + (url ? " " + url + "." : "");
        }
        document.getElementById("ctApa").textContent = apa;
        document.getElementById("ctMla").textContent = mla;
        document.getElementById("ctChi").textContent = chi;
        showMsg("Citations generated in APA, MLA and Chicago formats.");
    });
    document.getElementById("ctCopy").addEventListener("click", function () {
        var txt = "APA: " + document.getElementById("ctApa").textContent + "\nMLA: " + document.getElementById("ctMla").textContent + "\nChicago: " + document.getElementById("ctChi").textContent;
        if (document.getElementById("ctApa").textContent === "-") { showMsg("Generate citations first.", false); return; }
        copyText(txt, "All three citations copied.");
    });

})();
</script>
@endsection
