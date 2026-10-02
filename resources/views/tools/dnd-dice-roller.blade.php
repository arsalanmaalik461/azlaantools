@extends('layouts.app')

@section('title', 'DnD Dice Roller - Azlaan Tools')
@section('meta_description', 'Roll d4 to d20 dice with modifiers for tabletop RPG games, free online dice roller.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">DnD Dice Roller</h1>
            <p class="lead text-muted">Roll dice from d4 to d20 for tabletop games — with a modifier.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="countSel" class="form-label fw-semibold">Dice</label>
                            <select class="form-select" id="countSel"></select>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="dieSel" class="form-label fw-semibold">Die type</label>
                            <select class="form-select" id="dieSel">
                                <option value="4">d4</option>
                                <option value="6">d6</option>
                                <option value="8">d8</option>
                                <option value="10">d10</option>
                                <option value="12">d12</option>
                                <option value="20" selected>d20</option>
                                <option value="100">d100</option>
                            </select>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="modIn" class="form-label fw-semibold">Modifier</label>
                            <input type="number" class="form-control" id="modIn" value="0" min="-20" max="20">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-primary flex-fill" id="rollBtn">Roll Dice</button>
                        <button type="button" class="btn btn-outline-secondary" id="advBtn" title="Roll 2d20, take higher">Advantage</button>
                        <button type="button" class="btn btn-outline-secondary" id="disBtn" title="Roll 2d20, take lower">Disadvantage</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <div class="display-1 fw-bold" id="totalOut">-</div>
                        <div class="text-muted mb-3" id="formulaOut"></div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-3" id="diceOut"></div>
                        <div class="alert d-none" id="critBox" role="status"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Roll history</h2>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearHist">Clear</button>
                    </div>
                    <ul class="list-group" id="histList">
                        <li class="list-group-item text-muted" id="histEmpty">No rolls yet.</li>
                    </ul>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose how many dice, which die (d4–d100), and the modifier.</li>
                <li>Click Roll Dice — you will see each die and the total.</li>
                <li>Separate buttons for Advantage/Disadvantage on d20.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var countSel = document.getElementById('countSel');
    var dieSel = document.getElementById('dieSel');
    var modIn = document.getElementById('modIn');
    var rollBtn = document.getElementById('rollBtn');
    var advBtn = document.getElementById('advBtn');
    var disBtn = document.getElementById('disBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var totalOut = document.getElementById('totalOut');
    var formulaOut = document.getElementById('formulaOut');
    var diceOut = document.getElementById('diceOut');
    var critBox = document.getElementById('critBox');
    var histList = document.getElementById('histList');
    var histEmpty = document.getElementById('histEmpty');
    var clearHist = document.getElementById('clearHist');

    for (var n = 1; n <= 20; n++) {
        var o = document.createElement('option');
        o.value = n; o.textContent = n;
        countSel.appendChild(o);
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function rollOne(sides) {
        return 1 + Math.floor(Math.random() * sides);
    }

    function addHistory(label, total) {
        if (histEmpty) { histEmpty.remove(); histEmpty = null; }
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between';
        var s1 = document.createElement('span'); s1.textContent = label;
        var s2 = document.createElement('span'); s2.className = 'fw-bold'; s2.textContent = total;
        li.appendChild(s1); li.appendChild(s2);
        histList.insertBefore(li, histList.firstChild);
        while (histList.children.length > 20) histList.removeChild(histList.lastChild);
    }

    function settleRoll(rolls, sides, mod, label) {
        var sum = rolls.reduce(function (a, b) { return a + b; }, 0);
        var total = sum + mod;
        totalOut.textContent = total;
        var modTxt = mod > 0 ? ' + ' + mod : (mod < 0 ? ' - ' + Math.abs(mod) : '');
        formulaOut.textContent = label + ': [' + rolls.join(' + ') + ']' + modTxt + ' = ' + total;

        diceOut.innerHTML = '';
        rolls.forEach(function (r) {
            var chip = document.createElement('span');
            chip.className = 'badge fs-5 p-3 ';
            if (sides === 20 && rolls.length === 1 && r === 20) {
                chip.classList.add('bg-success');
            } else if (sides === 20 && rolls.length === 1 && r === 1) {
                chip.classList.add('bg-danger');
            } else {
                chip.classList.add('bg-secondary');
            }
            chip.textContent = r;
            diceOut.appendChild(chip);
        });

        critBox.classList.add('d-none');
        if (sides === 20 && rolls.length === 1) {
            if (rolls[0] === 20) {
                critBox.className = 'alert alert-success';
                critBox.textContent = 'NATURAL 20! Critical success!';
                critBox.classList.remove('d-none');
            } else if (rolls[0] === 1) {
                critBox.className = 'alert alert-danger';
                critBox.textContent = 'Natural 1... critical fail!';
                critBox.classList.remove('d-none');
            }
        }
        results.classList.remove('d-none');
        addHistory(label + ' (' + rolls.join(',') + modTxt + ')', total);
    }

    function animateThenSettle(rolls, sides, mod, label) {
        var ticks = 0;
        var timer = setInterval(function () {
            totalOut.textContent = 1 + Math.floor(Math.random() * sides);
            ticks++;
            if (ticks >= 8) {
                clearInterval(timer);
                settleRoll(rolls, sides, mod, label);
            }
        }, 60);
        results.classList.remove('d-none');
    }

    function doRoll(count, sides, mod, label) {
        hideError();
        if (!count || count < 1 || count > 20) { showError('Keep the number of dice between 1 and 20.'); return; }
        var rolls = [];
        for (var i = 0; i < count; i++) rolls.push(rollOne(sides));
        animateThenSettle(rolls, sides, mod, label);
    }

    rollBtn.addEventListener('click', function () {
        var count = parseInt(countSel.value, 10);
        var sides = parseInt(dieSel.value, 10);
        var mod = parseInt(modIn.value, 10) || 0;
        doRoll(count, sides, mod, count + 'd' + sides);
    });

    advBtn.addEventListener('click', function () {
        var r1 = rollOne(20), r2 = rollOne(20);
        var kept = Math.max(r1, r2);
        var mod = parseInt(modIn.value, 10) || 0;
        animateThenSettle([kept], 20, mod, 'Advantage [' + r1 + ', ' + r2 + ']');
    });

    disBtn.addEventListener('click', function () {
        var r1 = rollOne(20), r2 = rollOne(20);
        var kept = Math.min(r1, r2);
        var mod = parseInt(modIn.value, 10) || 0;
        animateThenSettle([kept], 20, mod, 'Disadvantage [' + r1 + ', ' + r2 + ']');
    });

    clearHist.addEventListener('click', function () {
        histList.innerHTML = '<li class="list-group-item text-muted" id="histEmpty">No rolls yet.</li>';
        histEmpty = document.getElementById('histEmpty');
    });
})();
</script>
@endsection
