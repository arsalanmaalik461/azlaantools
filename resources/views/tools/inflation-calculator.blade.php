@extends('layouts.app')

@section('title', 'Inflation Calculator PKR — Free Online Tool')
@section('meta_description', 'Free inflation calculator for Pakistan. See future cost of todays prices and how much your money purchasing power will lose over the years.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Inflation Calculator</h1>
            <p class="lead text-muted">Inflation eats the value of your money — see how much today's prices will cost tomorrow, and how much purchasing power your money will keep.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="infAmount" class="form-label fw-semibold">Amount Today (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="infAmount" min="0" step="any" value="100000">
                        </div>
                        <div class="col-md-4">
                            <label for="infRate" class="form-label fw-semibold">Inflation Rate (% per year)</label>
                            <input type="number" class="form-control form-control-lg" id="infRate" min="0" step="any" value="10">
                        </div>
                        <div class="col-md-4">
                            <label for="infYears" class="form-label fw-semibold">Years</label>
                            <input type="number" class="form-control form-control-lg" id="infYears" min="0" step="any" value="10">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Future Cost of Same Thing</div><div class="fs-5 fw-bold text-danger" id="infFuture">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Your Money Will Be Worth (today&#039;s value)</div><div class="fs-5 fw-bold" id="infWorth">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Purchasing Power Loss</div><div class="fs-5 fw-bold text-danger" id="infLoss">—</div></div></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="infNote">—</div>
                    <p class="small text-muted mt-3 mb-0">The inflation rate is your own assumption — Pakistan's inflation rate changes from year to year.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter today's amount in Rs — for example the price of something today or your savings.</li>
                        <li>Enter the inflation rate (%) and the number of years.</li>
                        <li>See how much the same thing will cost in the future, and how much the real value of your cash will drop.</li>
                        <li>That is why keeping savings only as cash loses value with inflation — it is important to put your money to work.</li>
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
        var amount = parseFloat(document.getElementById('infAmount').value) || 0;
        var rate = parseFloat(document.getElementById('infRate').value) || 0;
        var years = parseFloat(document.getElementById('infYears').value) || 0;
        if (amount <= 0) {
            document.getElementById('infFuture').textContent = '—';
            document.getElementById('infWorth').textContent = '—';
            document.getElementById('infLoss').textContent = '—';
            document.getElementById('infNote').textContent = 'Enter an amount to see the result.';
            return;
        }
        var factor = Math.pow(1 + rate / 100, years);
        var future = amount * factor;
        var worth = amount / factor;
        var lossPct = factor > 0 ? (1 - 1 / factor) * 100 : 0;
        document.getElementById('infFuture').textContent = fmt(future);
        document.getElementById('infWorth').textContent = fmt(worth);
        document.getElementById('infLoss').textContent = fmt(amount - worth) + ' (' + lossPct.toFixed(1) + '%)';
        document.getElementById('infNote').textContent = years + ' years later, a thing costing ' + fmt(amount) + ' today will be about ' + fmt(future) + ', and if you keep ' + fmt(amount) + ' in cash, its buying power will be worth only ' + fmt(worth) + '.';
    }
    ['infAmount', 'infRate', 'infYears'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
