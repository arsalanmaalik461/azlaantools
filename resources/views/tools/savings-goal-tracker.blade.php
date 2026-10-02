@extends('layouts.app')

@section('title', 'Savings Goal Tracker - Azlaan Tools')
@section('meta_description', 'Set a savings target — with progress bar and deposit/withdrawal log. Free savings goal tracker.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Savings Goal Tracker</h1>
            <p class="lead text-muted">Set a savings target — progress bar and deposit/withdrawal log. Data is saved only in your browser, nothing is uploaded.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">New saving goal</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sgName" class="form-label fw-semibold">Goal name</label>
                            <input type="text" class="form-control" id="sgName" placeholder="e.g. Eid clothes">
                        </div>
                        <div class="col-md-3">
                            <label for="sgTarget" class="form-label fw-semibold">Target (Rs)</label>
                            <input type="number" class="form-control" id="sgTarget" placeholder="0" min="1" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="sgDeadline" class="form-label fw-semibold">Deadline</label>
                            <input type="date" class="form-control" id="sgDeadline">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="sgAddGoal">Add</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="sgError" role="alert"></div>
                </div>
            </div>

            <div id="sgGoals"></div>
            <p class="text-muted" id="sgEmpty">No goals yet — make your first saving target from above.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Deposit / Withdrawal</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="sgGoalSel" class="form-label fw-semibold">Goal</label>
                            <select class="form-select" id="sgGoalSel"></select>
                        </div>
                        <div class="col-md-3">
                            <label for="sgLogType" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="sgLogType">
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sgLogAmt" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="sgLogAmt" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="sgLogBtn">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the goal name, target amount and deadline, then press <strong>Add</strong>.</li>
                <li>When you save or withdraw money, log it with <strong>Deposit / Withdrawal</strong>.</li>
                <li>The tool will tell you how much you need to save daily, weekly or monthly before the deadline.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_savings';

    var sgName = document.getElementById('sgName');
    var sgTarget = document.getElementById('sgTarget');
    var sgDeadline = document.getElementById('sgDeadline');
    var sgAddGoal = document.getElementById('sgAddGoal');
    var sgError = document.getElementById('sgError');
    var sgGoals = document.getElementById('sgGoals');
    var sgEmpty = document.getElementById('sgEmpty');
    var sgGoalSel = document.getElementById('sgGoalSel');
    var sgLogType = document.getElementById('sgLogType');
    var sgLogAmt = document.getElementById('sgLogAmt');
    var sgLogBtn = document.getElementById('sgLogBtn');

    function fmt(n) { return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 }); }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function r2(n) { return Math.round(n * 100) / 100; }
    function showError(m) { sgError.textContent = m; sgError.classList.remove('d-none'); }
    function hideError() { sgError.classList.add('d-none'); sgError.textContent = ''; }
    function load() {
        try { var r = localStorage.getItem(KEY); if (r) { var p = JSON.parse(r); if (Array.isArray(p)) return p; } } catch (e) {}
        return [];
    }
    function save(arr) { try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {} }
    function savedOf(g) {
        var s = 0;
        g.log.forEach(function (e) { s += e.type === 'deposit' ? e.amount : -e.amount; });
        return Math.max(0, r2(s));
    }
    function daysLeft(deadline) {
        var now = new Date(); now.setHours(0, 0, 0, 0);
        var dl = new Date(deadline + 'T00:00:00');
        return Math.ceil((dl - now) / 86400000);
    }

    function render() {
        var goals = load();
        sgEmpty.style.display = goals.length ? 'none' : '';
        sgGoals.innerHTML = '';

        goals.forEach(function (g) {
            var saved = savedOf(g);
            var pct = g.target > 0 ? Math.min(100, saved / g.target * 100) : 0;
            var rem = r2(Math.max(0, g.target - saved));
            var dl = daysLeft(g.deadline);
            var pace = '';
            if (dl > 0 && rem > 0) {
                pace = '<small class="text-muted">Saving needed by the deadline (' + esc(g.deadline) + '): ' +
                    '<strong>' + fmt(r2(rem / dl)) + '</strong>/day · ' + fmt(r2(rem / Math.max(1, dl / 7))) + '/week · ' + fmt(r2(rem / Math.max(1, dl / 30.4))) + '/month</small>';
            } else if (rem <= 0) {
                pace = '<span class="badge bg-success">Target complete. Well done!</span>';
            } else {
                pace = '<span class="badge bg-warning text-dark">Deadline has passed — set a new deadline.</span>';
            }

            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3';
            var logRows = g.log.slice().reverse().map(function (e) {
                return '<li class="list-group-item d-flex justify-content-between"><span><span class="badge ' + (e.type === 'deposit' ? 'bg-success' : 'bg-danger') + ' me-2">' +
                    (e.type === 'deposit' ? 'Deposit' : 'Withdrawn') + '</span><small class="text-muted">' + esc(e.date) + '</small></span>' +
                    '<span class="fw-semibold ' + (e.type === 'deposit' ? 'text-success' : 'text-danger') + '">' +
                    (e.type === 'deposit' ? '+' : '-') + fmt(e.amount) + '</span></li>';
            }).join('') || '<li class="list-group-item text-muted">No deposits or withdrawals yet.</li>';

            card.innerHTML =
                '<div class="card-body">' +
                '<div class="d-flex justify-content-between align-items-start mb-2"><div>' +
                '<h2 class="h5 mb-1">' + esc(g.name) + '</h2>' +
                '<small class="text-muted">Target: ' + fmt(g.target) + ' · Saved: ' + fmt(saved) + '</small>' +
                '</div><button type="button" class="btn btn-sm btn-outline-danger goal-del" data-id="' + g.id + '">Delete</button></div>' +
                '<div class="progress mb-2" style="height:22px;"><div class="progress-bar" role="progressbar" style="width:' + pct.toFixed(1) + '%;">' + pct.toFixed(1) + '%</div></div>' +
                '<div class="mb-2">' + pace + '</div>' +
                '<h3 class="h6">Log</h3>' +
                '<ul class="list-group list-group-flush">' + logRows + '</ul>' +
                '</div>';
            card.querySelector('.goal-del').addEventListener('click', function () {
                if (!confirm('Delete "' + g.name + '" and its full log?')) return;
                save(load().filter(function (x) { return x.id !== g.id; }));
                render();
            });
            sgGoals.appendChild(card);
        });

        sgGoalSel.innerHTML = goals.map(function (g) {
            return '<option value="' + g.id + '">' + esc(g.name) + ' (' + fmt(g.target) + ')</option>';
        }).join('');
    }

    sgAddGoal.addEventListener('click', function () {
        hideError();
        var name = sgName.value.trim();
        var target = Number(sgTarget.value);
        if (!name) { showError('Enter the goal name.'); return; }
        if (!(target > 0)) { showError('Enter a target above 0.'); return; }
        if (!sgDeadline.value) { showError('Select a deadline.'); return; }
        var goals = load();
        goals.push({ id: 'g' + Date.now().toString(36), name: name, target: r2(target), deadline: sgDeadline.value, log: [] });
        save(goals);
        sgName.value = ''; sgTarget.value = ''; sgDeadline.value = '';
        render();
    });

    sgLogBtn.addEventListener('click', function () {
        hideError();
        var amt = Number(sgLogAmt.value);
        if (!sgGoalSel.value) { showError('Make a goal first and select it.'); return; }
        if (!(amt > 0)) { showError('Enter an amount above 0.'); return; }
        var goals = load();
        var g = null;
        for (var i = 0; i < goals.length; i++) { if (goals[i].id === sgGoalSel.value) { g = goals[i]; break; } }
        if (!g) { showError('Goal not found.'); return; }
        var now = new Date();
        var ds = now.getFullYear() + '-' + ('0' + (now.getMonth() + 1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2);
        g.log.push({ type: sgLogType.value, amount: r2(amt), date: ds });
        save(goals);
        sgLogAmt.value = '';
        render();
    });

    render();
})();
</script>
@endsection
