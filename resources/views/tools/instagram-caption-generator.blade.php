@extends('layouts.app')

@section('title', 'Instagram Caption Generator - Azlaan Tools')
@section('meta_description', 'Make Instagram captions from templates — choose an occasion and mood. Free, with hashtags.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Instagram Caption Generator</h1>
            <p class="lead text-muted">Choose an occasion and a mood — get a ready caption + hashtags. Enter your own topic and the caption will be about it.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="occasion" class="form-label fw-semibold">Occasion</label>
                            <select class="form-control" id="occasion">
                                <option value="general">General / Daily</option>
                                <option value="birthday">Birthday</option>
                                <option value="travel">Travel / Vacation</option>
                                <option value="food">Food</option>
                                <option value="wedding">Wedding</option>
                                <option value="fitness">Fitness / Gym</option>
                                <option value="business">Business / Shop</option>
                                <option value="festival">Eid / Festival</option>
                                <option value="motivation">Motivation</option>
                                <option value="selfie">Selfie</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="mood" class="form-label fw-semibold">Mood</label>
                            <select class="form-control" id="mood">
                                <option value="fun">Funny</option>
                                <option value="cool">Cool</option>
                                <option value="sweet">Sweet</option>
                                <option value="deep">Deep</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label for="topic" class="form-label fw-semibold">Topic (optional)</label>
                        <input type="text" class="form-control" id="topic" placeholder="e.g. chai, dinner with friends, new shop">
                    </div>
                    <div class="mb-3">
                        <label for="tagCount" class="form-label fw-semibold">Hashtags: <span id="tagCountVal">8</span></label>
                        <input type="range" class="form-range" id="tagCount" min="0" max="20" value="8">
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="goBtn">Make Caption</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="card bg-light"><div class="card-body">
                            <pre class="mb-0" id="captionOut" style="white-space: pre-wrap; font-family: inherit;"></pre>
                        </div></div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="small text-muted" id="charCount"></span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="againBtn">Make Again</button>
                                <button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Caption</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose an occasion and a mood, and write a topic (optional).</li>
                <li>Press "Make Caption" — caption + hashtags are ready.</li>
                <li>If you do not like it, use "Make Again" for a new version, then copy and paste it into Instagram.</li>
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

    var captions = {
        general: {
            fun: ['Just like that, enjoying life with {t} ☕', 'Mood: {t} and a little fun ✨', 'The plan was to go straight home... then {t} happened 😄'],
            cool: ['{T} — just my style. 😎', 'Less talk, more {t}.', 'Level up. {T} mode on. 🔥'],
            sweet: ['Small little joys: {t} 💛', 'Today is all about {t} 🌸', '{T} — the thing that makes hearts happy.'],
            deep: ['{T} teaches us: peace is in the little things. 🌿', 'Time passes, but memories of {t} stay.']
        },
        birthday: {
            fun: ['Eat the cake, do not count the calories — happy birthday! 🎂', 'One year older, one year wiser (maybe) 😜 {t}'],
            cool: ['Birthday mode: ON. Cake mode: DOUBLE. 🎂🔥 {t}', 'Age is just a number — party is mandatory. {t}'],
            sweet: ['Happy birthday! Wishing you health and happiness 🎉 {t}', 'People like you make life beautiful. Happy birthday! 💛 {t}'],
            deep: ['New year, new dreams. Happy birthday — keep moving forward. 🌟 {t}']
        },
        travel: {
            fun: ['Out on a trip, no plan to come back 😄 {t}', 'The GPS says we arrived, but the heart says keep exploring! {t}'],
            cool: ['New cities, new views. {T} diaries ✈️', 'Passport ready, mood set. {t} 🌍'],
            sweet: ['A journey is beautiful when the destination is {t} 💛', 'Making memories — {t} 🌅'],
            deep: ['Travel changes a person. {T} taught me so much. 🌿']
        },
        food: {
            fun: ['Diet starts tomorrow, today is {t}! 😋', 'Food first, the world later. {T} time 🍽️'],
            cool: ['Foodie mode activated. {T} on the table 🔥', 'Taste > everything. {t} 🍴'],
            sweet: ['Nothing makes you happy like home food. {T} 💛', 'Sweet memories, sweet {t} 🍰'],
            deep: ['Food feeds more than the stomach — it feeds the soul. {T} 🌿']
        },
        wedding: {
            fun: ['Wedding season is on! Ate more than the bride and groom at {T} 😄', 'The wedding belongs to the couple, the dance floor belongs to me 😜 {t}'],
            cool: ['Wedding vibes on point. {T} ✨', 'Desi wedding > everything. {t} 💥'],
            sweet: ['Congratulations to the happy couple! {T} 💛', 'A new life begins — may they both stay happy 🤲 {t}'],
            deep: ['Wishing the couple a blessed new journey. {T} 🌙']
        },
        fitness: {
            fun: ['Went to the gym... took 2 hours to come back 💪😅 {t}', 'Pain now, results later. {t}'],
            cool: ['No pain, no gain. {T} grind 🔥', 'Beast mode: {t} 💪'],
            sweet: ['Health is the biggest wealth. One step toward {T} 💛', 'Love yourself — start with {t}.'],
            deep: ['Your body is a gift. {T} is how you take care of it. 🌿']
        },
        business: {
            fun: ['The shop is open and so is our heart — come try {t}! 😄', 'Happy customers, happy us. {T} 🛍️'],
            cool: ['Business mode ON. {T} — quality guaranteed 🔥', 'New stock, new prices. {t} 💼'],
            sweet: ['Thank you for your trust. {T} 💛', 'Small shop, big love. {t} 🌸'],
            deep: ['Hard work always pays off. {T} is that journey. 🌿']
        },
        festival: {
            fun: ['Eid mubarak! Keep the sheer khurma ready 😄 {t}', 'Eidi first, talk later! {t} 🎉'],
            cool: ['Festival vibes max. {T} ✨', 'Celebration mode: {t} 🔥'],
            sweet: ['Eid mubarak! Do not forget to share the happiness 💛 {t}', 'Festivals are about meeting loved ones. {T} 🌙'],
            deep: ['Eid is a message of patience and thanks. {T} 🤲']
        },
        motivation: {
            fun: ['Get up, do {t}, win the world... after chai ☕😄', 'Work hard, then watch Netflix. Balance! {t}'],
            cool: ['Hustle hard. {T} — no excuses 🔥', 'Dream big, work bigger. {t} 💪'],
            sweet: ['Every morning brings new hope. {T} 💛', 'You can do it — start with {t}. 🌸'],
            deep: ['Success does not come overnight — you have to do {t} every day. 🌿']
        },
        selfie: {
            fun: ['The filter did magic again today 😄 {t}', 'Took a selfie, attitude comes free with it. {t}'],
            cool: ['Me and my style. {T} 😎', 'Confidence level: {t} 🔥'],
            sweet: ['A smile is the most beautiful jewel 💛 {t}', 'Mood today: happy. {T} 🌸'],
            deep: ['Know yourself — {t} starts right there. 🌿']
        }
    };

    var hashtagPools = {
        general: ['#dailypost', '#instagood', '#picoftheday', '#life', '#goodvibes', '#pakistan', '#instadaily', '#photooftheday'],
        birthday: ['#happybirthday', '#birthday', '#birthdayboy', '#birthdaygirl', '#celebration', '#party', '#cake', '#birthdayvibes'],
        travel: ['#travel', '#wanderlust', '#travelgram', '#vacation', '#explore', '#travelpakistan', '#adventure', '#instatravel'],
        food: ['#foodie', '#food', '#foodstagram', '#yummy', '#desifood', '#foodlover', '#instafood', '#tasty'],
        wedding: ['#wedding', '#shaadi', '#desiwedding', '#bridal', '#weddingday', '#dulhan', '#weddingseason', '#love'],
        fitness: ['#fitness', '#gym', '#workout', '#fit', '#gymlife', '#health', '#training', '#fitnessmotivation'],
        business: ['#business', '#smallbusiness', '#shoplocal', '#onlineshopping', '#pakistanbusiness', '#entrepreneur', '#supportlocal', '#shopnow'],
        festival: ['#eidmubarak', '#eid', '#festival', '#celebration', '#eidulfitr', '#eiduladha', '#blessed', '#family'],
        motivation: ['#motivation', '#inspiration', '#goals', '#mindset', '#success', '#nevergiveup', '#hustle', '#dreambig'],
        selfie: ['#selfie', '#me', '#selfietime', '#instaselfie', '#smile', '#attitude', '#selflove', '#picoftheday']
    };

    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    document.getElementById('tagCount').addEventListener('input', function () {
        document.getElementById('tagCountVal').textContent = this.value;
    });

    function generate() {
        hideError();
        var occ = document.getElementById('occasion').value;
        var mood = document.getElementById('mood').value;
        var topic = document.getElementById('topic').value.trim();
        var tagN = parseInt(document.getElementById('tagCount').value, 10);
        var pool = captions[occ][mood] || captions.general.fun;
        var t = topic || { general: 'this moment', birthday: 'the party', travel: 'this trip', food: 'this food', wedding: 'this wedding', fitness: 'this workout', business: 'this offer', festival: 'this festival', motivation: 'this day', selfie: 'this look' }[occ];
        var cap = pick(pool).replace(/{t}/g, t).replace(/{T}/g, t.charAt(0).toUpperCase() + t.slice(1));
        var tags = hashtagPools[occ].slice();
        for (var i = tags.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var tmp = tags[i]; tags[i] = tags[j]; tags[j] = tmp; }
        var out = cap;
        if (tagN > 0) out += '\n\n' + tags.slice(0, tagN).join(' ');
        document.getElementById('captionOut').textContent = out;
        document.getElementById('charCount').textContent = out.length + ' / 2200 characters';
        results.classList.remove('d-none');
    }

    goBtn.addEventListener('click', generate);
    document.getElementById('againBtn').addEventListener('click', generate);

    document.getElementById('copyBtn').addEventListener('click', function () {
        var txt = document.getElementById('captionOut').textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(txt).then(function () {
                var b = document.getElementById('copyBtn');
                b.textContent = 'Copied!';
                setTimeout(function () { b.textContent = 'Copy Caption'; }, 1500);
            });
        } else { showError('Copy is not supported — please select the text manually.'); }
    });
})();
</script>
@endsection
