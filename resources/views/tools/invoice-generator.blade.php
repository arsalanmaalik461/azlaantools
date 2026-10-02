@extends('layouts.app')

@section('title', 'Invoice Generator - Free Bill Maker & PDF | Azlaan Tools')
@section('meta_description', 'Free invoice generator for shops and freelancers. Add items, discount and GST, then print or save a professional invoice as PDF. No signup needed.')

@section('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #invoicePreview, #invoicePreview * { visibility: visible; }
    #invoicePreview { position: absolute; top: 0; left: 0; width: 100%; box-shadow: none !important; }
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Invoice Generator</h1>
    <p class="lead text-muted">Create a professional invoice in seconds and print or save it as PDF — free, no signup.</p>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm"><div class="card-body">
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Business / Shop Name</label><input class="form-control inv-in" id="bizName" placeholder="Azlaan Electric AC Solar Center"></div>
                    <div class="col-12"><label class="form-label">Business Address</label><input class="form-control inv-in" id="bizAddr"></div>
                    <div class="col-md-6"><label class="form-label">Business Phone</label><input class="form-control inv-in" id="bizPhone"></div>
                    <div class="col-md-6"><label class="form-label">Customer Name</label><input class="form-control inv-in" id="custName"></div>
                    <div class="col-md-6"><label class="form-label">Invoice Number</label><input class="form-control inv-in" id="invNo"></div>
                    <div class="col-md-6"><label class="form-label">Date</label><input type="date" class="form-control inv-in" id="invDate"></div>
                    <div class="col-md-6"><label class="form-label">Discount %</label><input type="number" class="form-control inv-in" id="disc" value="0" min="0" max="100"></div>
                    <div class="col-md-6"><label class="form-label">GST / Tax %</label><input type="number" class="form-control inv-in" id="tax" value="0" min="0" max="100"></div>
                </div>
                <h3 class="h6 mt-4">Items</h3><div id="itemRows"></div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addItem">+ Add Item</button>
                <div class="mt-4"><button type="button" class="btn btn-success" onclick="window.print()">Print / Save PDF</button></div>
            </div></div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow" id="invoicePreview"><div class="card-body p-4">
                <div class="d-flex justify-content-between"><div><h2 class="h4 mb-0" id="pvBiz">Your Business</h2><div class="small text-muted" id="pvAddr"></div><div class="small text-muted" id="pvPhone"></div></div><div class="text-end"><div class="fs-4 fw-bold text-primary">INVOICE</div><div class="small">No: <span id="pvNo"></span></div><div class="small">Date: <span id="pvDate"></span></div></div></div>
                <hr><p class="mb-2"><strong>Bill To:</strong> <span id="pvCust">Customer</span></p>
                <table class="table table-bordered"><thead class="table-light"><tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr></thead><tbody id="pvItems"></tbody></table>
                <div class="text-end">
                    <div>Subtotal: <strong id="pvSub">Rs 0</strong></div>
                    <div>Discount: <strong id="pvDisc">Rs 0</strong></div>
                    <div>GST: <strong id="pvTax">Rs 0</strong></div>
                    <div class="fs-5 border-top pt-2 mt-2">Total: <strong id="pvTotal">Rs 0</strong></div>
                </div>
                <p class="small text-muted mt-4 mb-0">Thank you for your business!</p>
            </div></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4"><div class="card-body">
        <h2>How to use</h2>
        <ol><li>Enter your business and customer details.</li><li>Add item rows with quantity and rate — amounts calculate automatically.</li><li>Apply discount and GST if needed, then Print / Save PDF.</li></ol>
    </div></div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function esc(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
    function money(n) { return 'Rs ' + n.toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    var now = new Date();
    document.getElementById('invNo').value = 'INV-' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + '-' + String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0');
    document.getElementById('invDate').valueAsDate = now;
    function addItem() {
        var div = document.createElement('div');
        div.innerHTML = '<div class="row g-2 mb-2 item-row"><div class="col-5"><input class="form-control form-control-sm d" placeholder="Description"></div><div class="col-2"><input type="number" class="form-control form-control-sm q" value="1" min="0"></div><div class="col-3"><input type="number" class="form-control form-control-sm r" value="0" min="0"></div><div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm rm">×</button></div></div>';
        var el = div.firstChild;
        el.querySelector('.rm').addEventListener('click', function () { el.remove(); render(); });
        el.querySelectorAll('input').forEach(function (i) { i.addEventListener('input', render); });
        document.getElementById('itemRows').appendChild(el);
    }
    function render() {
        document.getElementById('pvBiz').textContent = document.getElementById('bizName').value || 'Your Business';
        document.getElementById('pvAddr').textContent = document.getElementById('bizAddr').value;
        document.getElementById('pvPhone').textContent = document.getElementById('bizPhone').value;
        document.getElementById('pvCust').textContent = document.getElementById('custName').value || 'Customer';
        document.getElementById('pvNo').textContent = document.getElementById('invNo').value;
        document.getElementById('pvDate').textContent = document.getElementById('invDate').value;
        var sub = 0, html = '';
        document.querySelectorAll('.item-row').forEach(function (r) { var d = r.querySelector('.d').value, q = parseFloat(r.querySelector('.q').value) || 0, rate = parseFloat(r.querySelector('.r').value) || 0, amt = q * rate; sub += amt; html += '<tr><td>' + esc(d) + '</td><td class="text-end">' + q + '</td><td class="text-end">' + money(rate) + '</td><td class="text-end">' + money(amt) + '</td></tr>'; });
        var discPct = parseFloat(document.getElementById('disc').value) || 0, taxPct = parseFloat(document.getElementById('tax').value) || 0;
        var discAmt = sub * discPct / 100, taxAmt = (sub - discAmt) * taxPct / 100, total = sub - discAmt + taxAmt;
        document.getElementById('pvItems').innerHTML = html;
        document.getElementById('pvSub').textContent = money(sub);
        document.getElementById('pvDisc').textContent = money(discAmt) + ' (' + discPct + '%)';
        document.getElementById('pvTax').textContent = money(taxAmt) + ' (' + taxPct + '%)';
        document.getElementById('pvTotal').textContent = money(total);
    }
    document.querySelectorAll('.inv-in').forEach(function (i) { i.addEventListener('input', render); });
    document.getElementById('addItem').addEventListener('click', addItem);
    addItem(); addItem(); render();
})();
</script>
@endsection
