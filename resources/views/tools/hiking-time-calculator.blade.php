@extends('layouts.app')

@section('title', 'Hiking Time Calculator — Free Online Tool')
@section('meta_description', 'Estimate hiking time from distance and total ascent using the Naismith rule')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Hiking Time Calculator</h1>
            <p class="lead small text-muted">Naismith rule has guided walkers for over a century: allow 1 hour per 5 km, plus 1 hour per 600 metres of ascent.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="dist">Distance (km)</label><input type="number" class="form-control" id="dist" value="12" step="any"></div>                    <div class="mb-3"><label class="form-label" for="ascent">Total ascent (m)</label><input type="number" class="form-control" id="ascent" value="600" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Enter the total route distance in kilometres.</li><li>Enter the total uphill climbing (ascent) in metres.</li><li>Your estimated walking time appears instantly.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Uses the classic Naismith rule at 5 km/h plus 10 minutes per 100 m of ascent. It assumes a reasonably fit walker on decent paths — add time for rough terrain, heavy packs, heat, breaks and descents on steep ground. Estimate only — not medical advice.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    function num(id) { var v = parseFloat(document.getElementById(id).value); return isFinite(v) ? v : NaN; }
    function fmt(n, d) { return Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = document.getElementById(id); el.addEventListener("input", fn); el.addEventListener("change", fn); }); }
    function out(html) { document.getElementById("result").innerHTML = html; }
    function calc() {
        var d = num("dist"), a = num("ascent");
        if (isNaN(d) || isNaN(a) || d <= 0 || a < 0) { out("Please enter a valid distance and ascent."); return; }
        var hrs = d / 5 + a / 600;
        var whole = Math.floor(hrs), mins = Math.round((hrs - whole) * 60);
        if (mins === 60) { whole += 1; mins = 0; }
        out("<strong>Estimated hiking time:</strong> about " + whole + " h " + mins + " min (" + fmt(hrs, 1) + " hours, breaks not included)");
    }
    bind(["dist", "ascent"], calc); calc();
})();
</script>
@endsection
