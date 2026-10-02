@extends('layouts.app')

@section('title', 'Faraid Inheritance Calculator — Free Online Tool')
@section('meta_description', 'Enter the inheritance amount and find the Quranic shares of wife, husband, sons, daughters and parents.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Faraid Inheritance Calculator</h1>
            <p class="lead small text-muted">Enter the net inheritance and the number of heirs — the calculator uses the fixed Quranic shares and the standard residuary (asaba) rules to find each heir's share.</p>
            <div class="mb-3"><label class="form-label" for="frEstate">Net estate (Rs) — after debts and funeral expenses</label><input type="number" class="form-control fr-in" id="frEstate" value="1200000" step="any"></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="frSpouse">Spouse (life partner)</label><select class="form-select fr-in" id="frSpouse"><option value="none">None / not alive</option><option value="husband">Husband (deceased was a wife)</option><option value="wife1">1 wife</option><option value="wife2">2 wives</option><option value="wife3">3 wives</option><option value="wife4">4 wives</option></select></div>
                <div class="col-md-4"><label class="form-label" for="frSons">Sons</label><input type="number" class="form-control fr-in" id="frSons" value="1" min="0" step="1"></div>
                <div class="col-md-4"><label class="form-label" for="frDaughters">Daughters</label><input type="number" class="form-control fr-in" id="frDaughters" value="1" min="0" step="1"></div>
                <div class="col-md-4"><label class="form-label" for="frFather">Father alive?</label><select class="form-select fr-in" id="frFather"><option value="0">No</option><option value="1">Yes</option></select></div>
                <div class="col-md-4"><label class="form-label" for="frMother">Mother alive?</label><select class="form-select fr-in" id="frMother"><option value="0">No</option><option value="1" selected>Yes</option></select></div>
                <div class="col-md-4"><label class="form-label" for="frBrothers">Full brothers</label><input type="number" class="form-control fr-in" id="frBrothers" value="0" min="0" step="1"></div>
                <div class="col-md-4"><label class="form-label" for="frSisters">Full sisters</label><input type="number" class="form-control fr-in" id="frSisters" value="0" min="0" step="1"></div>
            </div>
            <div class="alert alert-warning mt-3 d-none" id="frMsg"></div>
            <div class="table-responsive mt-3"><table class="table table-sm"><thead><tr><th>Heir</th><th>Share (fraction)</th><th>Percent</th><th>Amount per person</th><th>Total for group</th></tr></thead><tbody id="frRows"></tbody></table></div>
            <p class="small text-muted mb-0" id="frNote"></p>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>After deducting debts, funeral expenses and a valid will (at most one third), enter the remaining net inheritance.</li><li>Enter the number of spouse, sons, daughters, parents and full brothers/sisters.</li><li>See each heir's fraction, percent and amount in the table — the total is always 100% (a note will appear if awl or radd applies).</li></ol>
            <p class="small text-muted mb-0">Note: this tool only covers common heir combinations (husband/wife, sons, daughters, father, mother, full brothers/sisters) using the standard fixed-share and asaba rules. Grandparents, grandchildren, nephews and disputed cases are not included. Complex estates should be confirmed with a mufti/scholar before distribution — this calculation is only guidance, not a fatwa.</p>
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
    function gcd(a, b) { a = Math.abs(a); b = Math.abs(b); while (b) { var t = a % b; a = b; b = t; } return a || 1; }
    function F(n, d) { if (n === 0) return { n: 0, d: 1 }; var g = gcd(n, d); return { n: n / g, d: d / g }; }
    function fAdd(a, b) { return F(a.n * b.d + b.n * a.d, a.d * b.d); }
    function fSub(a, b) { return F(a.n * b.d - b.n * a.d, a.d * b.d); }
    function fMul(a, b) { return F(a.n * b.n, a.d * b.d); }
    function fVal(a) { return a.n / a.d; }
    function fStr(a) { return a.n === 0 ? "0" : (a.d === 1 ? String(a.n) : a.n + "/" + a.d); }
    function calc() {
        var estate = num("frEstate");
        var spouse = el("frSpouse").value;
        var wives = spouse.indexOf("wife") === 0 ? parseInt(spouse.slice(4), 10) : 0;
        var hasHusband = spouse === "husband";
        var sons = Math.max(0, Math.floor(num("frSons"))), daughters = Math.max(0, Math.floor(num("frDaughters")));
        var father = el("frFather").value === "1", mother = el("frMother").value === "1";
        var brothers = Math.max(0, Math.floor(num("frBrothers"))), sisters = Math.max(0, Math.floor(num("frSisters")));
        var msg = el("frMsg"); msg.classList.add("d-none");
        if (estate <= 0) { msg.textContent = "Please enter the net estate amount."; msg.classList.remove("d-none"); el("frRows").innerHTML = ""; return; }
        var descendants = sons + daughters > 0;
        var siblingsCount = brothers + sisters;
        var shares = []; // {label, frac (total for group), count, note}
        function push(label, frac, count, note) { if (frac.n > 0 || note) shares.push({ label: label, frac: frac, count: count, note: note || "" }); }
        // Fixed shares
        var spouseFrac = F(0, 1);
        if (hasHusband) spouseFrac = descendants ? F(1, 4) : F(1, 2);
        if (wives > 0) spouseFrac = descendants ? F(1, 8) : F(1, 4);
        var motherFrac = F(0, 1);
        if (mother) {
            if (!descendants && father && (hasHusband || wives > 0) && siblingsCount < 2) { motherFrac = fMul(F(1, 3), fSub(F(1, 1), spouseFrac)); }
            else if (descendants || siblingsCount >= 2) motherFrac = F(1, 6);
            else motherFrac = F(1, 3);
        }
        var fatherFixed = (father && descendants) ? F(1, 6) : F(0, 1);
        var daughtersFixed = F(0, 1);
        if (daughters > 0 && sons === 0) daughtersFixed = daughters === 1 ? F(1, 2) : F(2, 3);
        var blockedSiblings = descendants ? (sons > 0) : false;
        var sistersFixed = F(0, 1);
        var siblingsBlocked = sons > 0 || father;
        if (sisters > 0 && brothers === 0 && !siblingsBlocked && !descendants) sistersFixed = sisters === 1 ? F(1, 2) : F(2, 3);
        var fixedSum = F(0, 1);
        [spouseFrac, motherFrac, fatherFixed, daughtersFixed, sistersFixed].forEach(function (f) { fixedSum = fAdd(fixedSum, f); });
        // Residue
        var notes = [];
        var residue = fSub(F(1, 1), fixedSum);
        var childrenRes = F(0, 1), fatherRes = F(0, 1), siblingsRes = F(0, 1), sistersResWithDaughters = F(0, 1);
        if (fVal(residue) > 0) {
            if (sons > 0) { childrenRes = residue; }
            else if (father) { fatherRes = residue; }
            else if (brothers > 0) { siblingsRes = residue; }
            else if (sisters > 0 && daughters > 0) { sistersResWithDaughters = residue; }
            else {
                // Radd: return residue to non-spouse fixed heirs in proportion
                var eligible = [];
                if (motherFrac.n > 0) eligible.push("mother");
                if (daughtersFixed.n > 0) eligible.push("daughters");
                if (sistersFixed.n > 0) eligible.push("sisters");
                if (fatherFixed.n > 0) eligible.push("father");
                var eligSum = F(0, 1);
                eligible.forEach(function (k) { eligSum = fAdd(eligSum, k === "mother" ? motherFrac : k === "daughters" ? daughtersFixed : k === "sisters" ? sistersFixed : fatherFixed); });
                if (eligible.length && fVal(eligSum) > 0) {
                    notes.push("Radd applied: leftover returned to fixed-share heirs (spouse excluded).");
                    var extra = {};
                    eligible.forEach(function (k) { var base = k === "mother" ? motherFrac : k === "daughters" ? daughtersFixed : k === "sisters" ? sistersFixed : fatherFixed; extra[k] = fMul(residue, F(base.n * eligSum.d, base.d * eligSum.n)); });
                    if (extra.mother) motherFrac = fAdd(motherFrac, extra.mother);
                    if (extra.daughters) daughtersFixed = fAdd(daughtersFixed, extra.daughters);
                    if (extra.sisters) sistersFixed = fAdd(sistersFixed, extra.sisters);
                    if (extra.father) fatherFixed = fAdd(fatherFixed, extra.father);
                } else if (!eligible.length) { notes.push("No residuary or radd-eligible heir in this combination — the remainder is normally referred to bait-ul-mal / a scholar."); }
            }
        }
        // Children residue split 2:1
        var sonsTotal = F(0, 1), daughtersTotal = daughtersFixed;
        if (childrenRes.n > 0) {
            var units = sons * 2 + daughters;
            sonsTotal = fMul(childrenRes, F(sons * 2, units));
            daughtersTotal = fAdd(daughtersTotal, fMul(childrenRes, F(daughters, units)));
            notes.push("Children take the residue as asaba: each son gets twice a daughter.");
        }
        var fatherTotal = fAdd(fatherFixed, fatherRes);
        if (fatherRes.n > 0) notes.push("Father takes the residue as asaba" + (fatherFixed.n > 0 ? " in addition to his fixed 1/6 (daughters-only case)." : "."));
        var brothersTotal = F(0, 1), sistersTotal = sistersFixed;
        if (siblingsRes.n > 0) {
            var bu = brothers * 2 + sisters;
            brothersTotal = fMul(siblingsRes, F(brothers * 2, bu));
            sistersTotal = fAdd(sistersTotal, fMul(siblingsRes, F(sisters, bu)));
            notes.push("Full siblings take the residue as asaba: each brother gets twice a sister.");
        }
        if (sistersResWithDaughters.n > 0) { sistersTotal = fAdd(sistersTotal, sistersResWithDaughters); notes.push("Full sisters take the residue with the daughters (asaba ma-al-ghayr)."); }
        // Awl check: recompute total
        var total = F(0, 1);
        [spouseFrac, motherFrac, sonsTotal, daughtersTotal, fatherTotal, brothersTotal, sistersTotal].forEach(function (f) { total = fAdd(total, f); });
        var scaleNote = "";
        if (fVal(total) > 1.000001) {
            var over = total; scaleNote = "Awl applied: fixed shares exceeded the estate, so every share was reduced proportionally (" + fStr(over) + ").";
            function scale(f) { return F(f.n * over.d, f.d * over.n); }
            spouseFrac = scale(spouseFrac); motherFrac = scale(motherFrac); sonsTotal = scale(sonsTotal); daughtersTotal = scale(daughtersTotal); fatherTotal = scale(fatherTotal); brothersTotal = scale(brothersTotal); sistersTotal = scale(sistersTotal);
        }
        if (hasHusband) push("Husband", spouseFrac, 1);
        if (wives > 0) push(wives + (wives === 1 ? " wife" : " wives"), spouseFrac, wives);
        if (sons > 0) push(sons + (sons === 1 ? " son" : " sons"), sonsTotal, sons);
        if (daughters > 0) push(daughters + (daughters === 1 ? " daughter" : " daughters"), daughtersTotal, daughters);
        if (father) push("Father", fatherTotal, 1);
        if (mother) push("Mother", motherFrac, 1);
        if (brothers > 0) push(brothers + (brothers === 1 ? " full brother" : " full brothers"), brothersTotal, brothers, siblingsBlocked ? "Blocked by son or father in this combination." : "");
        if (sisters > 0) push(sisters + (sisters === 1 ? " full sister" : " full sisters"), sistersTotal, sisters, (siblingsBlocked && brothers === 0) ? "Blocked by son or father in this combination." : "");
        var rows = "";
        shares.forEach(function (s) { var v = fVal(s.frac); var per = s.count > 0 ? estate * v / s.count : 0; rows += "<tr><td>" + s.label + (s.note ? " <span class=\"text-muted small\">(" + s.note + ")</span>" : "") + "</td><td>" + fStr(s.frac) + "</td><td>" + (v * 100).toFixed(2) + "%</td><td>" + rs(per) + "</td><td>" + rs(estate * v) + "</td></tr>"; });
        el("frRows").innerHTML = rows || "<tr><td colspan=\"5\">No heirs entered.</td></tr>";
        el("frNote").textContent = (scaleNote ? scaleNote + " " : "") + notes.join(" ");
    }
    document.querySelectorAll(".fr-in").forEach(function (f) { f.addEventListener("input", calc); f.addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
