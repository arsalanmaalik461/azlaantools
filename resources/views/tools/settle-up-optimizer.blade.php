@extends('layouts.app')

@section('title', 'Settle-Up Optimizer - Azlaan Tools')
@section('meta_description', 'Settle all dues and get the minimum payments — with a greedy algorithm: who pays whom how much, in the fewest transactions.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Settle-Up Optimizer</h1>
            <p class="lead text-muted">Settle all dues with <strong>minimum payments</strong> — who pays whom, in the fewest transactions. Two ways: write balances directly or add each expense.</p>

            <ul class="nav nav-tabs mb-4" id="modeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tabBalances" type="button" role="tab">Write balances</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tabExpenses" type="button" role="tab">Add expenses</button>
                </li>
            </ul>

            <div id="paneBalances">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Net balance of each person</h5>
                        <p class="text-muted small">If a person <strong>should receive money</strong>, write a positive amount; if <strong>they should pay</strong>, write a negative (minus) amount. For example if Ahmed should get 1500 write <code>1500</code>, and if Ali should pay 1500 write <code>-1500</code>.</p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="balName" placeholder="Name">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control" id="balAmount" placeholder="Balance (Rs, + or -)" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-primary w-100" id="addBalBtn">Add</button>
                            </div>
                        </div>
                        <div id="balList" class="list-group mb-2"></div>
                    </div>
                </div>
            </div>

            <div id="paneExpenses" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Add an expense</h5>
                        <p class="text-muted small">Who paid, how much, and among whom to split equally — the optimizer will do the rest of the math.</p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="expDesc" placeholder="Expense (e.g. Dinner)">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" id="expAmount" placeholder="Amount Rs" min="0" step="0.01">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="expPayer" placeholder="Who paid? (name)">
                            </div>
                        </div>
                        <div class="mb-2 fw-semibold small">Split among whom? (write names separated by commas)</div>
                        <input type="text" class="form-control mb-3" id="expSplit" placeholder="e.g. Ahmed, Ali, Sana">
                        <button type="button" class="btn btn-outline-primary w-100" id="addExpBtn">Add Expense</button>
                        <div id="expList" class="list-group mt-3"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <button type="button" class="btn btn-primary w-100" id="optBtn">Optimize — See Minimum Payments</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="clearBtn">Clear All</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="mt-4 d-none">
                        <h5>Minimum payments (<span id="payCount">0</span> transactions)</h5>
                        <div class="list-group mb-3" id="payList"></div>
                        <div class="alert alert-info" id="netInfo" role="status"></div>
                        <button type="button" class="btn btn-success w-100" id="waBtn">Copy for WhatsApp</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How does it work?</h2>
                    <p class="mb-0 text-muted small">The greedy min-cash-flow algorithm matches the biggest payer with the biggest receiver, settles them, and repeats this — so the number of transactions becomes minimum. For example, the mixed expenses of 5 people are often settled in just 2-3 payments.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var balances = {};   // name -> net balance (positive = should receive)
    var expenses = [];   // {desc, amount, payer, split:[names]}

    var tabBalances = document.getElementById('tabBalances');
    var tabExpenses = document.getElementById('tabExpenses');
    var paneBalances = document.getElementById('paneBalances');
    var paneExpenses = document.getElementById('paneExpenses');

    var balName = document.getElementById('balName');
    var balAmount = document.getElementById('balAmount');
    var addBalBtn = document.getElementById('addBalBtn');
    var balList = document.getElementById('balList');

    var expDesc = document.getElementById('expDesc');
    var expAmount = document.getElementById('expAmount');
    var expPayer = document.getElementById('expPayer');
    var expSplit = document.getElementById('expSplit');
    var addExpBtn = document.getElementById('addExpBtn');
    var expList = document.getElementById('expList');

    var optBtn = document.getElementById('optBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var payCount = document.getElementById('payCount');
    var payList = document.getElementById('payList');
    var netInfo = document.getElementById('netInfo');
    var waBtn = document.getElementById('waBtn');

    var lastSummary = '';

    function fmt(n) {
        return 'Rs ' + (Math.round(n * 100) / 100).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setTab(balMode) {
        tabBalances.classList.toggle('active', balMode);
        tabExpenses.classList.toggle('active', !balMode);
        paneBalances.classList.toggle('d-none', !balMode);
        paneExpenses.classList.toggle('d-none', balMode);
    }

    tabBalances.addEventListener('click', function () { setTab(true); });
    tabExpenses.addEventListener('click', function () { setTab(false); });

    function renderBalances() {
        balList.innerHTML = '';
        var names = Object.keys(balances);
        if (names.length === 0) {
            var d = document.createElement('div');
            d.className = 'list-group-item text-muted';
            d.textContent = 'No balance added yet.';
            balList.appendChild(d);
            return;
        }
        names.forEach(function (name) {
            var v = balances[name];
            var div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            var label = v >= 0 ? 'to receive' : 'to pay';
            var cls = v >= 0 ? 'text-success' : 'text-danger';
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + esc(name) + '</strong> — <span class="' + cls + ' fw-semibold">' + fmt(Math.abs(v)) + ' ' + label + '</span>';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                delete balances[name];
                renderBalances();
            });
            div.appendChild(span);
            div.appendChild(btn);
            balList.appendChild(div);
        });
    }

    function renderExpenses() {
        expList.innerHTML = '';
        if (expenses.length === 0) {
            var d = document.createElement('div');
            d.className = 'list-group-item text-muted';
            d.textContent = 'No expense added yet.';
            expList.appendChild(d);
            return;
        }
        expenses.forEach(function (e, idx) {
            var div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
            var span = document.createElement('span');
            span.innerHTML = '<strong>' + esc(e.desc || 'Expense') + '</strong> — ' + fmt(e.amount) +
                '<br><small class="text-muted">' + esc(e.payer) + ' paid; split: ' + esc(e.split.join(', ')) + '</small>';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remove';
            btn.addEventListener('click', function () {
                expenses.splice(idx, 1);
                renderExpenses();
            });
            div.appendChild(span);
            div.appendChild(btn);
            expList.appendChild(div);
        });
    }

    addBalBtn.addEventListener('click', function () {
        var name = balName.value.trim();
        var amt = parseFloat(balAmount.value);
        if (!name) { showError('Write the name.'); return; }
        if (isNaN(amt) || amt === 0) { showError('Write the balance amount (positive = to receive, negative = to pay).'); return; }
        clearError();
        balances[name] = Math.round(((balances[name] || 0) + amt) * 100) / 100;
        balName.value = '';
        balAmount.value = '';
        balName.focus();
        renderBalances();
    });

    addExpBtn.addEventListener('click', function () {
        var desc = expDesc.value.trim();
        var amt = parseFloat(expAmount.value);
        var payer = expPayer.value.trim();
        var split = expSplit.value.split(',').map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
        if (isNaN(amt) || amt <= 0) { showError('Write the correct expense amount.'); return; }
        if (!payer) { showError('Write the name of the person who paid.'); return; }
        if (split.length === 0) { showError('Write the split names separated by commas.'); return; }
        clearError();
        expenses.push({ desc: desc, amount: amt, payer: payer, split: split });
        expDesc.value = ''; expAmount.value = ''; expPayer.value = ''; expSplit.value = '';
        expDesc.focus();
        renderExpenses();
    });

    function buildNet() {
        var net = {};
        Object.keys(balances).forEach(function (k) { net[k] = balances[k]; });
        expenses.forEach(function (e) {
            var per = e.amount / e.split.length;
            net[e.payer] = (net[e.payer] || 0) + e.amount;
            e.split.forEach(function (n) { net[n] = (net[n] || 0) - per; });
        });
        Object.keys(net).forEach(function (k) {
            net[k] = Math.round(net[k] * 100) / 100;
            if (Math.abs(net[k]) < 0.005) delete net[k];
        });
        return net;
    }

    function minCashFlow(net) {
        var debtors = [];   // {name, amt} amt > 0 means must pay
        var creditors = []; // {name, amt} amt > 0 means must receive
        Object.keys(net).forEach(function (k) {
            if (net[k] < -0.005) debtors.push({ name: k, amt: -net[k] });
            else if (net[k] > 0.005) creditors.push({ name: k, amt: net[k] });
        });
        var payments = [];
        var i = 0, j = 0;
        debtors.sort(function (a, b) { return b.amt - a.amt; });
        creditors.sort(function (a, b) { return b.amt - a.amt; });
        while (i < debtors.length && j < creditors.length) {
            var pay = Math.min(debtors[i].amt, creditors[j].amt);
            pay = Math.round(pay * 100) / 100;
            payments.push({ from: debtors[i].name, to: creditors[j].name, amount: pay });
            debtors[i].amt = Math.round((debtors[i].amt - pay) * 100) / 100;
            creditors[j].amt = Math.round((creditors[j].amt - pay) * 100) / 100;
            if (debtors[i].amt < 0.005) i++;
            if (creditors[j].amt < 0.005) j++;
        }
        return payments;
    }

    optBtn.addEventListener('click', function () {
        clearError();
        var net = buildNet();
        var names = Object.keys(net);
        if (names.length < 2) { showError('Add the accounts of at least 2 people.'); return; }
        var total = names.reduce(function (s, k) { return s + net[k]; }, 0);
        if (Math.abs(total) > 0.01) {
            showError('Balances total is not zero (' + fmt(total) + ') — payables and receivables must balance.');
            return;
        }
        var payments = minCashFlow(net);
        payCount.textContent = payments.length;
        payList.innerHTML = '';
        if (payments.length === 0) {
            var d = document.createElement('div');
            d.className = 'list-group-item text-success fw-semibold';
            d.textContent = 'All accounts are settled — no payment needed!';
            payList.appendChild(d);
        } else {
            payments.forEach(function (p, idx) {
                var div = document.createElement('div');
                div.className = 'list-group-item py-2';
                div.innerHTML = '<span class="badge bg-primary me-2">' + (idx + 1) + '</span>' +
                    '<strong>' + esc(p.from) + '</strong> pays <strong>' + esc(p.to) + '</strong>' +
                    ' <span class="fw-bold text-primary">' + fmt(p.amount) + '</span>';
                payList.appendChild(div);
            });
        }
        netInfo.textContent = names.length + ' people, all settled in ' + payments.length + ' payments total — fewer is not possible.';
        var lines = ['Settle-Up Plan (' + payments.length + ' payments):'];
        payments.forEach(function (p) { lines.push(p.from + ' -> ' + p.to + ': ' + fmt(p.amount)); });
        lines.push('- Azlaan Tools');
        lastSummary = lines.join('\n');
        results.classList.remove('d-none');
    });

    clearBtn.addEventListener('click', function () {
        balances = {};
        expenses = [];
        lastSummary = '';
        results.classList.add('d-none');
        clearError();
        renderBalances();
        renderExpenses();
    });

    waBtn.addEventListener('click', function () {
        if (!lastSummary) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastSummary).then(function () {
                waBtn.textContent = 'Copied! Paste in WhatsApp';
                setTimeout(function () { waBtn.textContent = 'Copy for WhatsApp'; }, 2500);
            }, function () {
                window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
            });
        } else {
            window.open('https://wa.me/?text=' + encodeURIComponent(lastSummary), '_blank');
        }
    });

    renderBalances();
    renderExpenses();
})();
</script>
@endsection
