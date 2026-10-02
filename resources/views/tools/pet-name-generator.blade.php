@extends('layouts.app')
@section('title', 'Pet Name Generator - Azlaan Tools')
@section('meta_description', 'Find cute, funny and cool names for your dog, cat, bird and other pets. Free pet name generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Pet Name Generator</h1>
            <p class="lead text-muted">Find the perfect name for your beloved pet — cute, funny and cool names for dogs, cats, birds and more.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="petSel" class="form-label fw-semibold">Pet</label>
                            <select class="form-select" id="petSel">
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="bird">Bird</option>
                                <option value="fish">Fish</option>
                                <option value="rabbit">Rabbit</option>
                                <option value="other">Other pet</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="genderSel" class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="genderSel">
                                <option value="any">Any</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="styleSel" class="form-label fw-semibold">Style</label>
                            <select class="form-select" id="styleSel">
                                <option value="cute">Cute</option>
                                <option value="funny">Funny</option>
                                <option value="cool">Cool</option>
                                <option value="royal">Royal</option>
                                <option value="mix" selected>Mix</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="letterIn" class="form-label fw-semibold">Start with this letter (optional)</label>
                        <input type="text" class="form-control" id="letterIn" placeholder="e.g. M" maxlength="1">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Names</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h2 class="h6 mb-3">Tap to copy:</h2>
                        <div id="nameGrid" class="row g-2"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select pet, gender and style.</li>
                <li>If you want, enter a starting letter.</li>
                <li>Press "Generate Names" — tap your favourite name to copy it.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nameGrid = document.getElementById('nameGrid');
    var petSel = document.getElementById('petSel');
    var genderSel = document.getElementById('genderSel');
    var styleSel = document.getElementById('styleSel');
    var letterIn = document.getElementById('letterIn');

    // m = male-leaning, f = female-leaning, a = any
    var NAMES = {
        dog: {
            cute: { m: ['Buddy', 'Max', 'Rocky', 'Teddy', 'Coco', 'Oreo'], f: ['Bella', 'Daisy', 'Luna', 'Coco', 'Molly', 'Rosie'], a: ['Biscuit', 'Peanut', 'Mochi', 'Pudding', 'Noodle'] },
            funny: { m: ['Bark Obama', 'Sir Wags', 'Chewie'], f: ['Miss Fluffy', 'Queen Bee'], a: ['Waffles', 'Pickles', 'Meatball', 'Nacho', 'Tater Tot', 'Biscuit'] },
            cool: { m: ['Rex', 'Ace', 'Zeus', 'Blaze', 'Diesel'], f: ['Nova', 'Zara', 'Storm', 'Vega'], a: ['Onyx', 'Jett', 'Echo'] },
            royal: { m: ['King', 'Duke', 'Prince', 'Caesar', 'Sultan'], f: ['Queen', 'Duchess', 'Princess', 'Cleopatra'], a: ['Majesty', 'Regal'] }
        },
        cat: {
            cute: { m: ['Simba', 'Oliver', 'Leo', 'Milo'], f: ['Luna', 'Misty', 'Kitty', 'Snowball'], a: ['Mochi', 'Pudding', 'Bubbles', 'Paws'] },
            funny: { m: ['Sir Meows', 'Chairman Meow'], f: ['Mrs Whiskers'], a: ['Waffles', 'Beans', 'Noodle', 'Pickle', 'Taco'] },
            cool: { m: ['Shadow', 'Smokey', 'Ash'], f: ['Midnight', 'Onyx'], a: ['Ghost', 'Storm', 'Vega'] },
            royal: { m: ['King', 'Pharaoh', 'Sultan'], f: ['Queen', 'Cleopatra', 'Duchess'], a: ['Czar', 'Empress'] }
        },
        bird: {
            cute: { m: ['Sunny', 'Kiwi', 'Pico'], f: ['Sunny', 'Tweety'], a: ['Chirpy', 'Pip', 'Beaky', 'Sunny', 'Coco'] },
            funny: { m: [], f: [], a: ['Captain Squawk', 'Beaky Blinder', 'Feather Locklear', 'Polly Wanna Cracker'] },
            cool: { m: ['Skye', 'Jet'], f: ['Sky'], a: ['Aero', 'Zephyr', 'Comet'] },
            royal: { m: ['King', 'Falcon'], f: ['Queen'], a: ['Majesty', 'Eagle'] }
        },
        fish: {
            cute: { m: [], f: [], a: ['Bubbles', 'Nemo', 'Goldie', 'Splash', 'Finn', 'Coral', 'Pebbles'] },
            funny: { m: [], f: [], a: ['Captain Jack Sparrowfin', 'Swim Shady', 'Fishstick', 'Gill Gates', 'Bait'] },
            cool: { m: [], f: [], a: ['Neptune', 'Tsunami', 'Reef', 'Aqua'] },
            royal: { m: ['King Neptune'], f: ['Queen Marina'], a: ['Poseidon', 'Atlantis'] }
        },
        rabbit: {
            cute: { m: ['Thumper', 'Bunbun'], f: ['Bunbun', 'Clover'], a: ['Hoppy', 'Clover', 'Snowball', 'Carrot', 'Mochi'] },
            funny: { m: [], f: [], a: ['Bugsy', 'Hops', 'Sir Hopsalot', 'Carrot Cake'] },
            cool: { m: ['Dash', 'Bolt'], f: [], a: ['Turbo', 'Zoom', 'Flash'] },
            royal: { m: ['King', 'Duke'], f: ['Queen', 'Duchess'], a: ['Prince', 'Princess'] }
        },
        other: {
            cute: { m: ['Buddy', 'Teddy'], f: ['Daisy', 'Luna'], a: ['Mochi', 'Pudding', 'Peanut', 'Bubbles', 'Coco'] },
            funny: { m: [], f: [], a: ['Waffles', 'Pickles', 'Noodle', 'Beans', 'Taco'] },
            cool: { m: ['Ace', 'Blaze'], f: ['Nova', 'Zara'], a: ['Onyx', 'Echo', 'Jett'] },
            royal: { m: ['King', 'Duke', 'Prince'], f: ['Queen', 'Duchess', 'Princess'], a: ['Majesty'] }
        }
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

    function copyText(text, btn) {
        function done() {
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-primary');
            setTimeout(function () {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 700);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            done();
        }
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var pet = petSel.value;
        var gender = genderSel.value;
        var style = styleSel.value;
        var letter = letterIn.value.trim().toLowerCase();

        var styles = style === 'mix' ? ['cute', 'funny', 'cool', 'royal'] : [style];
        var pool = [];
        styles.forEach(function (st) {
            var grp = NAMES[pet][st];
            ['m', 'f', 'a'].forEach(function (g) {
                if (gender === 'any' || gender === g || g === 'a') {
                    grp[g].forEach(function (n) { pool.push(n); });
                }
            });
        });
        pool = pool.filter(function (n, i) { return pool.indexOf(n) === i; });
        if (letter) {
            pool = pool.filter(function (n) { return n.charAt(0).toLowerCase() === letter; });
        }
        if (!pool.length) {
            showError(letter
                ? 'No name found starting with this letter. Leave the letter blank and try again.'
                : 'No names found. Try again.');
            return;
        }

        var count = Math.min(12, pool.length);
        var picked = pool.slice();
        for (var i = picked.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = picked[i]; picked[i] = picked[j]; picked[j] = tmp;
        }
        picked = picked.slice(0, count);

        nameGrid.innerHTML = '';
        picked.forEach(function (nm) {
            var col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary w-100 text-truncate';
            btn.textContent = nm;
            btn.title = 'Copy: ' + nm;
            btn.addEventListener('click', function () { copyText(nm, btn); });
            col.appendChild(btn);
            nameGrid.appendChild(col);
        });
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
