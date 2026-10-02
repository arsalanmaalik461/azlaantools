@extends('layouts.app')
@section('title', 'Final Grade Calculator — Azlaan Tools')
@section('meta_description', 'Free final grade calculator. Find what score you need on your final exam to reach your target grade, or predict your final grade.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Final Grade Calculator</h1>
            <p class="lead text-muted">What do you need in the final exam to hit your target grade? Find out in seconds — plus a reverse mode to predict your grade.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <h3 class="h5">What score do I need?</h3>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label" for="current">Current Grade %</label><input type="number" class="form-control" id="current" value="80" step="any"></div>
                    <div class="col-md-4"><label class="form-label" for="target">Target Grade %</label><input type="number" class="form-control" id="target" value="90" step="any"></div>
                    <div class="col-md-4"><label class="form-label" for="weight">Final Exam Weight %</label><input type="number" class="form-control" id="weight" value="40" step="any"></div>
                </div>
                <div class="border rounded p-3 text-center mt-3" id="needBox"><div class="text-muted small">You need on the final</div><div class="fs-3 fw-bold" id="needOut">—</div><div id="needMsg" class="mt-1">—</div></div>
                <hr>
                <h3 class="h5">Reverse — predict my final grade</h3>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="expected">Expected Final Exam Score %</label><input type="number" class="form-control" id="expected" value="85" step="any"></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Your Final Grade Would Be</div><div class="fs-4 fw-bold" id="predOut">—</div></div></div>
                </div>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Enter your current grade, target grade and the final exam weight.</li><li>See the exact score you need — with a clear message if it is already secured or not possible.</li><li>Use reverse mode: type an expected exam score to predict your final grade.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function calc() {
        var cur = parseFloat(document.getElementById('current').value) || 0;
        var tgt = parseFloat(document.getElementById('target').value) || 0;
        var wgt = parseFloat(document.getElementById('weight').value) || 0;
        var exp = parseFloat(document.getElementById('expected').value) || 0;
        var box = document.getElementById('needBox');
        if (wgt <= 0 || wgt > 100) { return; }
        var w = wgt / 100;
        var needed = (tgt - (cur * (1 - w))) / w;
        var out = document.getElementById('needOut'), msg = document.getElementById('needMsg');
        box.className = 'border rounded p-3 text-center mt-3';
        if (needed <= 0) {
            out.textContent = '0% needed';
            msg.textContent = 'Great news — your target is already secured, even before the final. Keep it up!';
            box.classList.add('bg-success', 'text-white');
        } else if (needed > 100) {
            out.textContent = needed.toFixed(1) + '% needed';
            msg.textContent = 'Sorry — this target is not possible even with 100% on the final. The maximum grade you can reach is shown in reverse mode with 100 entered.';
            box.classList.add('bg-danger', 'text-white');
        } else {
            out.textContent = needed.toFixed(1) + '%';
            msg.textContent = needed >= 90 ? 'Tough but doable — time for serious revision!' : (needed >= 60 ? 'Achievable with steady preparation. You can do this!' : 'Very achievable — a solid effort will get you there.');
            box.classList.add('bg-light');
        }
        var predicted = (cur * (1 - w)) + (exp * w);
        document.getElementById('predOut').textContent = predicted.toFixed(1) + '%';
    }
    ['current','target','weight','expected'].forEach(function (id) { document.getElementById(id).addEventListener('input', calc); });
    calc();
})();
</script>
@endsection
