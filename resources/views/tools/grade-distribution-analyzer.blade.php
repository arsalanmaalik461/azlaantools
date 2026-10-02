@extends('layouts.app')

@section('title', 'Grade Distribution Analyzer - Azlaan Tools')
@section('meta_description', 'Paste class marks to see grade bands, average, and which questions students missed most. Free online analyzer.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Grade Distribution Analyzer</h1>
            <p class="lead text-muted">Paste class marks — see grade bands, average and analysis of every question. It shows which question students missed the most.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="marksInput" class="form-label fw-semibold">Paste marks (one student per line)</label>
                        <textarea class="form-control" id="marksInput" rows="7" placeholder="Ali, 78&#10;Sara, 92&#10;Ahmed, 65&#10;&#10;Or question-wise:&#10;Ali, 18, 15, 20&#10;Sara, 20, 19, 18"></textarea>
                        <div class="form-text">Format: <code>Name, marks</code> or <code>Name, Q1, Q2, Q3...</code> (for question-wise analysis). Numbers alone also work.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <label for="cutA" class="form-label fw-semibold">A grade %</label>
                            <input type="number" class="form-control" id="cutA" value="80" min="1" max="100">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="cutB" class="form-label fw-semibold">B grade %</label>
                            <input type="number" class="form-control" id="cutB" value="70" min="1" max="100">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="cutC" class="form-label fw-semibold">C grade %</label>
                            <input type="number" class="form-control" id="cutC" value="60" min="1" max="100">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="cutD" class="form-label fw-semibold">D grade %</label>
                            <input type="number" class="form-control" id="cutD" value="50" min="1" max="100">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Analyze</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5>Class Statistics</h5>
                        <div class="row text-center mb-4" id="statCards"></div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6>Grade Distribution</h6>
                                <canvas id="gradeChart" class="w-100 border rounded bg-white" height="260"></canvas>
                            </div>
                            <div class="col-md-6 mb-4" id="itemWrap">
                                <h6>Question-wise Average (Item Analysis)</h6>
                                <canvas id="itemChart" class="w-100 border rounded bg-white" height="260"></canvas>
                                <div class="alert alert-warning mt-2 py-2" id="weakNote"></div>
                            </div>
                        </div>
                        <h6>Student Results</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead><tr><th>#</th><th>Name</th><th>Total</th><th>%</th><th>Grade</th></tr></thead>
                                <tbody id="resultRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Paste with <code>Name, marks</code> on each line.</li>
                <li>For question-wise analysis, use the <code>Name, Q1, Q2, Q3</code> format.</li>
                <li>Set the grade cutoffs as you like, then press <strong>Analyze</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var marksInput = document.getElementById('marksInput');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function gradeOf(pct, cuts) {
        if (pct >= Math.min(100, cuts.a + 5)) { return 'A+'; }
        if (pct >= cuts.a) { return 'A'; }
        if (pct >= cuts.b) { return 'B'; }
        if (pct >= cuts.c) { return 'C'; }
        if (pct >= cuts.d) { return 'D'; }
        return 'F';
    }
    var GRADE_COLORS = { 'A+': '#198754', 'A': '#20c997', 'B': '#0dcaf0', 'C': '#ffc107', 'D': '#fd7e14', 'F': '#dc3545' };

    function drawBarChart(canvas, labels, values, colors, suffix) {
        var dpr = window.devicePixelRatio || 1;
        var W = canvas.clientWidth || 400, H = 260;
        canvas.width = W * dpr; canvas.height = H * dpr;
        canvas.style.height = H + 'px';
        var x = canvas.getContext('2d');
        x.scale(dpr, dpr);
        x.clearRect(0, 0, W, H);
        x.fillStyle = '#ffffff'; x.fillRect(0, 0, W, H);
        var maxV = Math.max.apply(null, values.concat([1]));
        var padL = 36, padB = 44, padT = 18;
        var plotW = W - padL - 10, plotH = H - padB - padT;
        var n = labels.length;
        var slot = plotW / n, bw = Math.min(46, slot * 0.62);
        x.fillStyle = '#6c757d'; x.font = '11px Arial'; x.textAlign = 'right';
        for (var g = 0; g <= 4; g++) {
            var gv = maxV * g / 4, gy = padT + plotH - (plotH * g / 4);
            x.strokeStyle = '#e9ecef'; x.beginPath(); x.moveTo(padL, gy); x.lineTo(W - 10, gy); x.stroke();
            x.fillText((Math.round(gv * 10) / 10) + '', padL - 6, gy + 4);
        }
        for (var i = 0; i < n; i++) {
            var bh = values[i] / maxV * plotH;
            var bx = padL + slot * i + (slot - bw) / 2;
            var by = padT + plotH - bh;
            x.fillStyle = colors[i] || '#0d6efd';
            x.fillRect(bx, by, bw, bh);
            x.fillStyle = '#212529'; x.textAlign = 'center';
            x.fillText(String(values[i]) + (suffix || ''), bx + bw / 2, by - 6);
            x.fillText(labels[i], bx + bw / 2, padT + plotH + 18);
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var raw = marksInput.value.trim();
        if (!raw) { showError('Please paste marks first.'); return; }
        var cuts = {
            a: parseFloat(document.getElementById('cutA').value) || 80,
            b: parseFloat(document.getElementById('cutB').value) || 70,
            c: parseFloat(document.getElementById('cutC').value) || 60,
            d: parseFloat(document.getElementById('cutD').value) || 50
        };
        if (!(cuts.a > cuts.b && cuts.b > cuts.c && cuts.c > cuts.d)) {
            showError('Cutoffs must be in descending order: A > B > C > D.');
            return;
        }
        var students = [];
        var lines = raw.split('\n');
        for (var li = 0; li < lines.length; li++) {
            var line = lines[li].trim();
            if (!line) { continue; }
            var parts = line.split(/[,;\t]+/).map(function (p) { return p.trim(); }).filter(function (p) { return p !== ''; });
            var nums = [], name = '';
            parts.forEach(function (p) {
                if (/^-?\d+(\.\d+)?$/.test(p)) { nums.push(parseFloat(p)); }
                else if (!name) { name = p; }
            });
            if (!nums.length) { continue; }
            students.push({ name: name || ('Student ' + (students.length + 1)), marks: nums });
        }
        if (students.length < 2) { showError('Marks of at least 2 students are needed.'); return; }
        var multi = students.some(function (s) { return s.marks.length > 1; });
        var nCols = Math.max.apply(null, students.map(function (s) { return s.marks.length; }));
        var totals = students.map(function (s) {
            return s.marks.reduce(function (a, b) { return a + b; }, 0);
        });
        var maxTotal = Math.max.apply(null, totals);
        if (maxTotal <= 0) { showError('Marks must be greater than zero.'); return; }

        var sum = totals.reduce(function (a, b) { return a + b; }, 0);
        var mean = sum / totals.length;
        var sorted = totals.slice().sort(function (a, b) { return a - b; });
        var median = sorted.length % 2 ? sorted[Math.floor(sorted.length / 2)] : (sorted[sorted.length / 2 - 1] + sorted[sorted.length / 2]) / 2;
        var variance = totals.reduce(function (a, t) { return a + Math.pow(t - mean, 2); }, 0) / totals.length;
        var pass = totals.filter(function (t) { return (t / maxTotal * 100) >= cuts.d; }).length;

        var bands = ['A+', 'A', 'B', 'C', 'D', 'F'];
        var counts = { 'A+': 0, 'A': 0, 'B': 0, 'C': 0, 'D': 0, 'F': 0 };
        var rowsHtml = '';
        students.forEach(function (s, idx) {
            var pct = totals[idx] / maxTotal * 100;
            var g = gradeOf(pct, cuts);
            counts[g]++;
            rowsHtml += '<tr><td>' + (idx + 1) + '</td><td>' + s.name.replace(/</g, '&lt;') + '</td><td>' +
                (Math.round(totals[idx] * 100) / 100) + '</td><td>' + (Math.round(pct * 10) / 10) + '%</td>' +
                '<td><span class="badge" style="background:' + GRADE_COLORS[g] + '">' + g + '</span></td></tr>';
        });
        document.getElementById('resultRows').innerHTML = rowsHtml;

        var cards = [
            ['Students', students.length], ['Average', (Math.round(mean * 100) / 100)],
            ['Median', (Math.round(median * 100) / 100)], ['Highest', sorted[sorted.length - 1]],
            ['Lowest', sorted[0]], ['Pass %', Math.round(pass / students.length * 100) + '%']
        ];
        document.getElementById('statCards').innerHTML = cards.map(function (c) {
            return '<div class="col-4 col-md-2 mb-2"><div class="card"><div class="card-body p-2">' +
                '<div class="fw-bold fs-5">' + c[1] + '</div><div class="text-muted small">' + c[0] + '</div></div></div></div>';
        }).join('');

        drawBarChart(document.getElementById('gradeChart'), bands,
            bands.map(function (b) { return counts[b]; }),
            bands.map(function (b) { return GRADE_COLORS[b]; }), '');

        var itemWrap = document.getElementById('itemWrap');
        if (multi && nCols > 1) {
            itemWrap.style.display = '';
            var avgs = [], labels = [];
            for (var q = 0; q < nCols; q++) {
                var col = students.map(function (s) { return s.marks[q] || 0; });
                avgs.push(Math.round(col.reduce(function (a, b) { return a + b; }, 0) / col.length * 100) / 100);
                labels.push('Q' + (q + 1));
            }
            var minAvg = Math.min.apply(null, avgs);
            var weakest = avgs.indexOf(minAvg);
            drawBarChart(document.getElementById('itemChart'), labels, avgs,
                avgs.map(function (v, i) { return i === weakest ? '#dc3545' : '#0d6efd'; }), '');
            document.getElementById('weakNote').innerHTML =
                '<strong>Weakest question: Q' + (weakest + 1) + '</strong> (average ' + minAvg + '). Teach this topic again.';
        } else {
            itemWrap.style.display = 'none';
        }
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
