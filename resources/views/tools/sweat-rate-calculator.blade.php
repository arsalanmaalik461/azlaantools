@extends('layouts.app')

@section('title', 'Sweat Rate Calculator — Free Online Tool')
@section('meta_description', 'Measure your sweat rate per hour from pre and post workout weight and fluid intake')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Sweat Rate Calculator</h1>
            <p class="lead small text-muted">Weigh yourself before and after training, add what you drank, and work out how much fluid you lose per hour so you can replace it properly.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="pre">Weight before workout (kg)</label><input type="number" class="form-control" id="pre" value="75" step="any"></div>                    <div class="mb-3"><label class="form-label" for="post">Weight after workout (kg)</label><input type="number" class="form-control" id="post" value="74" step="any"></div>                    <div class="mb-3"><label class="form-label" for="fluid">Fluid drank during workout (litres)</label><input type="number" class="form-control" id="fluid" value="0.5" step="any"></div>                    <div class="mb-3"><label class="form-label" for="hours">Workout duration (hours)</label><input type="number" class="form-control" id="hours" value="1" step="any"></div>                    <div class="mb-3"><label class="form-label" for="urine">Urine passed during workout (litres) — optional</label><input type="number" class="form-control" id="urine" value="0" step="any"></div>
                    <div id="result" class="border rounded p-3 mt-3">Enter your details above and the result will appear here.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol><li>Weigh yourself right before training and enter it.</li><li>Weigh yourself right after, in similar clothing, and enter it.</li><li>Enter fluids drank, any urine passed, and the duration.</li></ol>
                    <h2 class="h5">Note</h2>
                    <p class="small text-muted mb-0">Sweat loss = (pre weight - post weight) + fluid intake - urine, treating 1 kg of weight change as 1 litre of fluid — the standard sports science weight-change method. Sweat rate varies a lot with heat, humidity and intensity. Estimate only — not medical advice.</p>
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
        var pre = num("pre"), post = num("post"), fluid = num("fluid"), hrs = num("hours"), urine = num("urine");
        if ([pre, post, fluid, hrs].some(isNaN) || pre <= 0 || post <= 0 || hrs <= 0 || fluid < 0) { out("Please enter valid weights, fluid intake and duration."); return; }
        if (isNaN(urine) || urine < 0) { urine = 0; }
        var loss = (pre - post) + fluid - urine;
        if (loss < 0) { out("These figures give a negative sweat loss — please check the weights and fluid amounts."); return; }
        var rate = loss / hrs;
        out("<strong>Total sweat loss:</strong> about " + fmt(loss, 2) + " L<br><strong>Sweat rate:</strong> about " + fmt(rate, 2) + " L per hour<br><strong>Replacement guide:</strong> about " + fmt(loss * 1.25, 2) + " to " + fmt(loss * 1.5, 2) + " L after the session (125 to 150 percent of losses, taken gradually with electrolytes)");
    }
    bind(["pre", "post", "fluid", "hours", "urine"], calc); calc();
})();
</script>
@endsection
