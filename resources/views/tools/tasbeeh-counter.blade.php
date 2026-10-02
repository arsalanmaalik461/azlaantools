@extends('layouts.app')

@section('title', 'Tasbeeh Counter — Free Online Tool')
@section('meta_description', 'Count SubhanAllah, Alhamdulillah and Allahu Akbar on a digital tasbeeh, with presets and targets.')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3">Tasbeeh Counter</h1>
            <p class="lead small text-muted">Tap the screen to count your dhikr — with presets, targets, cycles and a lifetime total. Your progress stays saved in your browser.</p>
            <div class="text-center">
                <div class="d-flex gap-2 justify-content-center flex-wrap mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary ts-dhikr" data-d="SubhanAllah">SubhanAllah</button>
                    <button type="button" class="btn btn-outline-primary ts-dhikr" data-d="Alhamdulillah">Alhamdulillah</button>
                    <button type="button" class="btn btn-outline-primary ts-dhikr" data-d="Allahu Akbar">Allahu Akbar</button>
                    <button type="button" class="btn btn-outline-primary ts-dhikr" data-d="La ilaha illallah">La ilaha illallah</button>
                    <button type="button" class="btn btn-outline-primary ts-dhikr" data-d="Astaghfirullah">Astaghfirullah</button>
                </div>
                <div class="fs-4 fw-semibold" id="tbDhikr">SubhanAllah</div>
                <div class="display-1 fw-bold my-2" id="tbCount">0</div>
                <div class="row justify-content-center g-2 align-items-end">
                    <div class="col-md-3"><label class="form-label" for="tbTarget">Target per cycle</label><select class="form-select" id="tbTarget"><option value="33" selected>33</option><option value="99">99</option><option value="100">100</option><option value="500">500</option><option value="1000">1000</option></select></div>
                    <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted small">Cycles complete</div><div class="fw-bold" id="tbCycles">0</div></div></div>
                    <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted small">Lifetime total (saved)</div><div class="fw-bold" id="tbTotal">0</div></div></div>
                </div>
                <button type="button" class="btn btn-success btn-lg w-100 mt-3" id="tbTap" style="min-height:84px">Tap to count</button>
                <div class="d-flex gap-2 justify-content-center mt-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="tbUndo">Undo last</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="tbReset">Reset current dhikr</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol><li>Choose a dhikr and set a target (33, 99, 100, and so on).</li><li>Keep tapping the big button — when the target is complete, the cycle count goes up and the counter starts again from zero.</li><li>Progress saves by itself — close the page and open it again and you start from the same place.</li></ol>
            <p class="small text-muted mb-0">Note: A tasbeeh is for remembrance from the heart, not just for counting — recite slowly and with focus. Saved progress stays only in this browser (localStorage).</p>
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
    var state = { dhikr: "SubhanAllah", count: 0, cycles: 0, total: 0 };
    try { var saved = localStorage.getItem("tasbeehState"); if (saved) { var s = JSON.parse(saved); if (s && typeof s.total === "number") state = s; } } catch (e) {}
    function save() { try { localStorage.setItem("tasbeehState", JSON.stringify(state)); } catch (e) {} }
    function render() {
        el("tbDhikr").textContent = state.dhikr;
        el("tbCount").textContent = state.count;
        el("tbCycles").textContent = state.cycles;
        el("tbTotal").textContent = state.total.toLocaleString("en-US");
    }
    el("tbTap").addEventListener("click", function () {
        var target = parseInt(el("tbTarget").value, 10) || 33;
        state.count++; state.total++;
        if (state.count >= target) { state.count = 0; state.cycles++; if (navigator.vibrate) navigator.vibrate(60); }
        save(); render();
    });
    el("tbUndo").addEventListener("click", function () { if (state.count > 0) { state.count--; state.total = Math.max(0, state.total - 1); save(); render(); } });
    el("tbReset").addEventListener("click", function () { state.count = 0; state.cycles = 0; save(); render(); });
    document.querySelectorAll(".ts-dhikr").forEach(function (b) { b.addEventListener("click", function () { state.dhikr = b.getAttribute("data-d"); state.count = 0; state.cycles = 0; save(); render(); }); });
    render();
})();
</script>
@endsection
