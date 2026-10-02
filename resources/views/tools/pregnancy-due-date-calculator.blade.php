@extends('layouts.app')

@section('title', 'Pregnancy Due Date Calculator - Weeks & Trimester | Azlaan Tools')
@section('meta_description', 'Free pregnancy due date calculator. Enter your last period date to find your due date, current week, trimester and days remaining. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Pregnancy Due Date Calculator</h1>
            <p class="lead text-muted">Find your expected due date, current week of pregnancy and trimester from the first day of your last period — free, no signup.</p>
            <div class="card shadow-sm mb-4"><div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="lmp">First Day of Last Period (LMP)</label><input type="date" class="form-control" id="lmp"></div>
                    <div class="col-md-6"><label class="form-label" for="cycle">Cycle Length (days)</label><select class="form-select" id="cycle"></select></div>
                </div>
                <button type="button" class="btn btn-primary mt-3" id="calcBtn">Calculate Due Date</button>
                <div class="alert alert-warning mt-3 d-none" id="errBox"></div>
                <div class="row g-3 mt-2 d-none" id="results">
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Estimated Due Date</div><div class="fs-4 fw-bold" id="dueOut">—</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">You Are</div><div class="fs-4 fw-bold" id="weekOut">—</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Trimester</div><div class="fs-4 fw-bold" id="triOut">—</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 bg-light text-center"><div class="text-muted small">Days Remaining</div><div class="fs-4 fw-bold" id="remOut">—</div></div></div>
                </div>
                <p class="small text-muted mt-3 mb-0">This is an estimate only — only about 5% of babies arrive exactly on the due date. Please confirm with your doctor or midwife.</p>
            </div></div>
            <div class="card shadow-sm"><div class="card-body">
                <h2>How to use</h2>
                <ol><li>Enter the first day of your last menstrual period.</li><li>Select your usual cycle length (28 days is average).</li><li>Click Calculate to see your due date, week, trimester and days left.</li></ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var cycleSel = document.getElementById('cycle');
    for (var i = 21; i <= 35; i++) { var o = document.createElement('option'); o.value = i; o.textContent = i + ' days'; if (i === 28) o.selected = true; cycleSel.appendChild(o); }
    function fmt(d) { return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
    document.getElementById('calcBtn').addEventListener('click', function () {
        var err = document.getElementById('errBox'), res = document.getElementById('results');
        var v = document.getElementById('lmp').value;
        if (!v) { err.textContent = 'Please select your last period date.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        var p = v.split('-'); var lmp = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
        var today = new Date(); today.setHours(0, 0, 0, 0);
        var diffDays = Math.floor((today - lmp) / 86400000);
        if (diffDays < 0) { err.textContent = 'Your LMP date is in the future — please check the date and try again.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        if (diffDays > 300) { err.textContent = 'That date is more than 300 days ago. If you already delivered, congratulations! Otherwise please check the date.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        err.classList.add('d-none');
        var cycle = parseInt(cycleSel.value, 10) || 28;
        var due = new Date(lmp.getTime()); due.setDate(due.getDate() + 280 + (cycle - 28));
        var weeks = Math.floor(diffDays / 7), days = diffDays % 7;
        var tri = weeks < 13 ? 'First Trimester' : (weeks < 27 ? 'Second Trimester' : 'Third Trimester');
        var remaining = Math.ceil((due - today) / 86400000);
        document.getElementById('dueOut').textContent = fmt(due);
        document.getElementById('weekOut').textContent = weeks + ' weeks, ' + days + ' days';
        document.getElementById('triOut').textContent = tri;
        document.getElementById('remOut').textContent = remaining > 0 ? remaining + ' days' : 'Due date reached!';
        res.classList.remove('d-none');
    });
})();
</script>
@endsection
