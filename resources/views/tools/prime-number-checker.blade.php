@extends('layouts.app')

@section('title', 'Prime Number Checker — Azlaan Tools')
@section('meta_description', 'Free prime number checker. Enter a number to see if it is prime or composite, its smallest factor and full factor list, plus a list of primes up to N and the next prime.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">Prime Number Checker</h1>
            <p class="lead text-muted">Enter any number — see right away if it is prime or composite, with all its factors.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="checkNum" class="form-label fw-semibold">Enter a number</label>
                    <input type="number" class="form-control form-control-lg" id="checkNum" placeholder="e.g. 97" step="1" min="0">
                    <div class="result-box mt-3">
                        <div class="fs-4 fw-bold text-center" id="primeVerdict">—</div>
                        <p class="text-center mb-1" id="primeDetails">Enter a number, the verdict will show here.</p>
                        <p class="small text-muted text-center mb-0" id="primeFactors"></p>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="nextPrimeBtn">Find Next Prime After This Number</button>
                            <div class="fw-semibold mt-2" id="nextPrimeResult"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">List Primes Up To N</h2>
                    <div class="row g-2 align-items-end">
                        <div class="col-8">
                            <label for="listN" class="form-label">Up to (N)</label>
                            <input type="number" class="form-control form-control-lg" id="listN" placeholder="e.g. 100" step="1" min="2">
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-primary w-100 btn-lg" id="listBtn">List Primes</button>
                        </div>
                    </div>
                    <p class="small text-muted mt-2">Note: for the list, N is capped at <strong>100,000</strong> so the browser does not slow down. If you enter a larger N, the list will be made up to 100,000.</p>
                    <p class="fw-semibold mb-1" id="primeCount"></p>
                    <div id="primesList" class="border rounded p-2 small" style="max-height: 200px; overflow-y: auto; word-break: break-word;">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>A prime number is only divisible by 1 and itself — like 2, 3, 5, 7, 11. The checker tests divisors up to the square root of the number; if a divisor is found the number is composite and the smallest factor is shown. The list uses the Sieve of Eratosthenes.</p>
                    <p class="mb-1"><strong>Example 1:</strong> 97 is prime — no number from 2 to 9 divides it fully.</p>
                    <p class="mb-0"><strong>Example 2:</strong> 91 is composite — 91 = 7 &times; 13, the smallest factor is 7, and the factors are 1, 7, 13, 91.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function parseInput(id) {
        var v = document.getElementById(id).value;
        if (v === '' || v === null) return null;
        var p = parseFloat(v);
        if (!isFinite(p)) return null;
        return Math.floor(Math.abs(p));
    }
    function isPrime(n) {
        if (n < 2) return false;
        if (n === 2 || n === 3) return true;
        if (n % 2 === 0 || n % 3 === 0) return false;
        for (var i = 5; i * i <= n; i += 6) {
            if (n % i === 0 || n % (i + 2) === 0) return false;
        }
        return true;
    }
    function smallestFactor(n) {
        if (n < 4) return null;
        if (n % 2 === 0) return 2;
        for (var i = 3; i * i <= n; i += 2) {
            if (n % i === 0) return i;
        }
        return null;
    }
    function allFactors(n) {
        var lo = [], hi = [];
        for (var i = 1; i * i <= n; i++) {
            if (n % i === 0) {
                lo.push(i);
                if (i !== n / i) hi.unshift(n / i);
            }
        }
        return lo.concat(hi);
    }
    function calc() {
        var verdict = document.getElementById('primeVerdict');
        var details = document.getElementById('primeDetails');
        var factors = document.getElementById('primeFactors');
        document.getElementById('nextPrimeResult').textContent = '';
        var n = parseInput('checkNum');
        if (n === null) {
            verdict.textContent = '—';
            details.textContent = 'Enter a number, the verdict will show here.';
            factors.textContent = '';
            return;
        }
        if (n < 2) {
            verdict.textContent = n + ' is neither prime nor composite';
            details.textContent = '0 and 1 are not prime numbers — prime numbers start from 2.';
            factors.textContent = '';
            return;
        }
        if (isPrime(n)) {
            verdict.textContent = n.toLocaleString('en-PK') + ' is a PRIME number ✓';
            details.textContent = 'It is only divisible by 1 and itself (' + n.toLocaleString('en-PK') + ').';
            factors.textContent = 'Factors: 1, ' + n.toLocaleString('en-PK');
        } else {
            var sf = smallestFactor(n);
            var other = sf ? (n / sf) : null;
            verdict.textContent = n.toLocaleString('en-PK') + ' is NOT prime (composite)';
            details.textContent = 'Smallest factor: ' + sf + (other ? ' — ' + n + ' = ' + sf + ' × ' + other : '');
            var f = allFactors(n);
            var shown = f.length > 200 ? f.slice(0, 200).join(', ') + ' …' : f.join(', ');
            factors.textContent = 'All factors (' + f.length + '): ' + shown;
        }
    }
    function findNext() {
        var out = document.getElementById('nextPrimeResult');
        var n = parseInput('checkNum');
        if (n === null) { out.textContent = 'First enter a number above.'; return; }
        var c = n + 1;
        var guard = 0;
        while (!isPrime(c) && guard < 1000000) { c++; guard++; }
        out.textContent = isPrime(c) ? ('Next prime after ' + n.toLocaleString('en-PK') + ' is ' + c.toLocaleString('en-PK')) : '—';
    }
    function listPrimes() {
        var countEl = document.getElementById('primeCount');
        var box = document.getElementById('primesList');
        var n = parseInput('listN');
        if (n === null || n < 2) {
            countEl.textContent = '';
            box.textContent = 'Enter an N of 2 or more.';
            return;
        }
        var capped = false;
        if (n > 100000) { n = 100000; capped = true; }
        var sieve = new Array(n + 1).fill(true);
        sieve[0] = false; sieve[1] = false;
        for (var i = 2; i * i <= n; i++) {
            if (sieve[i]) {
                for (var j = i * i; j <= n; j += i) sieve[j] = false;
            }
        }
        var primes = [];
        for (var k = 2; k <= n; k++) { if (sieve[k]) primes.push(k); }
        countEl.textContent = 'Total primes up to ' + n.toLocaleString('en-PK') + ': ' + primes.length.toLocaleString('en-PK') + (capped ? ' (N capped at 100,000)' : '');
        box.textContent = primes.join(', ');
    }
    document.getElementById('checkNum').addEventListener('input', calc);
    document.getElementById('nextPrimeBtn').addEventListener('click', findNext);
    document.getElementById('listBtn').addEventListener('click', listPrimes);
    document.getElementById('listN').addEventListener('input', function () {
        document.getElementById('primeCount').textContent = '';
    });
    calc();
})();
</script>
@endsection
