@extends('layouts.app')

@section('title', 'Pakistan Salary Income Tax Calculator 2025-26 (FBR) | Azlaan Tools')
@section('meta_description', 'Free Pakistan salary income tax calculator for FBR 2025-26 salaried slabs. Enter monthly salary to see annual tax, monthly tax, net salary and effective tax rate. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Pakistan Salary Income Tax Calculator (FBR 2025-26)</h1>
            <p class="lead text-muted">Enter your monthly salary and instantly find out your annual tax, monthly tax and net salary under the FBR 2025-26 salaried slabs — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="monthlySalary" class="form-label fw-semibold">Monthly Salary (PKR) — Gross</label>
                    <input type="number" class="form-control form-control-lg" id="monthlySalary" min="0" step="any" placeholder="e.g. 150000">
                    <div class="form-text">Enter only your basic gross monthly salary. Allowances, bonuses and tax credits are not included in this estimate.</div>
                    <div class="alert alert-warning mt-3 d-none" id="salaryMsg"></div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Annual Gross Income</div><div class="fs-4 fw-bold" id="annualGrossOut">—</div></div></div>
                        <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Annual Tax</div><div class="fs-4 fw-bold text-danger" id="annualTaxOut">—</div><div class="small text-muted" id="surchargeOut"></div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Tax</div><div class="fs-5 fw-bold" id="monthlyTaxOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Monthly Net Salary</div><div class="fs-5 fw-bold text-success" id="monthlyNetOut">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Effective Tax Rate</div><div class="fs-5 fw-bold" id="effectiveOut">—</div></div></div>
                    </div>
                    <div class="small text-muted mt-3" id="slabDetail">Details will appear here once you enter your salary.</div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">FBR Salaried Slabs 2025-26 (Finance Act 2025)</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-2">
                            <thead class="table-light"><tr><th>Annual Taxable Income (PKR)</th><th>Tax Rate</th></tr></thead>
                            <tbody>
                                <tr><td>Up to 600,000</td><td>0%</td></tr>
                                <tr><td>600,001 – 1,200,000</td><td>1% of amount above 600,000</td></tr>
                                <tr><td>1,200,001 – 2,200,000</td><td>Rs 6,000 + 11% of amount above 1,200,000</td></tr>
                                <tr><td>2,200,001 – 3,200,000</td><td>Rs 116,000 + 23% of amount above 2,200,000</td></tr>
                                <tr><td>3,200,001 – 4,100,000</td><td>Rs 346,000 + 30% of amount above 3,200,000</td></tr>
                                <tr><td>Above 4,100,000</td><td>Rs 616,000 + 35% of amount above 4,100,000</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-1">Surcharge: if your annual income is over Rs 10,000,000, a 9% surcharge applies on the tax — the calculator applies it automatically.</p>
                    <p class="small text-muted mb-0">Note: these rates are indicative — verify them with FBR notifications, as slabs can change.</p>
                </div>
            </div>

            <div class="alert alert-info">Disclaimer: this is only an estimate, based on the FBR Finance Act 2025 salaried slabs. Allowances, exemptions, tax credits, super tax, other adjustments and employer withholding are not included. For your final tax, verify with your tax consultant or FBR.</div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter your gross monthly salary in PKR.</li>
                        <li>The calculator works out your annual income (monthly x 12) and applies the correct slab.</li>
                        <li>Check your annual tax, monthly tax, monthly net salary and effective tax percentage.</li>
                        <li>If your annual income is over 1 crore, the 9% surcharge is added automatically.</li>
                        <li>You can also verify your slab yourself in the table below.</li>
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
    var input = document.getElementById('monthlySalary');
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
    function calcTax(income) {
        if (income <= 600000) return { tax: 0, label: 'Slab 1: Up to Rs 600,000 — 0% tax' };
        if (income <= 1200000) return { tax: (income - 600000) * 0.01, label: 'Slab 2: 1% of amount above Rs 600,000' };
        if (income <= 2200000) return { tax: 6000 + (income - 1200000) * 0.11, label: 'Slab 3: Rs 6,000 + 11% above Rs 1,200,000' };
        if (income <= 3200000) return { tax: 116000 + (income - 2200000) * 0.23, label: 'Slab 4: Rs 116,000 + 23% above Rs 2,200,000' };
        if (income <= 4100000) return { tax: 346000 + (income - 3200000) * 0.30, label: 'Slab 5: Rs 346,000 + 30% above Rs 3,200,000' };
        return { tax: 616000 + (income - 4100000) * 0.35, label: 'Slab 6: Rs 616,000 + 35% above Rs 4,100,000' };
    }
    function calculate() {
        var msg = document.getElementById('salaryMsg');
        var monthly = parseFloat(input.value);
        if (isNaN(monthly) || monthly < 0 || input.value === '') {
            document.getElementById('annualGrossOut').textContent = '—';
            document.getElementById('annualTaxOut').textContent = '—';
            document.getElementById('monthlyTaxOut').textContent = '—';
            document.getElementById('monthlyNetOut').textContent = '—';
            document.getElementById('effectiveOut').textContent = '—';
            document.getElementById('surchargeOut').textContent = '';
            document.getElementById('slabDetail').textContent = 'Enter a valid monthly salary.';
            if (input.value !== '' && (isNaN(monthly) || monthly < 0)) { msg.textContent = 'Please enter a valid salary amount (0 or more).'; msg.classList.remove('d-none'); } else { msg.classList.add('d-none'); }
            return;
        }
        msg.classList.add('d-none');
        var annual = monthly * 12;
        var result = calcTax(annual);
        var baseTax = result.tax;
        var surcharge = 0;
        if (annual > 10000000) { surcharge = baseTax * 0.09; }
        var totalTax = baseTax + surcharge;
        var monthlyTax = totalTax / 12;
        var netMonthly = monthly - monthlyTax;
        var effective = annual > 0 ? (totalTax / annual) * 100 : 0;
        document.getElementById('annualGrossOut').textContent = fmt(annual);
        document.getElementById('annualTaxOut').textContent = fmt(totalTax);
        document.getElementById('surchargeOut').textContent = surcharge > 0 ? 'Includes 9% surcharge: ' + fmt(surcharge) + ' (base tax ' + fmt(baseTax) + ')' : 'No surcharge (income Rs 10,000,000 or below)';
        document.getElementById('monthlyTaxOut').textContent = fmt(monthlyTax);
        document.getElementById('monthlyNetOut').textContent = fmt(netMonthly);
        document.getElementById('effectiveOut').textContent = effective.toFixed(2) + '%';
        document.getElementById('slabDetail').textContent = result.label + ' | Annual tax ' + fmt(totalTax) + ' | Monthly net ' + fmt(netMonthly);
    }
    input.addEventListener('input', calculate);
    calculate();
})();
</script>
@endsection
