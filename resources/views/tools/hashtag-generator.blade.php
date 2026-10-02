@extends('layouts.app')

@section('title', 'Hashtag Generator - Azlaan Tools')
@section('meta_description', 'Generate free hashtags for Instagram, TikTok, YouTube and X. Enter your topic and copy ready-to-post hashtag sets.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Hashtag Generator</h1>
            <p class="lead text-muted">Enter your topic — get ready hashtag sets for Instagram, TikTok, YouTube or X. Copy them and paste directly into your post.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="topicInput" class="form-label fw-semibold">Topic / keywords</label>
                        <input type="text" class="form-control" id="topicInput" placeholder="e.g. homemade biryani, or solar panels pakistan">
                        <div class="form-text">Write 2-4 words, e.g. "fitness gym workout".</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="platformSelect" class="form-label fw-semibold">Platform</label>
                            <select class="form-select" id="platformSelect">
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                                <option value="youtube">YouTube</option>
                                <option value="x">X (Twitter)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="countSelect" class="form-label fw-semibold">How many hashtags?</label>
                            <select class="form-select" id="countSelect">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Hashtags</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h5 mb-0">Your hashtags</h2>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copyAllBtn">Copy all</button>
                        </div>
                        <div class="border rounded p-3 bg-light mb-3" id="tagBox" style="line-height: 2;"></div>
                        <p class="small text-muted mb-1"><strong>Tip:</strong> A mix of niche-specific tags + some broad reach tags works best. Do not copy-paste the same set on every post — change it a little.</p>
                        <div class="alert alert-success d-none py-2" id="copiedMsg" role="status">Copied!</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter your topic or keywords (e.g. "desi food recipes").</li>
                <li>Choose the platform and number of hashtags.</li>
                <li>Press the button and get them with "Copy all", then paste into your post.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var topicInput = document.getElementById('topicInput');
    var platformSelect = document.getElementById('platformSelect');
    var countSelect = document.getElementById('countSelect');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var tagBox = document.getElementById('tagBox');
    var copyAllBtn = document.getElementById('copyAllBtn');
    var copiedMsg = document.getElementById('copiedMsg');

    var categories = {
        fitness: { keys: ['fitness', 'gym', 'workout', 'exercise', 'bodybuilding', 'yoga', 'health'], tags: ['fitness', 'gym', 'workout', 'gymlife', 'fitnessmotivation', 'fit', 'training', 'bodybuilding', 'gymrat', 'healthylifestyle', 'personaltrainer', 'muscle', 'fitfam', 'exercise', 'weightloss'] },
        food: { keys: ['food', 'recipe', 'cooking', 'biryani', 'khana', 'baking', 'restaurant', 'eat'], tags: ['food', 'foodie', 'foodlover', 'instafood', 'foodphotography', 'yummy', 'delicious', 'homemade', 'foodblogger', 'recipe', 'cooking', 'foodstagram', 'dinner', 'lunch', 'desifood'] },
        travel: { keys: ['travel', 'trip', 'tour', 'vacation', 'pakistan', 'northern', 'hunza', 'mountains'], tags: ['travel', 'travelgram', 'wanderlust', 'travelphotography', 'instatravel', 'travelblogger', 'adventure', 'explore', 'vacation', 'nature', 'mountains', 'traveling', 'pakistantravel', 'beautifulpakistan', 'roam'] },
        fashion: { keys: ['fashion', 'style', 'clothes', 'dress', 'outfit', 'kurti', 'abaya', 'shalwar'], tags: ['fashion', 'style', 'ootd', 'fashionista', 'outfit', 'instafashion', 'stylish', 'fashionblogger', 'outfitoftheday', 'trend', 'shopping', 'dress', 'lookbook', 'fashionstyle', 'clothing'] },
        photography: { keys: ['photo', 'photography', 'camera', 'portrait', 'wedding'], tags: ['photography', 'photooftheday', 'photographer', 'instaphoto', 'photoshoot', 'portrait', 'capture', 'photo', 'picoftheday', 'photogram', 'naturephotography', 'canon', 'nikon', 'lightroom', 'weddingphotography'] },
        business: { keys: ['business', 'startup', 'shop', 'store', 'brand', 'marketing', 'online'], tags: ['business', 'entrepreneur', 'startup', 'smallbusiness', 'marketing', 'success', 'motivation', 'businessowner', 'branding', 'ecommerce', 'onlineshopping', 'digitalmarketing', 'money', 'hustle', 'supportlocal'] },
        tech: { keys: ['tech', 'mobile', 'phone', 'computer', 'software', 'ai', 'gadget', 'solar', 'bijli'], tags: ['technology', 'tech', 'gadgets', 'innovation', 'smartphone', 'ai', 'technews', 'electronics', 'gadget', 'coding', 'software', 'digital', 'future', 'engineering', 'techtips'] },
        gaming: { keys: ['game', 'gaming', 'pubg', 'freefire', 'ps5', 'gamer', 'esports'], tags: ['gaming', 'gamer', 'videogames', 'esports', 'gaminglife', 'pubg', 'freefire', 'ps5', 'gamingcommunity', 'gameon', 'stream', 'gamers', 'xbox', 'playstation', 'mobilegaming'] },
        beauty: { keys: ['beauty', 'makeup', 'skincare', 'mehndi', 'henna', 'salon'], tags: ['beauty', 'makeup', 'skincare', 'mua', 'beautytips', 'cosmetics', 'makeuplover', 'skincareroutine', 'glow', 'beautyblogger', 'naturalbeauty', 'mehndi', 'salon', 'haircare', 'selfcare'] },
        music: { keys: ['music', 'song', 'singer', 'gana', 'naat', 'qawwali'], tags: ['music', 'song', 'musician', 'singer', 'newmusic', 'instamusic', 'songwriter', 'melody', 'beats', 'concert', 'musiclover', 'desimusic', 'cover', 'playlist', 'vocals'] },
        education: { keys: ['study', 'school', 'exam', 'student', 'learn', 'course', 'teacher', 'parhai'], tags: ['study', 'education', 'student', 'learning', 'school', 'exam', 'studymotivation', 'knowledge', 'teacher', 'onlinelearning', 'studygram', 'college', 'notes', 'motivation', 'success'] },
        motivation: { keys: ['motivation', 'quotes', 'inspiration', 'success', 'mindset'], tags: ['motivation', 'motivationalquotes', 'inspiration', 'success', 'mindset', 'quotes', 'goals', 'believe', 'positivevibes', 'nevergiveup', 'inspire', 'dreambig', 'hardwork', 'lifequotes', 'growth'] },
        pets: { keys: ['cat', 'dog', 'pet', 'billi', 'kutta', 'parrot'], tags: ['pets', 'petsofinstagram', 'dog', 'cat', 'petlover', 'cute', 'animals', 'doglovers', 'catlover', 'puppy', 'kitten', 'petlife', 'furryfriends', 'dogsofinstagram', 'cats'] },
        sports: { keys: ['cricket', 'football', 'sport', 'psl', 'match', 'hockey'], tags: ['sports', 'cricket', 'football', 'psl', 'cricketlover', 'game', 'matchday', 'sportsnews', 'athlete', 'team', 'victory', 'cricketfans', 'pakistancricket', 'fitness', 'champions'] },
        realestate: { keys: ['property', 'plot', 'house', 'ghar', 'realestate', 'rent'], tags: ['realestate', 'property', 'houseforsale', 'investment', 'plot', 'home', 'realtor', 'dreamhome', 'propertyinvestment', 'housing', 'land', 'newhome', 'realestateagent', 'pakistanproperty', 'rent'] }
    };

    var platformTags = {
        instagram: ['reels', 'reelsinstagram', 'explore', 'explorepage', 'viral', 'trending', 'instagood', 'love'],
        tiktok: ['fyp', 'foryou', 'foryoupage', 'viral', 'trending', 'tiktok', 'tiktokpakistan', 'duet'],
        youtube: ['shorts', 'youtubeshorts', 'subscribe', 'youtube', 'youtuber', 'viralvideo', 'trending', 'newvideo'],
        x: ['trending', 'viral', 'pakistan', 'news', 'update', 'thread', 'breaking', 'follow']
    };

    var generic = ['love', 'instadaily', 'photooftheday', 'follow', 'like', 'pakistan', 'desi', 'trendingnow', 'daily', 'best'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function cleanWord(w) {
        return w.toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        copiedMsg.classList.add('d-none');
        var topic = topicInput.value.trim();
        if (!topic) { showError('Please enter a topic first.'); return; }

        var words = topic.split(/\s+/).map(cleanWord).filter(function (w) { return w.length > 1; });
        var lower = topic.toLowerCase();

        var catTags = [];
        for (var key in categories) {
            if (!categories.hasOwnProperty(key)) continue;
            var c = categories[key];
            for (var i = 0; i < c.keys.length; i++) {
                if (lower.indexOf(c.keys[i]) !== -1) { catTags = c.tags.slice(); break; }
            }
            if (catTags.length) break;
        }

        var derived = [];
        words.forEach(function (w) {
            derived.push(w);
            if (w.length > 3) derived.push(w + 'love');
        });

        var plat = platformTags[platformSelect.value] || platformTags.instagram;
        var pool = derived.concat(catTags, plat, generic);
        var seen = {};
        var out = [];
        pool.forEach(function (t) {
            if (!seen[t]) { seen[t] = true; out.push('#' + t); }
        });

        var count = parseInt(countSelect.value, 10) || 20;
        out = out.slice(0, count);

        tagBox.innerHTML = '';
        out.forEach(function (t) {
            var s = document.createElement('span');
            s.className = 'badge bg-primary-subtle text-primary border me-1 mb-1';
            s.style.fontSize = '0.95rem';
            s.textContent = t;
            tagBox.appendChild(s);
        });
        results.classList.remove('d-none');
    });

    copyAllBtn.addEventListener('click', function () {
        var tags = [];
        var badges = tagBox.querySelectorAll('span');
        for (var i = 0; i < badges.length; i++) { tags.push(badges[i].textContent); }
        var text = tags.join(' ');
        function done() {
            copiedMsg.classList.remove('d-none');
            setTimeout(function () { copiedMsg.classList.add('d-none'); }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
        } else { fallbackCopy(text); done(); }
    });

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) { /* noop */ }
        document.body.removeChild(ta);
    }
})();
</script>
@endsection
