@extends('layouts.app')

@section('title', 'Password Generator - Azlaan Tools')
@section('meta_description', 'Free strong password generator. Create secure random passwords with letters, numbers and symbols using your browser cryptography. Nothing is sent to a server.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Password Generator</h1>
            <p class="lead text-muted">Generate strong, random passwords instantly. Everything runs in your browser, so your passwords are never sent anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="lengthRange" class="form-label fw-semibold">Password Length: <span id="lengthVal">16</span></label>
                        <input type="range" class="form-range" id="lengthRange" min="8" max="64" value="16">
                        <div class="d-flex justify-content-between text-muted small"><span>8</span><span>64</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input opt" type="checkbox" id="optLower" checked><label class="form-check-label" for="optLower">Lowercase (a-z)</label></div></div>
                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input opt" type="checkbox" id="optUpper" checked><label class="form-check-label" for="optUpper">Uppercase (A-Z)</label></div></div>
                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input opt" type="checkbox" id="optNumbers" checked><label class="form-check-label" for="optNumbers">Numbers (0-9)</label></div></div>
                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input opt" type="checkbox" id="optSymbols" checked><label class="form-check-label" for="optSymbols">Symbols</label></div></div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="generateBtn">Generate 4 Passwords</button>
                    <div class="alert alert-danger mt-3 d-none" id="pwError" role="alert"></div>

                    <div id="pwResults" class="d-none mt-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold"><span>Strength</span><span id="strengthLabel">-</span></div>
                            <div class="progress" role="progressbar" style="height: 10px;">
                                <div class="progress-bar" id="strengthBar" style="width: 0%;"></div>
                            </div>
                        </div>
                        <div id="pwList" class="list-group"></div>
                        <div id="copyMsg" class="text-success small d-none mt-2">Password copied!</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Move the slider to choose a password length between 8 and 64 characters.</li>
                <li>Tick the character types you want: lowercase, uppercase, numbers and symbols.</li>
                <li>Click <strong>Generate 4 Passwords</strong> to create four random passwords at once.</li>
                <li>Check the strength indicator, then click the copy button next to the password you like.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var range = document.getElementById('lengthRange');
    range.addEventListener('input', function () { document.getElementById('lengthVal').textContent = range.value; });

    function charset() {
        var set = '';
        if (document.getElementById('optLower').checked) set += 'abcdefghijklmnopqrstuvwxyz';
        if (document.getElementById('optUpper').checked) set += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (document.getElementById('optNumbers').checked) set += '0123456789';
        if (document.getElementById('optSymbols').checked) set += '!#$%&*()-_=+[]{};:,.<>?/';
        return set;
    }

    function randomPassword(length, set) {
        var arr = new Uint32Array(length);
        crypto.getRandomValues(arr);
        var out = '';
        for (var i = 0; i < length; i++) { out += set[arr[i] % set.length]; }
        return out;
    }

    function strength(length, setSize) {
        var bits = length * Math.log2(setSize);
        if (bits < 40) return { label: 'Weak', pct: 25, cls: 'bg-danger' };
        if (bits < 60) return { label: 'Fair', pct: 50, cls: 'bg-warning' };
        if (bits < 80) return { label: 'Strong', pct: 75, cls: 'bg-info' };
        return { label: 'Very Strong', pct: 100, cls: 'bg-success' };
    }

    document.getElementById('generateBtn').addEventListener('click', function () {
        var err = document.getElementById('pwError');
        var res = document.getElementById('pwResults');
        var list = document.getElementById('pwList');
        err.classList.add('d-none');
        var set = charset();
        if (!set) { err.textContent = 'Please select at least one character type.'; err.classList.remove('d-none'); res.classList.add('d-none'); return; }
        var length = parseInt(range.value, 10);
        list.innerHTML = '';
        for (var i = 0; i < 4; i++) {
            var pw = randomPassword(length, set);
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-center gap-2';
            var code = document.createElement('code');
            code.className = 'text-break';
            code.textContent = pw;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary flex-shrink-0';
            btn.textContent = 'Copy';
            btn.setAttribute('data-pw', pw);
            btn.addEventListener('click', function () {
                var val = this.getAttribute('data-pw');
                var done = function () { document.getElementById('copyMsg').classList.remove('d-none'); };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(val).then(done).catch(done);
                } else { done(); }
            });
            item.appendChild(code);
            item.appendChild(btn);
            list.appendChild(item);
        }
        var s = strength(length, set.length);
        var bar = document.getElementById('strengthBar');
        bar.style.width = s.pct + '%';
        bar.className = 'progress-bar ' + s.cls;
        document.getElementById('strengthLabel').textContent = s.label;
        document.getElementById('copyMsg').classList.add('d-none');
        res.classList.remove('d-none');
    });
})();
</script>
@endsection
