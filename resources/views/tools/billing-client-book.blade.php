@extends('layouts.app')

@section('title', 'Billing Client Book - Azlaan Tools')
@section('meta_description', 'Keep a record of your clients — name, address, phone — select with one click when making an invoice.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Billing Client Book</h1>
            <p class="lead text-muted">An address book for your clients — when making an invoice, select the client name with one click. Data is saved only in your browser, nothing is uploaded anywhere.</p>

            <div class="row">
                <div class="col-12 col-md-5 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title" id="formTitle">Add a new client</h5>
                            <div class="mb-2">
                                <label for="clName" class="form-label fw-semibold">Name / Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="clName" placeholder="Example: Ahmed Traders">
                            </div>
                            <div class="mb-2">
                                <label for="clPhone" class="form-label fw-semibold">Phone</label>
                                <input type="tel" class="form-control" id="clPhone" placeholder="0300-0000000">
                            </div>
                            <div class="mb-2">
                                <label for="clAddress" class="form-label fw-semibold">Address</label>
                                <textarea class="form-control" id="clAddress" rows="2" placeholder="Shop address"></textarea>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="clEmail" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="clEmail" placeholder="optional">
                                </div>
                                <div class="col-6">
                                    <label for="clTax" class="form-label fw-semibold">NTN / STRN</label>
                                    <input type="text" class="form-control" id="clTax" placeholder="optional">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="clNotes" class="form-label fw-semibold">Notes</label>
                                <input type="text" class="form-control" id="clNotes" placeholder="Example: monthly billing client">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary flex-fill" id="clSave">Save Client</button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="clCancel">Cancel</button>
                            </div>
                            <div class="alert alert-danger mt-3 d-none" id="clError" role="alert"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Clients <span class="badge bg-secondary" id="clCount">0</span></h5>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-success" id="clExport">CSV</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clClearAll">Clear All</button>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">&#128269;</span>
                                <input type="text" class="form-control" id="clSearch" placeholder="Search client — name, phone or address...">
                            </div>
                            <div id="clList" class="list-group"></div>
                            <p class="text-muted small mt-2 mb-0">This same list will be used to select a client in <strong>Invoice Maker</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the client name, phone and address, then press <strong>Save Client</strong>.</li>
                <li>Find a client from the search box, change it with <strong>Edit</strong> or remove it with <strong>Delete</strong>.</li>
                <li>For backup, download a file with the <strong>CSV</strong> button.</li>
            </ol>
            <p class="small text-muted">Data is saved only in your browser, nothing is uploaded anywhere. If you clear your browser data, the record will also be deleted — keep taking CSV backups.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan_clients';
    var clName = document.getElementById('clName');
    var clPhone = document.getElementById('clPhone');
    var clAddress = document.getElementById('clAddress');
    var clEmail = document.getElementById('clEmail');
    var clTax = document.getElementById('clTax');
    var clNotes = document.getElementById('clNotes');
    var clSave = document.getElementById('clSave');
    var clCancel = document.getElementById('clCancel');
    var clError = document.getElementById('clError');
    var clList = document.getElementById('clList');
    var clSearch = document.getElementById('clSearch');
    var clCount = document.getElementById('clCount');
    var clExport = document.getElementById('clExport');
    var clClearAll = document.getElementById('clClearAll');
    var formTitle = document.getElementById('formTitle');

    var clients = [];
    var editingId = null;

    function load() {
        try {
            var raw = localStorage.getItem(KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) clients = p;
            }
        } catch (e) { clients = []; }
    }
    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(clients)); }
        catch (e) { showError('Could not save — browser storage is blocked.'); }
    }
    function uid() {
        return 'cl' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        clError.textContent = msg;
        clError.classList.remove('d-none');
    }
    function hideError() {
        clError.classList.add('d-none');
        clError.textContent = '';
    }
    function resetForm() {
        clName.value = '';
        clPhone.value = '';
        clAddress.value = '';
        clEmail.value = '';
        clTax.value = '';
        clNotes.value = '';
        editingId = null;
        clSave.textContent = 'Save Client';
        formTitle.textContent = 'Add a new client';
        clCancel.classList.add('d-none');
    }
    function getClient(id) {
        for (var i = 0; i < clients.length; i++) {
            if (clients[i].id === id) return clients[i];
        }
        return null;
    }

    function render() {
        hideError();
        var q = clSearch.value.trim().toLowerCase();
        clCount.textContent = clients.length;
        clList.innerHTML = '';
        var shown = clients.filter(function (c) {
            if (!q) return true;
            return (c.name + ' ' + (c.phone || '') + ' ' + (c.address || '')).toLowerCase().indexOf(q) !== -1;
        });
        if (!clients.length) {
            clList.innerHTML = '<div class="text-muted small p-3">No clients yet. Add your first client from the form.</div>';
            return;
        }
        if (!shown.length) {
            clList.innerHTML = '<div class="text-muted small p-3">No client found in search.</div>';
            return;
        }
        shown.forEach(function (c) {
            var item = document.createElement('div');
            item.className = 'list-group-item';
            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-start';
            var nameWrap = document.createElement('div');
            var strong = document.createElement('strong');
            strong.textContent = c.name;
            nameWrap.appendChild(strong);
            if (c.phone) {
                var ph = document.createElement('div');
                ph.className = 'small text-muted';
                ph.textContent = c.phone;
                nameWrap.appendChild(ph);
            }
            var btnWrap = document.createElement('div');
            btnWrap.className = 'd-flex gap-1';
            var editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'btn btn-sm btn-outline-primary';
            editBtn.textContent = 'Edit';
            editBtn.addEventListener('click', function () { startEdit(c.id); });
            var delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-sm btn-outline-danger';
            delBtn.textContent = 'Delete';
            delBtn.addEventListener('click', function () { deleteClient(c.id); });
            btnWrap.appendChild(editBtn);
            btnWrap.appendChild(delBtn);
            head.appendChild(nameWrap);
            head.appendChild(btnWrap);
            item.appendChild(head);
            var details = [];
            if (c.address) details.push(c.address);
            if (c.email) details.push(c.email);
            if (c.tax) details.push('NTN/STRN: ' + c.tax);
            if (c.notes) details.push('Note: ' + c.notes);
            if (details.length) {
                var d = document.createElement('div');
                d.className = 'small text-muted mt-1';
                d.textContent = details.join(' | ');
                item.appendChild(d);
            }
            clList.appendChild(item);
        });
    }

    function startEdit(id) {
        var c = getClient(id);
        if (!c) return;
        editingId = id;
        clName.value = c.name || '';
        clPhone.value = c.phone || '';
        clAddress.value = c.address || '';
        clEmail.value = c.email || '';
        clTax.value = c.tax || '';
        clNotes.value = c.notes || '';
        clSave.textContent = 'Update';
        formTitle.textContent = 'Edit client';
        clCancel.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function deleteClient(id) {
        var c = getClient(id);
        if (!c) return;
        if (!confirm('Delete ' + c.name + ' from the client book?')) return;
        clients = clients.filter(function (x) { return x.id !== id; });
        if (editingId === id) resetForm();
        save();
        render();
    }

    clSave.addEventListener('click', function () {
        hideError();
        var name = clName.value.trim();
        if (!name) { showError('The client name is required.'); clName.focus(); return; }
        var entry = {
            id: editingId || uid(),
            name: name,
            phone: clPhone.value.trim(),
            address: clAddress.value.trim(),
            email: clEmail.value.trim(),
            tax: clTax.value.trim(),
            notes: clNotes.value.trim(),
            createdAt: new Date().toISOString()
        };
        if (editingId) {
            var c = getClient(editingId);
            if (c) {
                c.name = entry.name;
                c.phone = entry.phone;
                c.address = entry.address;
                c.email = entry.email;
                c.tax = entry.tax;
                c.notes = entry.notes;
            }
        } else {
            clients.unshift(entry);
        }
        save();
        resetForm();
        render();
    });

    clCancel.addEventListener('click', function () {
        hideError();
        resetForm();
    });

    clSearch.addEventListener('input', render);

    clExport.addEventListener('click', function () {
        hideError();
        if (!clients.length) { showError('Add a client first before exporting.'); return; }
        var rows = ['Name,Phone,Address,Email,NTN_STRN,Notes'];
        clients.forEach(function (c) {
            var cols = [c.name, c.phone || '', c.address || '', c.email || '', c.tax || '', c.notes || ''];
            rows.push(cols.map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(','));
        });
        var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'client-book.csv';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });

    clClearAll.addEventListener('click', function () {
        hideError();
        if (!clients.length) return;
        if (!confirm('Delete all clients? This cannot be undone.')) return;
        clients = [];
        save();
        resetForm();
        render();
    });

    load();
    render();
})();
</script>
@endsection
