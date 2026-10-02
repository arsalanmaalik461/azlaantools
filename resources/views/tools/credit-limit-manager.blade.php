@extends('layouts.app')

@section('title', 'Customer Credit Limit Manager - Azlaan Tools')
@section('meta_description', 'Set a credit limit for every customer — automatic warning when the limit is crossed. With a credit usage bar.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Customer Credit Limit Manager</h1>
            <p class="lead text-muted">Set a credit <strong>limit</strong> for every customer. When their balance gets close to or goes over the limit, a <span class="badge bg-warning text-dark">warning</span> shows by itself. Easier collection, less loss.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">New customer / limit</h5>
                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label for="clName" class="form-label fw-semibold">Customer name</label>
                            <input type="text" class="form-control" id="clName" placeholder="e.g. Rashid Bhai">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="clLimit" class="form-label fw-semibold">Credit limit (Rs)</label>
                            <input type="number" class="form-control" id="clLimit" placeholder="e.g. 20000" min="1" step="0.01">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="clBalance" class="form-label fw-semibold">Current balance (Rs)</label>
                            <input type="number" class="form-control" id="clBalance" placeholder="e.g. 5000" min="0" step="0.01">
                        </div>
                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="clAdd">Add</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="row text-center g-2 mb-4">
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
                    <div class="small text-muted">Customers</div><div class="fw-bold" id="clCount">0</div>
                </div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body py-2">
                    <div class="small text-muted">Total Credit Given</div><div class="fw-bold" id="clTotBal">Rs 0</div>
                </div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm border-warning"><div class="card-body py-2">
                    <div class="small text-muted">Near Limit</div><div class="fw-bold text-warning" id="clNear">0</div>
                </div></div></div>
                <div class="col-6 col-md-3"><div class="card shadow-sm border-danger"><div class="card-body py-2">
                    <div class="small text-muted">Over Limit</div><div class="fw-bold text-danger" id="clOver">0</div>
                </div></div></div>
            </div>

            <div id="clList"></div>
            <p class="text-muted small" id="clEmpty">No customers yet. Enter the name, limit and balance above, then press Add.</p>

            <h2>How to use</h2>
            <ol>
                <li>Enter the customer name, their <strong>credit limit</strong> (the maximum credit you can give) and <strong>current balance</strong>, then press Add.</li>
                <li>If the balance goes over <strong>80%</strong> of the limit you get a <span class="badge bg-warning text-dark">Near limit</span> warning; if the limit is crossed you get a <span class="badge bg-danger">Over limit</span> warning.</li>
                <li>On each card, use <strong>+ / −</strong> to update the balance (new credit or payment) — the usage bar fills by itself.</li>
            </ol>
            <p class="text-muted small">Note: Data is saved only in your browser — it is not uploaded anywhere.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_credit_limits';
    var clName = document.getElementById('clName');
    var clLimit = document.getElementById('clLimit');
    var clBalance = document.getElementById('clBalance');
    var clAdd = document.getElementById('clAdd');
    var clList = document.getElementById('clList');
    var clEmpty = document.getElementById('clEmpty');
    var clCount = document.getElementById('clCount');
    var clTotBal = document.getElementById('clTotBal');
    var clNear = document.getElementById('clNear');
    var clOver = document.getElementById('clOver');
    var errorBox = document.getElementById('errorBox');

    var data = { customers: [] };

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.customers) data = parsed;
        }
    } catch (e) { data = { customers: [] }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* ignore */ }
    }
    function uid() {
        return 'l' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }
    function fmt(n) {
        return 'Rs ' + Number(n).toLocaleString('en-PK');
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function statusOf(c) {
        if (c.limit <= 0) return 'nolimit';
        if (c.balance >= c.limit) return 'over';
        if (c.balance >= c.limit * 0.8) return 'near';
        return 'ok';
    }

    function render() {
        hideError();
        clList.innerHTML = '';
        clEmpty.style.display = data.customers.length ? 'none' : '';
        clCount.textContent = data.customers.length;
        var tot = 0, near = 0, over = 0;
        var sorted = data.customers.slice().sort(function (a, b) {
            var pa = a.limit > 0 ? a.balance / a.limit : 0;
            var pb = b.limit > 0 ? b.balance / b.limit : 0;
            return pb - pa;
        });
        sorted.forEach(function (c) {
            tot += c.balance;
            var st = statusOf(c);
            if (st === 'near') near++;
            if (st === 'over') over++;

            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3' + (st === 'over' ? ' border-danger' : st === 'near' ? ' border-warning' : '');
            var body = document.createElement('div');
            body.className = 'card-body';

            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-2 flex-wrap gap-2';
            var title = document.createElement('div');
            title.innerHTML = '<strong>' + esc(c.name) + '</strong> ' + statusBadge(st) +
                '<div class="small text-muted">Balance ' + fmt(c.balance) + ' / Limit ' + fmt(c.limit) + '</div>';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'btn btn-sm btn-outline-danger';
            del.textContent = 'Delete';
            del.addEventListener('click', function () {
                if (!confirm('Remove ' + c.name + ' from the list?')) return;
                data.customers = data.customers.filter(function (x) { return x.id !== c.id; });
                save(); render();
            });
            head.appendChild(title);
            head.appendChild(del);

            var pct = c.limit > 0 ? Math.min(100, Math.round(c.balance / c.limit * 100)) : 0;
            var barWrap = document.createElement('div');
            barWrap.className = 'progress mb-2';
            barWrap.style.height = '18px';
            var bar = document.createElement('div');
            bar.className = 'progress-bar ' + (st === 'over' ? 'bg-danger' : st === 'near' ? 'bg-warning' : 'bg-success');
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
            bar.setAttribute('role', 'progressbar');
            bar.setAttribute('aria-valuenow', String(pct));
            bar.setAttribute('aria-valuemin', '0');
            bar.setAttribute('aria-valuemax', '100');
            barWrap.appendChild(bar);

            var adj = document.createElement('div');
            adj.className = 'input-group input-group-sm';
            adj.style.maxWidth = '420px';
            var amtIn = document.createElement('input');
            amtIn.type = 'number';
            amtIn.className = 'form-control';
            amtIn.placeholder = 'Amount';
            amtIn.min = '1';
            var plusBtn = document.createElement('button');
            plusBtn.type = 'button';
            plusBtn.className = 'btn btn-outline-danger';
            plusBtn.textContent = '+ Credit';
            plusBtn.title = 'Add new credit (balance goes up)';
            var minusBtn = document.createElement('button');
            minusBtn.type = 'button';
            minusBtn.className = 'btn btn-outline-success';
            minusBtn.textContent = '− Payment';
            minusBtn.title = 'Payment received (balance goes down)';
            var limitBtn = document.createElement('button');
            limitBtn.type = 'button';
            limitBtn.className = 'btn btn-outline-secondary';
            limitBtn.textContent = 'Change limit';
            plusBtn.addEventListener('click', function () { adjust(c, amtIn, 1); });
            minusBtn.addEventListener('click', function () { adjust(c, amtIn, -1); });
            limitBtn.addEventListener('click', function () {
                var nv = prompt('New credit limit for ' + c.name + ' (Rs):', String(c.limit));
                if (nv === null) return;
                var v = parseFloat(nv);
                if (isNaN(v) || v <= 0) { showError('Please enter a valid limit (more than 0).'); return; }
                c.limit = Math.round(v * 100) / 100;
                save(); render();
            });
            adj.appendChild(amtIn);
            adj.appendChild(plusBtn);
            adj.appendChild(minusBtn);
            adj.appendChild(limitBtn);

            body.appendChild(head);
            body.appendChild(barWrap);
            body.appendChild(adj);
            if (st === 'over') {
                var warn = document.createElement('div');
                warn.className = 'alert alert-danger mt-2 mb-0 py-2 small';
                warn.textContent = 'Warning: ' + c.name + ' is over the limit (' + fmt(c.balance - c.limit) + ' extra). Collect the old amount before giving new credit.';
                body.appendChild(warn);
            } else if (st === 'near') {
                var warn2 = document.createElement('div');
                warn2.className = 'alert alert-warning mt-2 mb-0 py-2 small';
                warn2.textContent = 'Caution: ' + pct + '% of the limit is used. Only ' + fmt(c.limit - c.balance) + ' left.';
                body.appendChild(warn2);
            }
            card.appendChild(body);
            clList.appendChild(card);
        });
        clTotBal.textContent = fmt(tot);
        clNear.textContent = near;
        clOver.textContent = over;
    }

    function statusBadge(st) {
        if (st === 'over') return '<span class="badge bg-danger">Over limit</span>';
        if (st === 'near') return '<span class="badge bg-warning text-dark">Near limit</span>';
        return '<span class="badge bg-success">Safe</span>';
    }

    function adjust(c, input, dir) {
        hideError();
        var amt = parseFloat(input.value);
        if (isNaN(amt) || amt <= 0) { showError('Please enter a valid amount first.'); return; }
        c.balance = Math.round((c.balance + dir * amt) * 100) / 100;
        if (c.balance < 0) c.balance = 0;
        save(); render();
    }

    clAdd.addEventListener('click', function () {
        hideError();
        var name = clName.value.trim();
        if (!name) { showError('Please enter the customer name.'); return; }
        var lim = parseFloat(clLimit.value);
        if (isNaN(lim) || lim <= 0) { showError('Please enter a valid credit limit (more than 0).'); return; }
        var bal = parseFloat(clBalance.value);
        if (isNaN(bal) || bal < 0) bal = 0;
        data.customers.push({
            id: uid(),
            name: name,
            limit: Math.round(lim * 100) / 100,
            balance: Math.round(bal * 100) / 100
        });
        save();
        clName.value = ''; clLimit.value = ''; clBalance.value = '';
        render();
    });

    render();
})();
</script>
@endsection
