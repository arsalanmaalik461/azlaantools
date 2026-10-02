@extends('layouts.app')

@section('title', 'Delivery Challan Maker - Azlaan Tools')
@section('meta_description', 'Create a delivery challan when sending goods — with items, quantity and receiver details. Free and printable.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Delivery Challan Maker</h1>
            <p class="lead text-muted">Create a delivery challan when sending goods: with items, quantity, receiver and vehicle details. Print it and give it to the driver.</p>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <h2 class="h5 mb-3">Challan Details</h2>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bizName" class="form-label fw-semibold">Sending company / shop</label>
                            <input type="text" class="form-control" id="bizName" placeholder="e.g. Azlaan Electric Store">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="challanNo" class="form-label fw-semibold">Challan No.</label>
                            <input type="text" class="form-control" id="challanNo">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="challanDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="challanDate">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="receiverName" class="form-label fw-semibold">Receiver name</label>
                            <input type="text" class="form-control" id="receiverName" placeholder="e.g. Malik Traders">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="receiverPhone" class="form-label fw-semibold">Receiver phone</label>
                            <input type="text" class="form-control" id="receiverPhone" placeholder="e.g. 0300-0000000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="receiverAddr" class="form-label fw-semibold">Delivery address</label>
                        <input type="text" class="form-control" id="receiverAddr" placeholder="Where the goods should be delivered">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicleNo" class="form-label fw-semibold">Vehicle number (optional)</label>
                            <input type="text" class="form-control" id="vehicleNo" placeholder="e.g. FSD-1234">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="driverName" class="form-label fw-semibold">Driver name (optional)</label>
                            <input type="text" class="form-control" id="driverName" placeholder="e.g. Ahmed">
                        </div>
                    </div>

                    <h2 class="h5 mb-3 mt-2">Items</h2>
                    <div id="itemsWrap"></div>
                    <button type="button" class="btn btn-outline-secondary mb-3" id="addItemBtn">+ Add Item</button>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes (optional)</label>
                        <input type="text" class="form-control" id="notes" placeholder="e.g. No cash on delivery — please check the goods first">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Create Challan</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div id="results" class="d-none">
                <div class="d-flex gap-2 mb-3 no-print">
                    <button type="button" class="btn btn-success flex-fill" id="printBtn">Print / Save as PDF</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="editBtn">Edit Again</button>
                </div>
                <div class="card shadow-sm mb-4" id="challanSheet">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h2 class="mb-1" id="sheetBiz">Delivery Challan</h2>
                            <div class="text-muted" id="sheetMeta"></div>
                        </div>
                        <div class="row mb-3 small">
                            <div class="col-6"><strong>Receiver:</strong> <span id="sheetReceiver"></span><br><strong>Phone:</strong> <span id="sheetPhone"></span></div>
                            <div class="col-6"><strong>Address:</strong> <span id="sheetAddr"></span><br><strong>Vehicle / Driver:</strong> <span id="sheetVehicle"></span></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:40px">#</th><th>Item</th><th style="width:90px">Qty</th><th style="width:90px">Unit</th><th>Remarks</th></tr>
                                </thead>
                                <tbody id="sheetItems"></tbody>
                                <tfoot>
                                    <tr><td colspan="2" class="text-end fw-bold">Total Quantity</td><td class="fw-bold" id="sheetTotalQty"></td><td colspan="2"></td></tr>
                                </tfoot>
                            </table>
                        </div>
                        <p class="small" id="sheetNotes"></p>
                        <div class="row mt-5 small">
                            <div class="col-6 text-center"><div class="border-top pt-2">Sender signature</div></div>
                            <div class="col-6 text-center"><div class="border-top pt-2">Receiver signature</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="no-print">
                <h2>How to use</h2>
                <ol>
                    <li>Enter the company, receiver and vehicle details.</li>
                    <li>Use "+ Add Item" to enter as many items, quantities and units as you need.</li>
                    <li>Press "Create Challan", then use "Print / Save as PDF" to print or save the PDF.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    #challanSheet { box-shadow: none !important; border: none !important; }
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var itemsWrap = document.getElementById('itemsWrap');
    var addItemBtn = document.getElementById('addItemBtn');
    var printBtn = document.getElementById('printBtn');
    var editBtn = document.getElementById('editBtn');
    var itemCount = 0;

    function todayISO() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }

    document.getElementById('challanDate').value = todayISO();
    document.getElementById('challanNo').value = 'DC-' + todayISO().replace(/-/g, '') + '-' + Math.floor(100 + Math.random() * 900);

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function addItem() {
        itemCount++;
        var row = document.createElement('div');
        row.className = 'row g-2 mb-2 item-row';
        row.setAttribute('data-row', itemCount);
        row.innerHTML =
            '<div class="col-12 col-md-5"><input type="text" class="form-control item-name" placeholder="Item name"></div>' +
            '<div class="col-4 col-md-2"><input type="number" class="form-control item-qty" placeholder="Qty" min="0" step="any"></div>' +
            '<div class="col-4 col-md-2"><input type="text" class="form-control item-unit" placeholder="Unit" value="pcs"></div>' +
            '<div class="col-3 col-md-2"><input type="text" class="form-control item-remark" placeholder="Remarks"></div>' +
            '<div class="col-1 col-md-1"><button type="button" class="btn btn-outline-danger btn-sm w-100 del-item" title="Delete">x</button></div>';
        itemsWrap.appendChild(row);
        row.querySelector('.del-item').addEventListener('click', function () {
            itemsWrap.removeChild(row);
        });
    }

    addItemBtn.addEventListener('click', addItem);
    addItem(); addItem();

    function val(id) { return document.getElementById(id).value.trim(); }

    goBtn.addEventListener('click', function () {
        hideError();
        var biz = val('bizName'), no = val('challanNo'), date = val('challanDate');
        var rName = val('receiverName'), rPhone = val('receiverPhone');
        if (!biz) { showError('Please enter the sending company / shop name.'); return; }
        if (!no) { showError('Please enter a challan number.'); return; }
        if (!rName) { showError('Please enter the receiver name.'); return; }

        var rows = itemsWrap.querySelectorAll('.item-row');
        var items = [];
        for (var i = 0; i < rows.length; i++) {
            var nm = rows[i].querySelector('.item-name').value.trim();
            var q = rows[i].querySelector('.item-qty').value.trim();
            var u = rows[i].querySelector('.item-unit').value.trim() || 'pcs';
            var rm = rows[i].querySelector('.item-remark').value.trim();
            if (!nm && !q) { continue; }
            if (!nm) { showError('Please enter a name for every item (row ' + (i + 1) + ').'); return; }
            if (q === '' || isNaN(parseFloat(q)) || parseFloat(q) <= 0) {
                showError('Please enter a valid quantity for item "' + nm + '".');
                return;
            }
            items.push({ name: nm, qty: parseFloat(q), unit: u, remark: rm });
        }
        if (items.length === 0) { showError('Please add at least one item.'); return; }

        document.getElementById('sheetBiz').textContent = biz + ' — Delivery Challan';
        document.getElementById('sheetMeta').textContent = 'Challan No: ' + no + '  |  Date: ' + (date || todayISO());
        document.getElementById('sheetReceiver').textContent = rName;
        document.getElementById('sheetPhone').textContent = rPhone || '-';
        document.getElementById('sheetAddr').textContent = val('receiverAddr') || '-';
        var veh = val('vehicleNo'), drv = val('driverName');
        document.getElementById('sheetVehicle').textContent = (veh || '-') + (drv ? ' / ' + drv : '');

        var tbody = document.getElementById('sheetItems');
        tbody.innerHTML = '';
        var totalQty = 0;
        for (var j = 0; j < items.length; j++) {
            totalQty += items[j].qty;
            var tr = document.createElement('tr');
            var td0 = document.createElement('td'); td0.textContent = (j + 1);
            var td1 = document.createElement('td'); td1.textContent = items[j].name;
            var td2 = document.createElement('td'); td2.textContent = items[j].qty;
            var td3 = document.createElement('td'); td3.textContent = items[j].unit;
            var td4 = document.createElement('td'); td4.textContent = items[j].remark || '-';
            tr.appendChild(td0); tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
            tbody.appendChild(tr);
        }
        document.getElementById('sheetTotalQty').textContent = totalQty;
        var notes = val('notes');
        document.getElementById('sheetNotes').textContent = notes ? 'Notes: ' + notes : '';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    });

    printBtn.addEventListener('click', function () { window.print(); });
    editBtn.addEventListener('click', function () {
        results.classList.add('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>
@endsection
