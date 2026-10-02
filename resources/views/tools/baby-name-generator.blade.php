@extends('layouts.app')

@section('title', 'Baby Name Generator - Azlaan Tools')
@section('meta_description', 'Discover beautiful baby names by gender and origin with meanings. Free baby name generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Baby Name Generator</h1>
            <p class="lead text-muted">Discover beautiful baby names by gender and origin, with meanings. Pick a lovely name — use filters or generate random names.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label for="genderSel" class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="genderSel">
                                <option value="any">Boy / Girl</option>
                                <option value="B">Boy</option>
                                <option value="G">Girl</option>
                                <option value="U">Unisex</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="originSel" class="form-label fw-semibold">Origin</label>
                            <select class="form-select" id="originSel">
                                <option value="any">Any origin</option>
                                <option>Arabic</option>
                                <option>Urdu</option>
                                <option>Persian</option>
                                <option>Turkish</option>
                                <option>Hebrew</option>
                                <option>Latin</option>
                                <option>Greek</option>
                                <option>Sanskrit</option>
                                <option>English</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="letterInput" class="form-label fw-semibold">Starts with (optional)</label>
                            <input type="text" class="form-control" id="letterInput" maxlength="1" placeholder="e.g. A">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="countSel" class="form-label fw-semibold">How many</label>
                            <select class="form-select" id="countSel">
                                <option>5</option>
                                <option selected>10</option>
                                <option>20</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Names</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" id="resultTitle"></h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">Copy List</button>
                        </div>
                        <div id="nameGrid" class="row g-2"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select gender and origin (or leave as "any").</li>
                <li>Press "Generate Names" — you get new random names every time.</li>
                <li>Click a favorite name to copy it, or copy the whole list.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var NAMES = [
        { n: 'Aariz', g: 'B', o: 'Arabic', m: 'Respectable, leader' },
        { n: 'Aayan', g: 'B', o: 'Arabic', m: 'Gift of God' },
        { n: 'Adeel', g: 'B', o: 'Arabic', m: 'Just, fair' },
        { n: 'Ahmed', g: 'B', o: 'Arabic', m: 'Highly praised' },
        { n: 'Ali', g: 'B', o: 'Arabic', m: 'Exalted, noble' },
        { n: 'Arham', g: 'B', o: 'Arabic', m: 'Merciful' },
        { n: 'Arslan', g: 'B', o: 'Turkish', m: 'Lion, brave' },
        { n: 'Ayaan', g: 'B', o: 'Arabic', m: 'God\'s gift' },
        { n: 'Azlaan', g: 'B', o: 'Urdu', m: 'Lion-like, strong' },
        { n: 'Bilal', g: 'B', o: 'Arabic', m: 'Moist, fresh' },
        { n: 'Danish', g: 'B', o: 'Persian', m: 'Wise, knowledgeable' },
        { n: 'Daniyal', g: 'B', o: 'Hebrew', m: 'God is my judge' },
        { n: 'Fahad', g: 'B', o: 'Arabic', m: 'Panther, swift' },
        { n: 'Farhan', g: 'B', o: 'Arabic', m: 'Joyful, merry' },
        { n: 'Hamza', g: 'B', o: 'Arabic', m: 'Strong, steadfast' },
        { n: 'Hassan', g: 'B', o: 'Arabic', m: 'Handsome, good' },
        { n: 'Huzaifa', g: 'B', o: 'Arabic', m: 'One who keeps promises' },
        { n: 'Ibrahim', g: 'B', o: 'Hebrew', m: 'Father of nations' },
        { n: 'Imran', g: 'B', o: 'Arabic', m: 'Prosperity' },
        { n: 'Izaan', g: 'B', o: 'Arabic', m: 'Obedience' },
        { n: 'Kamran', g: 'B', o: 'Persian', m: 'Successful' },
        { n: 'Mahad', g: 'B', o: 'Arabic', m: 'Guided one' },
        { n: 'Mikaeel', g: 'B', o: 'Hebrew', m: 'Who is like God' },
        { n: 'Mustafa', g: 'B', o: 'Arabic', m: 'Chosen one' },
        { n: 'Omar', g: 'B', o: 'Arabic', m: 'Long-lived' },
        { n: 'Rayyan', g: 'B', o: 'Arabic', m: 'Lush, well-watered' },
        { n: 'Rehan', g: 'B', o: 'Arabic', m: 'Sweet basil, fragrant' },
        { n: 'Saad', g: 'B', o: 'Arabic', m: 'Happiness, luck' },
        { n: 'Sahil', g: 'B', o: 'Arabic', m: 'Guide, shore' },
        { n: 'Shayan', g: 'B', o: 'Persian', m: 'Worthy, deserving' },
        { n: 'Talha', g: 'B', o: 'Arabic', m: 'Fruit-bearing tree' },
        { n: 'Usman', g: 'B', o: 'Arabic', m: 'Baby bustard (bird)' },
        { n: 'Yahya', g: 'B', o: 'Hebrew', m: 'God is gracious' },
        { n: 'Zayan', g: 'B', o: 'Arabic', m: 'Bright, graceful' },
        { n: 'Aaliya', g: 'G', o: 'Arabic', m: 'Exalted, sublime' },
        { n: 'Aiman', g: 'G', o: 'Arabic', m: 'Righteous, blessed' },
        { n: 'Aiza', g: 'G', o: 'Arabic', m: 'Noble, respected' },
        { n: 'Alina', g: 'G', o: 'Greek', m: 'Bright, beautiful' },
        { n: 'Amara', g: 'G', o: 'Latin', m: 'Eternal, graceful' },
        { n: 'Areeba', g: 'G', o: 'Arabic', m: 'Wise, intelligent' },
        { n: 'Ayesha', g: 'G', o: 'Arabic', m: 'Alive, prosperous' },
        { n: 'Dua', g: 'G', o: 'Arabic', m: 'Prayer, blessing' },
        { n: 'Eshal', g: 'G', o: 'Urdu', m: 'Flower of heaven' },
        { n: 'Fatima', g: 'G', o: 'Arabic', m: 'One who abstains' },
        { n: 'Hadia', g: 'G', o: 'Arabic', m: 'Guide to righteousness' },
        { n: 'Hania', g: 'G', o: 'Arabic', m: 'Happy, delighted' },
        { n: 'Hira', g: 'G', o: 'Arabic', m: 'Diamond, precious' },
        { n: 'Inaya', g: 'G', o: 'Arabic', m: 'Concern, care' },
        { n: 'Iqra', g: 'G', o: 'Arabic', m: 'Read, recite' },
        { n: 'Jannat', g: 'G', o: 'Arabic', m: 'Paradise, garden' },
        { n: 'Mahnoor', g: 'G', o: 'Urdu', m: 'Moonlight' },
        { n: 'Maira', g: 'G', o: 'Arabic', m: 'Shining, bright' },
        { n: 'Manahil', g: 'G', o: 'Arabic', m: 'Fountain, spring' },
        { n: 'Maryam', g: 'G', o: 'Hebrew', m: 'Beloved, wished-for child' },
        { n: 'Minahil', g: 'G', o: 'Arabic', m: 'Spring of water' },
        { n: 'Mishal', g: 'G', o: 'Arabic', m: 'Torch of light' },
        { n: 'Noor', g: 'U', o: 'Arabic', m: 'Light, radiance' },
        { n: 'Rania', g: 'G', o: 'Arabic', m: 'Queen-like, gazing' },
        { n: 'Saba', g: 'G', o: 'Arabic', m: 'Morning breeze' },
        { n: 'Sana', g: 'G', o: 'Arabic', m: 'Brilliance, praise' },
        { n: 'Shiza', g: 'G', o: 'Urdu', m: 'Gift, present' },
        { n: 'Zainab', g: 'G', o: 'Arabic', m: 'Fragrant flower' },
        { n: 'Zara', g: 'G', o: 'Arabic', m: 'Blooming flower' },
        { n: 'Zoya', g: 'G', o: 'Persian', m: 'Alive, loving' },
        { n: 'Aarav', g: 'B', o: 'Sanskrit', m: 'Peaceful sound' },
        { n: 'Vihaan', g: 'B', o: 'Sanskrit', m: 'Dawn, first ray of sun' },
        { n: 'Arjun', g: 'B', o: 'Sanskrit', m: 'Bright, shining' },
        { n: 'Ishaan', g: 'B', o: 'Sanskrit', m: 'Sun, guardian' },
        { n: 'Anaya', g: 'G', o: 'Hebrew', m: 'God has answered' },
        { n: 'Diya', g: 'G', o: 'Sanskrit', m: 'Lamp, light' },
        { n: 'Myra', g: 'G', o: 'Sanskrit', m: 'Extraordinary, sweet' },
        { n: 'Adrian', g: 'B', o: 'Latin', m: 'From the sea' },
        { n: 'Felix', g: 'B', o: 'Latin', m: 'Lucky, fortunate' },
        { n: 'Lucas', g: 'B', o: 'Latin', m: 'Bringer of light' },
        { n: 'Milo', g: 'B', o: 'Latin', m: 'Soldier, gracious' },
        { n: 'Theo', g: 'B', o: 'Greek', m: 'Gift of God' },
        { n: 'Elena', g: 'G', o: 'Greek', m: 'Shining light' },
        { n: 'Sophia', g: 'G', o: 'Greek', m: 'Wisdom' },
        { n: 'Aria', g: 'G', o: 'Latin', m: 'Melody, air' },
        { n: 'Luna', g: 'G', o: 'Latin', m: 'Moon' },
        { n: 'Avery', g: 'U', o: 'English', m: 'Ruler of elves' },
        { n: 'Jordan', g: 'U', o: 'Hebrew', m: 'Flowing down' },
        { n: 'Riley', g: 'U', o: 'English', m: 'Valiant, brave' },
        { n: 'Robin', g: 'U', o: 'English', m: 'Bright fame' },
        { n: 'Sam', g: 'U', o: 'Hebrew', m: 'Heard by God' },
        { n: 'Oliver', g: 'B', o: 'Latin', m: 'Olive tree, peace' },
        { n: 'Ethan', g: 'B', o: 'Hebrew', m: 'Strong, firm' },
        { n: 'Liam', g: 'B', o: 'English', m: 'Strong-willed warrior' },
        { n: 'Noah', g: 'B', o: 'Hebrew', m: 'Rest, comfort' },
        { n: 'Ava', g: 'G', o: 'Latin', m: 'Life, birdlike' },
        { n: 'Mia', g: 'G', o: 'Latin', m: 'Mine, beloved' },
        { n: 'Olivia', g: 'G', o: 'Latin', m: 'Olive tree' },
        { n: 'Emma', g: 'G', o: 'Latin', m: 'Universal, whole' },
        { n: 'Emir', g: 'B', o: 'Turkish', m: 'Commander, prince' },
        { n: 'Kerem', g: 'B', o: 'Turkish', m: 'Generous, noble' },
        { n: 'Elif', g: 'G', o: 'Turkish', m: 'First letter, slender' },
        { n: 'Zeynep', g: 'G', o: 'Turkish', m: 'Precious, valuable' }
    ];

    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var nameGrid = document.getElementById('nameGrid');
    var resultTitle = document.getElementById('resultTitle');
    var copyBtn = document.getElementById('copyBtn');
    var lastList = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function shuffled(a) {
        var arr = a.slice();
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = arr[i]; arr[i] = arr[j]; arr[j] = t;
        }
        return arr;
    }
    function genderLabel(g) {
        return g === 'B' ? 'Boy' : (g === 'G' ? 'Girl' : 'Unisex');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var gender = document.getElementById('genderSel').value;
        var origin = document.getElementById('originSel').value;
        var letter = document.getElementById('letterInput').value.trim().toLowerCase();
        var count = parseInt(document.getElementById('countSel').value, 10);
        var pool = NAMES.filter(function (x) {
            if (gender !== 'any' && x.g !== gender) return false;
            if (origin !== 'any' && x.o !== origin) return false;
            if (letter && x.n.charAt(0).toLowerCase() !== letter) return false;
            return true;
        });
        if (!pool.length) {
            showError('No names match these filters — try a different combination.');
            return;
        }
        var picked = shuffled(pool).slice(0, count);
        lastList = picked;
        nameGrid.innerHTML = '';
        picked.forEach(function (x) {
            var col = document.createElement('div');
            col.className = 'col-12 col-sm-6';
            var card = document.createElement('button');
            card.type = 'button';
            card.className = 'card w-100 text-start shadow-sm';
            card.style.cursor = 'pointer';
            card.title = 'Click to copy';
            card.innerHTML = '<div class="card-body py-2 px-3">' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<span class="fw-bold fs-5"></span>' +
                '<span class="badge bg-primary"></span>' +
                '</div>' +
                '<div class="text-muted small"></div>' +
                '<div class="small fst-italic text-secondary"></div>' +
                '</div>';
            card.querySelector('.fw-bold').textContent = x.n;
            card.querySelector('.badge').textContent = genderLabel(x.g);
            card.querySelector('.text-muted').textContent = x.o + ' origin';
            card.querySelector('.fst-italic').textContent = '"' + x.m + '"';
            card.addEventListener('click', function () {
                var txt = x.n + ' (' + genderLabel(x.g) + ', ' + x.o + ') - ' + x.m;
                if (navigator.clipboard) navigator.clipboard.writeText(txt);
                card.classList.add('border-success');
                setTimeout(function () { card.classList.remove('border-success'); }, 800);
            });
            col.appendChild(card);
            nameGrid.appendChild(col);
        });
        resultTitle.textContent = picked.length + ' names';
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        var txt = lastList.map(function (x) { return x.n + ' (' + x.o + ') - ' + x.m; }).join('\n');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(txt).then(function () {
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy List'; }, 1500);
            });
        }
    });
})();
</script>
@endsection
