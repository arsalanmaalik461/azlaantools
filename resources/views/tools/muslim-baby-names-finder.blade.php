@extends('layouts.app')

@section('title', 'Muslim Baby Names Finder - Azlaan Tools')
@section('meta_description', 'Muslim baby names with meanings — Islamic names for boys and girls, with search and random picker. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Muslim Baby Names Finder</h1>
            <p class="lead text-muted">Search for an Islamic name for a boy or girl — with the famous meaning of each name. Search or pick a random name.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search names... (e.g. Ahmed, Noor)">
                        </div>
                        <div class="col-12">
                            <div class="btn-group w-100" role="group" id="genderGroup">
                                <button type="button" class="btn btn-outline-primary active" data-g="all">All</button>
                                <button type="button" class="btn btn-outline-primary" data-g="b">Boys 👦</button>
                                <button type="button" class="btn btn-outline-primary" data-g="g">Girls 👧</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Search</button>
                        <button type="button" class="btn btn-outline-secondary flex-fill" id="randomBtn">🎲 Random Name</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="text-muted"><span id="countOut">0</span> names found</p>
                        <div id="namesList" class="row g-2"></div>
                        <p class="text-muted mt-3 mb-0"><small>Meanings are usually the well-known ones — verify with a scholar before naming your child.</small></p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type part of a name in the search box or select a gender.</li>
                <li>Press <strong>Search</strong> — matching names will appear with their meanings.</li>
                <li>Use <strong>Random Name</strong> to pick a lovely name.</li>
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
        {n:'Muhammad',g:'b',m:'The most praised'},
        {n:'Ahmad',g:'b',m:'Most worthy of praise'},
        {n:'Ali',g:'b',m:'High, great'},
        {n:'Hassan',g:'b',m:'Beautiful, good'},
        {n:'Hussain',g:'b',m:'Little Hassan, beautiful'},
        {n:'Umar',g:'b',m:'Long-lived, prosperous'},
        {n:'Usman',g:'b',m:'Pious, wise'},
        {n:'Abu Bakr',g:'b',m:'Name of the first caliph'},
        {n:'Bilal',g:'b',m:'First muezzin, chosen'},
        {n:'Talha',g:'b',m:'Fruit-bearing tree'},
        {n:'Zubair',g:'b',m:'Strong, powerful'},
        {n:'Hamza',g:'b',m:'Lion, strong'},
        {n:'Abdullah',g:'b',m:'Servant of Allah'},
        {n:'Abdul Rehman',g:'b',m:'Servant of the Most Merciful'},
        {n:'Abdul Malik',g:'b',m:'Servant of the King (Allah)'},
        {n:'Abdul Aziz',g:'b',m:'Servant of the Almighty'},
        {n:'Abdul Qadir',g:'b',m:'Servant of the All-Powerful'},
        {n:'Abdul Hadi',g:'b',m:'Servant of the Guide'},
        {n:'Daniyal',g:'b',m:'Wise judge'},
        {n:'Ibrahim',g:'b',m:'Father of nations'},
        {n:'Ismail',g:'b',m:'Allah hears'},
        {n:'Yusuf',g:'b',m:'Allah increases'},
        {n:'Musa',g:'b',m:'Drawn from the water'},
        {n:'Yahya',g:'b',m:'The living one'},
        {n:'Zakariya',g:'b',m:'Allah remembers'},
        {n:'Sulaiman',g:'b',m:'Peaceful'},
        {n:'Dawood',g:'b',m:'Beloved, dear'},
        {n:'Ayyub',g:'b',m:'Patient'},
        {n:'Yunus',g:'b',m:'Dove (soft-hearted)'},
        {n:'Zaid',g:'b',m:'Growing, increase'},
        {n:'Anas',g:'b',m:'Love, affection'},
        {n:'Saad',g:'b',m:'Happiness, success'},
        {n:'Saeed',g:'b',m:'Fortunate'},
        {n:'Tariq',g:'b',m:'Morning star'},
        {n:'Khalid',g:'b',m:'Eternal'},
        {n:'Ammar',g:'b',m:'Builder'},
        {n:'Arslan',g:'b',m:'Lion, brave'},
        {n:'Azlaan',g:'b',m:'Lion (bravery)'},
        {n:'Rayan',g:'b',m:'Mirage, gate of Paradise'},
        {n:'Zayan',g:'b',m:'Bright, adorned'},
        {n:'Ayaan',g:'b',m:'Gift of Allah'},
        {n:'Farhan',g:'b',m:'Happy, joyful'},
        {n:'Imran',g:'b',m:'Prosperity'},
        {n:'Kamran',g:'b',m:'Successful'},
        {n:'Salman',g:'b',m:'Safe, secure'},
        {n:'Rizwan',g:'b',m:'Pleasure, contentment'},
        {n:'Adnan',g:'b',m:'Settler'},
        {n:'Owais',g:'b',m:'Little wolf (brave)'},
        {n:'Huzaifa',g:'b',m:'Pure, clean'},
        {n:'Yasir',g:'b',m:'Easy, prosperous'},
        {n:'Junaid',g:'b',m:'Soldier, warrior'},
        {n:'Danish',g:'b',m:'Wisdom'},
        {n:'Fahad',g:'b',m:'Panther (fast)'},
        {n:'Fahim',g:'b',m:'Intelligent'},
        {n:'Haseeb',g:'b',m:'Noble, respected'},
        {n:'Irfan',g:'b',m:'Knowledge, recognition'},
        {n:'Kashif',g:'b',m:'Revealer'},
        {n:'Luqman',g:'b',m:'Wise'},
        {n:'Mahmood',g:'b',m:'Praised'},
        {n:'Mansoor',g:'b',m:'Victorious, helped'},
        {n:'Nabeel',g:'b',m:'Noble, good'},
        {n:'Naeem',g:'b',m:'Comfort, blessing'},
        {n:'Naseer',g:'b',m:'Helper'},
        {n:'Qasim',g:'b',m:'Distributor'},
        {n:'Rafay',g:'b',m:'One who raises high'},
        {n:'Rehan',g:'b',m:'Fragrant plant'},
        {n:'Sadiq',g:'b',m:'Truthful'},
        {n:'Shahid',g:'b',m:'Witness'},
        {n:'Mustafa',g:'b',m:'Chosen'},
        {n:'Murtaza',g:'b',m:'Beloved, chosen one'},
        {n:'Hadi',g:'b',m:'Guide'},
        {n:'Karim',g:'b',m:'Generous, honored'},
        {n:'Jameel',g:'b',m:'Beautiful'},
        {n:'Noor',g:'b',m:'Light'},
        {n:'Zafar',g:'b',m:'Victory, success'},
        {n:'Fatima',g:'g',m:'Pure, chaste'},
        {n:'Ayesha',g:'g',m:'Living, alive'},
        {n:'Khadija',g:'g',m:'First Muslim woman'},
        {n:'Zainab',g:'g',m:'Fragrant flower'},
        {n:'Maryam',g:'g',m:'Pure, beloved'},
        {n:'Amina',g:'g',m:'Trustworthy'},
        {n:'Hafsa',g:'g',m:'Little lioness (brave)'},
        {n:'Ruqayya',g:'g',m:'Height, progress'},
        {n:'Safiya',g:'g',m:'Pure, chosen'},
        {n:'Asma',g:'g',m:'High rank'},
        {n:'Sumayya',g:'g',m:'Exalted, first female martyr'},
        {n:'Juwairiya',g:'g',m:'Little girl (lovely)'},
        {n:'Maimuna',g:'g',m:'Blessed'},
        {n:'Hania',g:'g',m:'Happy, peaceful'},
        {n:'Mahnoor',g:'g',m:'Moonlight'},
        {n:'Eman',g:'g',m:'Faith, belief'},
        {n:'Areeba',g:'g',m:'Wise, intelligent'},
        {n:'Areej',g:'g',m:'Fragrance'},
        {n:'Alina',g:'g',m:'Soft, delicate'},
        {n:'Aliza',g:'g',m:'Happiness, joy'},
        {n:'Amna',g:'g',m:'Safe, peaceful'},
        {n:'Anaya',g:'g',m:'Answer of Allah, grace'},
        {n:'Anabia',g:'g',m:'Turning to Allah'},
        {n:'Aiza',g:'g',m:'Honorable, noble'},
        {n:'Azka',g:'g',m:'Pure, clean'},
        {n:'Bisma',g:'g',m:'Smile'},
        {n:'Dua',g:'g',m:'Prayer, supplication'},
        {n:'Eshal',g:'g',m:'Fragrant flower'},
        {n:'Fajar',g:'g',m:'Morning light'},
        {n:'Fariha',g:'g',m:'Happy, glad'},
        {n:'Hiba',g:'g',m:'Gift'},
        {n:'Hoor',g:'g',m:'Beauty of Paradise'},
        {n:'Inaya',g:'g',m:'Care, grace'},
        {n:'Iqra',g:'g',m:'Read (first revelation)'},
        {n:'Isra',g:'g',m:'Night journey (Miraj)'},
        {n:'Jannat',g:'g',m:'Garden, Paradise'},
        {n:'Mahira',g:'g',m:'Skilled, talented'},
        {n:'Malaika',g:'g',m:'Angels'},
        {n:'Manahil',g:'g',m:'Springs, fountains'},
        {n:'Marwa',g:'g',m:'Hill of Safa Marwa'},
        {n:'Mishal',g:'g',m:'Torch, light'},
        {n:'Nabeeha',g:'g',m:'Smart, intelligent'},
        {n:'Rania',g:'g',m:'Like a queen'},
        {n:'Rida',g:'g',m:'Pleasure, contentment'},
        {n:'Saba',g:'g',m:'Morning breeze'},
        {n:'Sadia',g:'g',m:'Fortunate'},
        {n:'Sahar',g:'g',m:'Early morning'},
        {n:'Saira',g:'g',m:'Traveler'},
        {n:'Sana',g:'g',m:'Bright, shining'},
        {n:'Shifa',g:'g',m:'Health, cure'},
        {n:'Sidra',g:'g',m:'Tree of Paradise'},
        {n:'Tuba',g:'g',m:'Goodness, tree of Paradise'},
        {n:'Warda',g:'g',m:'Rose'},
        {n:'Yumna',g:'g',m:'Mubarak, ba barkat'},
        {n:'Zahra',g:'g',m:'Bright, radiant'},
        {n:'Zunaira',g:'g',m:'Flower of Paradise'},
        {n:'Zoya',g:'g',m:'Lively, bright'},
        {n:'Zara',g:'g',m:'Flower, brightness'},
        {n:'Hareem',g:'g',m:'Sacred, protected'},
        {n:'Iffat',g:'g',m:'Chastity, purity'},
        {n:'Kinza',g:'g',m:'Treasure'},
        {n:'Laiba',g:'g',m:'Beautiful, charming'},
        {n:'Maham',g:'g',m:'Moon'},
        {n:'Maha',g:'g',m:'Beautiful eyes'},
        {n:'Nimra',g:'g',m:'Pure'},
        {n:'Rabia',g:'g',m:'Spring season'},
        {n:'Uswa',g:'g',m:'Example, model'}
    ];

    var goBtn = document.getElementById('goBtn');
    var randomBtn = document.getElementById('randomBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var gender = 'all';

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    var gBtns = document.querySelectorAll('#genderGroup button');
    Array.prototype.forEach.call(gBtns, function (b) {
        b.addEventListener('click', function () {
            Array.prototype.forEach.call(gBtns, function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            gender = b.getAttribute('data-g');
            hideError();
        });
    });

    function render(list) {
        var wrap = document.getElementById('namesList');
        wrap.innerHTML = '';
        list.forEach(function (it) {
            var div = document.createElement('div');
            div.className = 'col-12 col-sm-6';
            var badge = it.g === 'b' ? '<span class="badge bg-primary">Boy</span>' : '<span class="badge" style="background:#d63384">Girl</span>';
            div.innerHTML = '<div class="border rounded p-2 bg-light d-flex justify-content-between align-items-center">' +
                '<div><strong>' + it.n + '</strong><br><small class="text-muted">' + it.m + '</small></div>' + badge + '</div>';
            wrap.appendChild(div);
        });
        document.getElementById('countOut').textContent = list.length;
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var q = document.getElementById('searchInput').value.trim().toLowerCase();
        var list = NAMES.filter(function (it) {
            if (gender !== 'all' && it.g !== gender) return false;
            if (q && it.n.toLowerCase().indexOf(q) === -1) return false;
            return true;
        });
        if (!list.length) { showError('No name found — change the search or gender and try again.'); return; }
        render(list.slice(0, 200));
    });

    randomBtn.addEventListener('click', function () {
        hideError();
        var pool = NAMES.filter(function (it) { return gender === 'all' || it.g === gender; });
        var pick = pool[Math.floor(Math.random() * pool.length)];
        render([pick]);
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    document.getElementById('searchInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') goBtn.click();
    });
})();
</script>
@endsection
