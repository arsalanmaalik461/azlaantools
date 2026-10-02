@extends('layouts.app')

@section('title', 'Asma ul Husna 99 Names - Azlaan Tools')
@section('meta_description', 'Read and learn the 99 names of Allah (Asma ul Husna) with Arabic, transliteration and meanings. Free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Asma ul Husna — 99 Names of Allah</h1>
            <p class="lead text-muted">The 99 blessed names of Allah — with the Arabic name, English name and meaning. Search, read and memorize them.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="searchBox" class="form-label fw-semibold">Search names</label>
                            <input type="text" class="form-control" id="searchBox" placeholder="e.g. Rahman, Ghaffar, رحمٰن...">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary w-100" id="copyBtn">Copy All Names</button>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <p class="mt-3 mb-0"><span id="countBox" class="badge bg-primary">99</span> <span class="text-muted small">names shown</span></p>
                </div>
            </div>

            <div id="results" class="row g-3"></div>

            <p class="text-muted small mt-4">Note: The order of names may differ slightly in different books and sources. Meanings are given as brief explanations.</p>

            <h2 class="mt-4">How to use</h2>
            <ol>
                <li>Search by typing a name or meaning in the search box above.</li>
                <li>Each card shows the number, Arabic name, English name and meaning.</li>
                <li>"Copy All Names" copies the full list to the clipboard.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var searchBox = document.getElementById('searchBox');
    var copyBtn = document.getElementById('copyBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var countBox = document.getElementById('countBox');

    var names = [
        ['الرحمن', 'Ar-Rahman', 'The Most Gracious — The Extremely Merciful'],
        ['الرحيم', 'Ar-Rahim', 'The Most Merciful — The Very Kind'],
        ['الملك', 'Al-Malik', 'The King — The Ruler of All'],
        ['القدوس', 'Al-Quddus', 'The Most Holy — The Most Pure'],
        ['السلام', 'As-Salam', 'The Source of Peace — The Giver of Safety'],
        ['المؤمن', 'Al-Mu’min', 'The Giver of Faith — The Giver of Security'],
        ['المهيمن', 'Al-Muhaymin', 'The Guardian — The Protector'],
        ['العزيز', 'Al-Aziz', 'The Almighty — The Overpowering'],
        ['الجبار', 'Al-Jabbar', 'The Compeller — The One with Power over All'],
        ['المتكبر', 'Al-Mutakabbir', 'The Supreme — The Possessor of Greatness'],
        ['الخالق', 'Al-Khaliq', 'The Creator — The One who Creates'],
        ['البارئ', 'Al-Bari', 'The Originator — The One Who Creates Without Equal'],
        ['المصور', 'Al-Musawwir', 'The Fashioner — The Shaper of Forms'],
        ['الغفار', 'Al-Ghaffar', 'The Great Forgiver — The One who Forgives Much'],
        ['القهار', 'Al-Qahhar', 'The Irresistible — The Dominant over All'],
        ['الوهاب', 'Al-Wahhab', 'The Bestower — The Generous Giver'],
        ['الرزاق', 'Ar-Razzaq', 'The Provider — The Giver of Sustenance'],
        ['الفتاح', 'Al-Fattah', 'The Opener — The Solver of Difficulties'],
        ['العليم', 'Al-Alim', 'The All-Knowing — The One who Knows Everything'],
        ['القابض', 'Al-Qabid', 'The Withholder — The Restrictor'],
        ['الباسط', 'Al-Basit', 'The Extender — The One who Expands'],
        ['الخافض', 'Al-Khafid', 'The Abaser — The One who Lowers'],
        ['الرافع', 'Ar-Rafi’', 'The Exalter — The One Who Raises'],
        ['المعز', 'Al-Mu’izz', 'The Honourer — The Giver of Honour'],
        ['المذل', 'Al-Mudhill', 'The Humiliator — The Giver of Disgrace'],
        ['السميع', 'As-Sami’', 'The All-Hearing — The One Who Hears Everything'],
        ['البصير', 'Al-Basir', 'The All-Seeing — The One who Sees Everything'],
        ['الحكم', 'Al-Hakam', 'The Judge — The Decider'],
        ['العدل', 'Al-Adl', 'The Utterly Just — The Fully Fair'],
        ['اللطيف', 'Al-Latif', 'The Subtle One — The Most Gentle, the Perceptive'],
        ['الخبير', 'Al-Khabir', 'The All-Aware — The One Aware of Everything'],
        ['الحليم', 'Al-Halim', 'The Forbearing — The Patient'],
        ['العظيم', 'Al-Azim', 'The Magnificent — The Possessor of Greatness'],
        ['الغفور', 'Al-Ghafoor', 'The All-Forgiving — The One who Forgives Much'],
        ['الشكور', 'Ash-Shakur', 'The Appreciative — The Valuer of Good'],
        ['العلي', 'Al-Ali', 'The Most High — The Highest'],
        ['الكبير', 'Al-Kabir', 'The Most Great — The Greatest'],
        ['الحفيظ', 'Al-Hafiz', 'The Preserver — The Protector'],
        ['المقيت', 'Al-Muqit', 'The Sustainer — The Provider of Food'],
        ['الحسيب', 'Al-Hasib', 'The Reckoner — The Accountant of Deeds'],
        ['الجليل', 'Al-Jalil', 'The Majestic — The Possessor of Majesty'],
        ['الكريم', 'Al-Karim', 'The Most Generous — The Very Kind'],
        ['الرقيب', 'Ar-Raqib', 'The Watchful — The Observer'],
        ['المجيب', 'Al-Mujib', 'The Responsive — The Answerer of Prayers'],
        ['الواسع', 'Al-Wasi’', 'The All-Encompassing — The Vast'],
        ['الحكيم', 'Al-Hakim', 'The All-Wise — The Possessor of Wisdom'],
        ['الودود', 'Al-Wadud', 'The Most Loving — The One who Loves Much'],
        ['المجيد', 'Al-Majid', 'The Most Glorious — The Noble'],
        ['الباعث', 'Al-Ba’ith', 'The Resurrector — The One who Raises Again'],
        ['الشهيد', 'Ash-Shahid', 'The Witness — The Observer'],
        ['الحق', 'Al-Haqq', 'The Absolute Truth — The Truly Real'],
        ['الوكيل', 'Al-Wakil', 'The Trustee — The Disposer of Affairs'],
        ['القوي', 'Al-Qawi', 'The All-Strong — The Powerful'],
        ['المتين', 'Al-Matin', 'The Firm — The Strong'],
        ['الولي', 'Al-Waliyy', 'The Protecting Friend — The Helper'],
        ['الحميد', 'Al-Hamid', 'The Praiseworthy — The Worthy of Praise'],
        ['المحصي', 'Al-Muhsi', 'The Appraiser — The Counter'],
        ['المبدئ', 'Al-Mubdi’', 'The Originator — The One Who Creates for the First Time'],
        ['المعيد', 'Al-Mu’id', 'The Restorer — The One who Recreates'],
        ['المحيي', 'Al-Muhyi', 'The Giver of Life'],
        ['المميت', 'Al-Mumit', 'The Bringer of Death'],
        ['الحي', 'Al-Hayy', 'The Ever-Living'],
        ['القيوم', 'Al-Qayyum', 'The Self-Subsisting — The Self-Sufficient'],
        ['الواجد', 'Al-Wajid', 'The Perceiver — The Finder'],
        ['الماجد', 'Al-Majid', 'The Noble — The Honoured'],
        ['الواحد', 'Al-Wahid', 'The One — The Unique'],
        ['الأحد', 'Al-Ahad', 'The Indivisible — The Single'],
        ['الصمد', 'As-Samad', 'The Eternal — The Need-Free'],
        ['القادر', 'Al-Qadir', 'The Capable — The Possessor of Power'],
        ['المقتدر', 'Al-Muqtadir', 'The Powerful — The Fully Powerful'],
        ['المقدم', 'Al-Muqaddim', 'The Expediter — The Advancer'],
        ['المؤخر', 'Al-Mu’akhkhir', 'The Delayer — The One who Delays'],
        ['الأول', 'Al-Awwal', 'The First'],
        ['الآخر', 'Al-Akhir', 'The Last'],
        ['الظاهر', 'Az-Zahir', 'The Manifest — The Evident'],
        ['الباطن', 'Al-Batin', 'The Hidden'],
        ['الوالي', 'Al-Wali', 'The Governor — The Ruler'],
        ['المتعالي', 'Al-Muta’ali', 'The Most Exalted — The Highest'],
        ['البر', 'Al-Barr', 'The Source of Goodness — The Doer of Good'],
        ['التواب', 'At-Tawwab', 'The Acceptor of Repentance'],
        ['المنتقم', 'Al-Muntaqim', 'The Avenger — The Retaliator'],
        ['العفو', 'Al-Afuww', 'The Pardoner — The Forgiver'],
        ['الرؤوف', 'Ar-Ra’uf', 'The Most Kind — The Extremely Compassionate'],
        ['مالك الملك', 'Malik-ul-Mulk', 'Master of the Kingdom — The Owner of Sovereignty'],
        ['ذو الجلال والإكرام', 'Dhul-Jalali-wal-Ikram', 'Possessor of Glory and Honour'],
        ['المقسط', 'Al-Muqsit', 'The Equitable — The Just'],
        ['الجامع', 'Al-Jami’', 'The Gatherer — The One Who Gathers'],
        ['الغني', 'Al-Ghaniyy', 'The Self-Sufficient — The Need-Free'],
        ['المغني', 'Al-Mughni', 'The Enricher — The One who Enriches'],
        ['المانع', 'Al-Mani’', 'The Preventer — The One Who Withholds'],
        ['الضار', 'Ad-Darr', 'The Distresser — The One with Power to Harm'],
        ['النافع', 'An-Nafi’', 'The Benefactor — The One Who Gives Benefit'],
        ['النور', 'An-Nur', 'The Light'],
        ['الهادي', 'Al-Hadi', 'The Guide'],
        ['البديع', 'Al-Badi’', 'The Incomparable Originator — The One Who Creates Without Equal'],
        ['الباقي', 'Al-Baqi', 'The Ever-Surviving — The Everlasting'],
        ['الوارث', 'Al-Warith', 'The Inheritor — The Heir'],
        ['الرشيد', 'Ar-Rashid', 'The Guide to Right Path — The One who Shows the Straight Path'],
        ['الصبور', 'As-Sabur', 'The Patient — The Most Patient']
    ];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function render(filter) {
        hideError();
        var q = (filter || '').toLowerCase().trim();
        var html = '';
        var count = 0;
        names.forEach(function (n, i) {
            var num = i + 1;
            var hay = (n[0] + ' ' + n[1] + ' ' + n[2] + ' ' + num).toLowerCase();
            if (q && hay.indexOf(q) === -1) { return; }
            count++;
            html += '<div class="col-12 col-sm-6 col-md-4">';
            html += '<div class="card h-100 shadow-sm"><div class="card-body text-center">';
            html += '<span class="badge bg-secondary mb-2">' + num + '</span>';
            html += '<div class="fs-3 mb-1" dir="rtl" lang="ar">' + esc(n[0]) + '</div>';
            html += '<div class="fw-semibold">' + esc(n[1]) + '</div>';
            html += '<div class="text-muted small">' + esc(n[2]) + '</div>';
            html += '</div></div></div>';
        });
        results.innerHTML = html || '<div class="col-12"><div class="alert alert-info">No name found. Try a different search.</div></div>';
        countBox.textContent = count;
    }

    searchBox.addEventListener('input', function () {
        render(searchBox.value);
    });

    copyBtn.addEventListener('click', function () {
        hideError();
        var text = '';
        names.forEach(function (n, i) {
            text += (i + 1) + '. ' + n[0] + ' - ' + n[1] + ' (' + n[2] + ')\n';
        });
        var done = function () {
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy All Names'; }, 2000);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy. Check your browser permission.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); }
            catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });

    render('');
})();
</script>
@endsection
