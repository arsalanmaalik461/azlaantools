@extends('layouts.app')

@section('title', 'Roman Numerals Converter - Number to Roman Free | Azlaan Tools')
@section('meta_description', 'Free Roman numerals converter: convert numbers 1 to 3999 to Roman numerals and Roman numerals back to numbers with validation. No signup needed.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Roman Numerals Converter</h1>
            <p class="lead text-muted">Convert numbers to Roman numerals and back, instantly as you type — free, no signup.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="numInput" class="form-label fw-semibold">Number (1 - 3999)</label>
                            <input type="number" class="form-control form-control-lg" id="numInput" placeholder="e.g. 2026" min="1" max="3999">
                        </div>
                        <div class="col-md-6">
                            <label for="romanInput" class="form-label fw-semibold">Roman Numeral</label>
                            <input type="text" class="form-control form-control-lg text-uppercase" id="romanInput" placeholder="e.g. MMXXVI">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 d-none" id="errorBox"></div>
                    <div class="alert alert-success mt-3 mb-0" id="resultBox">Type a number or a Roman numeral to convert.</div>
                    <button type="button" class="btn btn-success btn-sm mt-3" id="copyBtn">Copy Roman Result</button>
                    <span class="text-success small d-none" id="copyMsg">Copied!</span>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Reference Table</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <tr><th>I</th><th>V</th><th>X</th><th>L</th><th>C</th><th>D</th><th>M</th></tr>
                            <tr><td>1</td><td>5</td><td>10</td><td>50</td><td>100</td><td>500</td><td>1000</td></tr>
                            <tr><th>IV</th><th>IX</th><th>XL</th><th>XC</th><th>CD</th><th>CM</th><th>Year 2026</th></tr>
                            <tr><td>4</td><td>9</td><td>40</td><td>90</td><td>400</td><td>900</td><td>MMXXVI</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Type a number between 1 and 3999 to see its Roman numeral instantly.</li>
                        <li>Or type a Roman numeral like XIV to see its number value.</li>
                        <li>Invalid entries show a friendly error so you can fix them.</li>
                        <li>Use the reference table to learn the basic symbols.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var numInput = document.getElementById('numInput');
    var romanInput = document.getElementById('romanInput');
    var errorBox = document.getElementById('errorBox');
    var resultBox = document.getElementById('resultBox');
    var table = [[1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'], [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'], [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']];
    var values = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 };
    function toRoman(n) {
        var out = '';
        table.forEach(function (pair) {
            while (n >= pair[0]) { out += pair[1]; n -= pair[0]; }
        });
        return out;
    }
    function fromRoman(s) {
        var total = 0;
        for (var i = 0; i < s.length; i++) {
            var cur = values[s[i]];
            var nxt = i + 1 < s.length ? values[s[i + 1]] : 0;
            if (cur < nxt) { total -= cur; } else { total += cur; }
        }
        return total;
    }
    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); }
    numInput.addEventListener('input', function () {
        hideError();
        if (numInput.value === '') { romanInput.value = ''; resultBox.textContent = 'Type a number or a Roman numeral to convert.'; return; }
        var n = parseInt(numInput.value, 10);
        if (isNaN(n) || n < 1 || n > 3999) { showError('Please enter a whole number between 1 and 3999.'); return; }
        var r = toRoman(n);
        romanInput.value = r;
        resultBox.textContent = n + ' in Roman numerals is ' + r;
    });
    romanInput.addEventListener('input', function () {
        hideError();
        var s = romanInput.value.trim().toUpperCase();
        romanInput.value = s;
        if (s === '') { numInput.value = ''; resultBox.textContent = 'Type a number or a Roman numeral to convert.'; return; }
        var valid = /^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/.test(s);
        if (!valid) { showError('Invalid Roman numeral. Use only I, V, X, L, C, D, M in correct order, e.g. MMXXVI.'); return; }
        var n = fromRoman(s);
        numInput.value = n;
        resultBox.textContent = s + ' in numbers is ' + n;
    });
    document.getElementById('copyBtn').addEventListener('click', async function () {
        if (!romanInput.value) { return; }
        try { await navigator.clipboard.writeText(romanInput.value); }
        catch (e) { romanInput.select(); document.execCommand('copy'); }
        var msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(function () { msg.classList.add('d-none'); }, 2000);
    });
})();
</script>
@endsection
