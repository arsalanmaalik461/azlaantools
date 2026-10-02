@extends('layouts.app')

@section('title', 'Student Budget Calculator - Azlaan Tools')
@section('meta_description', 'Make a monthly budget from your pocket money — track expenses and savings. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Student Budget Calculator</h1>
            <p class="lead text-muted">Make a budget from your pocket money or monthly income — a clear record of spending and savings.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="income" class="form-label fw-semibold">Monthly income / Pocket money (Rs)</label>
                        <input type="number" class="form-control" id="income" placeholder="e.g. 15000" min="0">
                    </div>
                    <label class="form-label fw-semibold">Monthly expenses (add rows)</label>
                    <div id="expenseRows"></div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addRowBtn">+ Add Expense</button>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Budget</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your monthly income or pocket money.</li>
                <li>Enter each expense (mess, transport, books and so on) in a separate row.</li>
                <li>Press <strong>Make Budget</strong> — you get total expenses, savings and tips.</li>
            </ol>
            <p class="text-muted small">This is only a planning estimate; make real money decisions carefully.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var incomeEl = document.getElementById('income');
    var rowsWrap = document.getElementById('expenseRows');
    var addRowBtn = document.getElementById('addRowBtn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }

    function addRow(name, amount) {
        var div = document.createElement('div');
        div.className = 'row g-2 mb-2 expense-row';
        div.innerHTML =
            '<div class="col-7"><input type="text" class="form-control exp-name" placeholder="e.g. Mess / Transport" value="' + (name || '').replace(/"/g, '&quot;') + '"></div>' +
            '<div class="col-3"><input type="number" class="form-control exp-amt" placeholder="Rs" min="0" value="' + (amount || '') + '"></div>' +
            '<div class="col-2"><button type="button" class="btn btn-outline-danger w-100 del-row" title="Remove">&times;</button></div>';
        rowsWrap.appendChild(div);
        div.querySelector('.del-row').addEventListener('click', function () {
            div.remove();
        });
    }

    addRowBtn.addEventListener('click', function () { addRow('', ''); hideError(); });

    // default rows
    addRow('Mess / Food', '6000');
    addRow('Transport', '2000');
    addRow('Books / Copies', '1000');

    function advice(savingPct, topExp) {
        var tips = [];
        if (savingPct < 0) {
            tips.push('Your spending is more than your income — try to reduce your biggest expense (' + topExp + ').');
        } else if (savingPct < 10) {
            tips.push('Your savings are very low — watch small extra spending (snacks, outings).');
        } else if (savingPct < 20) {
            tips.push('Good start! Keep your savings separate so you do not spend them.');
        } else {
            tips.push('Excellent! ' + savingPct + '% savings means your budget is healthy.');
        }
        tips.push('At the start of each month, set savings aside first, then live on the rest.');
        return tips;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var income = parseFloat(incomeEl.value);
        if (!income || income <= 0) { showError('Please enter your monthly income.'); return; }
        var rows = rowsWrap.querySelectorAll('.expense-row');
        var expenses = [];
        var total = 0;
        for (var i = 0; i < rows.length; i++) {
            var name = rows[i].querySelector('.exp-name').value.trim() || ('Expense ' + (i + 1));
            var amt = parseFloat(rows[i].querySelector('.exp-amt').value) || 0;
            if (amt > 0) {
                expenses.push({ name: name, amt: amt });
                total += amt;
            }
        }
        if (!expenses.length) { showError('Enter at least one expense.'); return; }
        var saving = income - total;
        var savingPct = Math.round((saving / income) * 100);
        expenses.sort(function (a, b) { return b.amt - a.amt; });
        var topExp = expenses[0].name;

        var html = '<div class="row g-2 text-center mb-3">';
        html += '<div class="col-4"><div class="border rounded p-2 bg-light"><div class="small text-muted">Income</div><div class="fw-bold">' + fmt(income) + '</div></div></div>';
        html += '<div class="col-4"><div class="border rounded p-2 bg-light"><div class="small text-muted">Total Expenses</div><div class="fw-bold">' + fmt(total) + '</div></div></div>';
        var savClass = saving >= 0 ? 'text-success' : 'text-danger';
        html += '<div class="col-4"><div class="border rounded p-2 bg-light"><div class="small text-muted">Savings</div><div class="fw-bold ' + savClass + '">' + fmt(saving) + ' (' + savingPct + '%)</div></div></div>';
        html += '</div>';

        html += '<label class="form-label fw-semibold">Expense details</label>';
        html += '<div class="table-responsive"><table class="table table-sm table-bordered"><thead class="table-light"><tr><th>Expense</th><th class="text-end">Amount</th><th class="text-end">Share</th></tr></thead><tbody>';
        for (var j = 0; j < expenses.length; j++) {
            var pct = Math.round((expenses[j].amt / total) * 100);
            html += '<tr><td>' + expenses[j].name.replace(/</g, '&lt;') + '</td><td class="text-end">' + fmt(expenses[j].amt) + '</td><td class="text-end">' + pct + '%</td></tr>';
        }
        html += '</tbody></table></div>';

        var tips = advice(savingPct, topExp);
        html += '<div class="alert alert-info"><strong>Tip:</strong><ul class="mb-0 mt-1">';
        for (var t = 0; t < tips.length; t++) { html += '<li>' + tips[t] + '</li>'; }
        html += '</ul></div>';

        results.innerHTML = html;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
})();
</script>
@endsection
