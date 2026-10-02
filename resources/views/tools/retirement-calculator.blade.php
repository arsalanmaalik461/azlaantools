@extends('layouts.app')

@section('title', 'Retirement Calculator PKR — Corpus Planner — Azlaan Tools')
@section('meta_description', 'Free retirement calculator for Pakistan. Current age, savings and monthly saving to estimate corpus at retirement, inflation-adjusted value and monthly income for 20 years.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Retirement Calculator</h1>
            <p class="lead text-muted">How much retirement corpus do you need? Estimate your retirement fund and the monthly income you can get from it, based on your current savings and monthly saving.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rtAge" class="form-label fw-semibold">Current Age (Years)</label>
                            <input type="number" class="form-control form-control-lg" id="rtAge" min="0" step="1" value="30">
                        </div>
                        <div class="col-md-6">
                            <label for="rtRetireAge" class="form-label fw-semibold">Retirement Age (Years)</label>
                            <input type="number" class="form-control form-control-lg" id="rtRetireAge" min="0" step="1" value="60">
                        </div>
                        <div class="col-md-6">
                            <label for="rtSavings" class="form-label fw-semibold">Current Savings (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="rtSavings" min="0" step="any" value="1000000">
                        </div>
                        <div class="col-md-6">
                            <label for="rtMonthly" class="form-label fw-semibold">Monthly Saving (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="rtMonthly" min="0" step="any" value="30000">
                        </div>
                        <div class="col-md-6">
                            <label for="rtReturn" class="form-label fw-semibold">Expected Annual Return (%)</label>
                            <input type="number" class="form-control form-control-lg" id="rtReturn" min="0" step="any" value="12">
                        </div>
                        <div class="col-md-6">
                            <label for="rtInflation" class="form-label">Inflation Rate (%) — optional</label>
                            <input type="number" class="form-control form-control-lg" id="rtInflation" min="0" step="any" value="8">
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="rtMsg"></div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Corpus at Retirement</div><div class="fs-5 fw-bold text-success" id="rtCorpus">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Value in Today&#039;s Rupees (inflation-adjusted)</div><div class="fs-5 fw-bold" id="rtReal">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Income for 20 Years After Retirement</div><div class="fs-5 fw-bold" id="rtIncome">—</div></div></div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Years to Retirement</div><div class="fs-6 fw-bold" id="rtYears">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Total You Will Save (contributions)</div><div class="fs-6 fw-bold" id="rtContrib">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">All rates are your own assumptions — this is an estimate, not financial advice or a guaranteed return. The monthly income calculation assumes the same return on the corpus after retirement.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your current age and retirement age.</li>
                        <li>Enter your savings so far and your monthly saving in Rs.</li>
                        <li>Set the expected return (%) and inflation (%) according to your own assumption.</li>
                        <li>See the retirement corpus, its real value in today&#039;s rupees, and the 20-year monthly income estimate instantly.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function calc() {
        var age = parseFloat(document.getElementById('rtAge').value) || 0;
        var retireAge = parseFloat(document.getElementById('rtRetireAge').value) || 0;
        var savings = parseFloat(document.getElementById('rtSavings').value) || 0;
        var monthly = parseFloat(document.getElementById('rtMonthly').value) || 0;
        var annual = parseFloat(document.getElementById('rtReturn').value) || 0;
        var inflation = parseFloat(document.getElementById('rtInflation').value) || 0;
        var msg = document.getElementById('rtMsg');
        var years = retireAge - age;
        if (years <= 0) {
            msg.textContent = 'Retirement age must be more than current age.';
            msg.classList.remove('d-none');
            document.getElementById('rtCorpus').textContent = '—';
            document.getElementById('rtReal').textContent = '—';
            document.getElementById('rtIncome').textContent = '—';
            document.getElementById('rtYears').textContent = '—';
            document.getElementById('rtContrib').textContent = '—';
            return;
        }
        msg.classList.add('d-none');
        var rm = annual / 100 / 12;
        var months = Math.round(years * 12);
        var fvSavings = savings * Math.pow(1 + rm, months);
        var fvMonthly = rm !== 0 ? monthly * ((Math.pow(1 + rm, months) - 1) / rm) : monthly * months;
        var corpus = fvSavings + fvMonthly;
        var real = corpus / Math.pow(1 + inflation / 100, years);
        var n2 = 20 * 12;
        var income = rm !== 0 ? corpus * rm / (1 - Math.pow(1 + rm, -n2)) : corpus / n2;
        var contrib = savings + monthly * months;
        document.getElementById('rtCorpus').textContent = fmt(corpus);
        document.getElementById('rtReal').textContent = fmt(real);
        document.getElementById('rtIncome').textContent = fmt(income) + ' / month';
        document.getElementById('rtYears').textContent = years + ' years (' + months + ' months)';
        document.getElementById('rtContrib').textContent = fmt(contrib);
    }
    ['rtAge', 'rtRetireAge', 'rtSavings', 'rtMonthly', 'rtReturn', 'rtInflation'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
