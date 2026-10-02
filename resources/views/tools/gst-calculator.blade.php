@extends('layouts.app')

@section('title', 'GST Calculator Pakistan — Sales Tax 18% — Azlaan Tools')
@section('meta_description', 'Free GST / sales tax calculator for Pakistan. Add GST to a price or remove GST from a gross amount at 18 percent or any custom rate.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">GST Calculator — Pakistan Sales Tax</h1>
            <p class="lead text-muted">Whether you want to add GST to an invoice or price, or remove tax from a gross price — net, tax and gross are all calculated instantly.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="gstAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control form-control-lg" id="gstAmount" min="0" step="any" value="100000">
                        </div>
                        <div class="col-md-6">
                            <label for="gstRate" class="form-label fw-semibold">GST / Sales Tax Rate (%)</label>
                            <input type="number" class="form-control form-control-lg" id="gstRate" min="0" step="any" value="18">
                            <div class="btn-group mt-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm gst-preset" data-rate="17">17%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm gst-preset" data-rate="18">18%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm gst-preset" data-rate="5">5%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm gst-preset" data-rate="10">10%</button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mode</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="gstMode" id="gstModeAdd" value="add" checked>
                                <label class="btn btn-outline-primary" for="gstModeAdd">Add GST (amount is Net)</label>
                                <input type="radio" class="btn-check" name="gstMode" id="gstModeRemove" value="remove">
                                <label class="btn btn-outline-primary" for="gstModeRemove">Remove GST (amount is Gross)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Net Amount (without tax)</div><div class="fs-5 fw-bold" id="gstNet">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">GST / Tax Amount</div><div class="fs-5 fw-bold text-danger" id="gstTax">—</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Gross Amount (with tax)</div><div class="fs-4 fw-bold text-success" id="gstGross">—</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Note: The standard sales tax rate in Pakistan is usually 18%, but rates can change in the budget and some items/services have a different rate — so the rate is editable here, set it according to your invoice.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the amount in Rs.</li>
                        <li>Choose a rate — 18% preset is the default, you can also enter 17% or any custom rate.</li>
                        <li>Select a mode: &quot;Add GST&quot; if the amount is without tax, &quot;Remove GST&quot; if the amount includes tax.</li>
                        <li>Net, tax and gross amounts will be shown instantly.</li>
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
    function fmt(n) { return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function calc() {
        var amount = parseFloat(document.getElementById('gstAmount').value) || 0;
        var rate = parseFloat(document.getElementById('gstRate').value) || 0;
        var mode = document.querySelector('input[name="gstMode"]:checked').value;
        if (amount <= 0) {
            document.getElementById('gstNet').textContent = '—';
            document.getElementById('gstTax').textContent = '—';
            document.getElementById('gstGross').textContent = '—';
            return;
        }
        var net, tax, gross;
        if (mode === 'add') {
            net = amount;
            tax = net * rate / 100;
            gross = net + tax;
        } else {
            gross = amount;
            net = gross / (1 + rate / 100);
            tax = gross - net;
        }
        document.getElementById('gstNet').textContent = fmt(net);
        document.getElementById('gstTax').textContent = fmt(tax);
        document.getElementById('gstGross').textContent = fmt(gross);
    }
    document.getElementById('gstAmount').addEventListener('input', calc);
    document.getElementById('gstRate').addEventListener('input', calc);
    document.querySelectorAll('input[name="gstMode"]').forEach(function (el) { el.addEventListener('change', calc); });
    document.querySelectorAll('.gst-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('gstRate').value = btn.getAttribute('data-rate');
            calc();
        });
    });
    calc();
})();
</script>
@endsection
