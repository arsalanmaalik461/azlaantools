@extends('layouts.app')
@section('title', 'Blog Post Idea Generator - Azlaan Tools')
@section('meta_description', 'Make 50+ blog post titles from your keyword — free content idea generator, no signup.')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Blog Post Idea Generator</h1>
            <p class="lead text-muted">Type your keyword — get 50+ blog post titles instantly. Never let your content calendar stay empty!</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="keywordInput" class="form-label fw-semibold">Your keyword / topic</label>
                        <input type="text" class="form-control" id="keywordInput" placeholder="e.g. solar panels, biryani recipe, freelancing">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="nicheSel" class="form-label fw-semibold">Niche (optional)</label>
                            <select id="nicheSel" class="form-select">
                                <option value="general" selected>General</option>
                                <option value="tech">Technology</option>
                                <option value="food">Food &amp; Cooking</option>
                                <option value="health">Health &amp; Fitness</option>
                                <option value="travel">Travel</option>
                                <option value="business">Business &amp; Money</option>
                                <option value="education">Education</option>
                                <option value="lifestyle">Lifestyle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="countSel" class="form-label fw-semibold">How many ideas?</label>
                            <select id="countSel" class="form-select">
                                <option value="15">15 ideas</option>
                                <option value="30">30 ideas</option>
                                <option value="50" selected>50 ideas</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Ideas</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="copyAllBtn">Copy All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="downloadBtn">Download .txt</button>
                            <span class="text-muted small align-self-center" id="ideaCount"></span>
                        </div>
                        <div id="ideasList" class="list-group"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your keyword or topic (for example "solar panels").</li>
                <li>Select a niche and the number of ideas, then press <strong>Generate Ideas</strong>.</li>
                <li>Press the copy button next to a favorite title, or copy/download them all.</li>
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
    var ideasList = document.getElementById('ideasList');
    var currentIdeas = [];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    var NICHE_EXTRA = {
        tech: ['AI', 'apps', 'gadgets', 'software', 'smartphones'],
        food: ['recipes', 'cooking tips', 'restaurants', 'spices', 'meal plans'],
        health: ['workouts', 'diet plans', 'mental health', 'sleep', 'nutrition'],
        travel: ['destinations', 'itineraries', 'budget travel', 'packing lists', 'visas'],
        business: ['startups', 'marketing', 'sales', 'investing', 'side hustles'],
        education: ['exams', 'study tips', 'courses', 'scholarships', 'careers'],
        lifestyle: ['home decor', 'fashion', 'productivity', 'hobbies', 'minimalism'],
        general: ['tips', 'guides', 'reviews', 'trends', 'resources']
    };

    function buildTemplates(kw, niche) {
        var K = cap(kw);
        var extras = NICHE_EXTRA[niche] || NICHE_EXTRA.general;
        var e1 = extras[0], e2 = extras[1], e3 = extras[2];
        return [
            '10 Best ' + K + ' Tips for Beginners',
            'How to Get Started with ' + K + ': A Complete Guide',
            '15 ' + K + ' Ideas You Need to Try in 2026',
            'The Ultimate Guide to ' + K + ' (Step by Step)',
            '7 Common ' + K + ' Mistakes and How to Avoid Them',
            K + ' vs Alternatives: Which One Is Right for You?',
            'Why ' + K + ' Is More Important Than Ever in 2026',
            'How I Improved My ' + K + ' in Just 30 Days',
            '25 ' + K + ' Statistics That Will Surprise You',
            K + ' for Beginners: Everything You Need to Know',
            'The Pros and Cons of ' + K + ' Explained Simply',
            '12 ' + K + ' Tools and Resources Worth Using',
            'How Much Does ' + K + ' Really Cost? (Honest Breakdown)',
            '5 ' + K + ' Myths You Should Stop Believing',
            'A Day in the Life: ' + K + ' Done Right',
            'The Future of ' + K + ': Trends to Watch in 2026',
            K + ' Checklist: 20 Things to Do Before You Start',
            'How to Choose the Best ' + K + ' for Your Needs',
            '9 ' + K + ' FAQs Answered by Experts',
            'From Zero to Hero: My ' + K + ' Journey',
            'The ' + K + ' Mistake Almost Everyone Makes',
            '20 Creative ' + K + ' Ideas for ' + e1,
            'Is ' + K + ' Worth It? An Honest Review',
            K + ' on a Budget: How to Save Money',
            'The Science Behind ' + K + ' (Simplified)',
            '8 Signs You Need to Rethink Your ' + K,
            'How Experts Approach ' + K + ' Differently',
            'The Beginner\'s Glossary of ' + K + ' Terms',
            '30-Day ' + K + ' Challenge: Rules and Results',
            K + ' Case Study: What Worked and What Did Not',
            'Top 10 ' + K + ' Questions People Ask on Google',
            'How to Master ' + K + ' Without Spending a Fortune',
            'The History of ' + K + ' and Where It Is Heading',
            '5-Minute ' + K + ' Fixes You Can Do Today',
            'What No One Tells You About ' + K,
            'The ' + K + ' Routine of Highly Successful People',
            K + ' Safety Guide: What to Watch Out For',
            'Comparing the Top 5 ' + K + ' Options in 2026',
            'How ' + K + ' Can Improve Your Daily Life',
            'The Environmental Impact of ' + K,
            'Beginner vs Advanced ' + K + ': Key Differences',
            'How to Teach ' + K + ' to Someone Else',
            'The Best ' + K + ' Books, Courses and Channels',
            'My Honest ' + K + ' Review After One Year',
            '10 ' + K + ' Hacks That Actually Work',
            'Why Most People Fail at ' + K + ' (and How to Succeed)',
            'The Complete ' + K + ' Buying Guide',
            K + ' and ' + cap(e2) + ': A Powerful Combination',
            'Seasonal ' + K + ' Guide: What to Do Each Month',
            'How to Stay Consistent with ' + K + ' Long Term'
        ];
    }

    function shuffle(arr) {
        var a = arr.slice(), i, j, tmp;
        for (i = a.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1));
            tmp = a[i]; a[i] = a[j]; a[j] = tmp;
        }
        return a;
    }

    function copyText(text, btn) {
        function done() {
            if (btn) {
                var old = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = old; }, 1200);
            }
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
        } else { fallbackCopy(text); done(); }
    }
    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
        document.body.removeChild(ta);
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var kw = document.getElementById('keywordInput').value.trim();
        if (!kw) { showError('Please enter a keyword.'); return; }
        var niche = document.getElementById('nicheSel').value;
        var count = parseInt(document.getElementById('countSel').value, 10);
        var templates = buildTemplates(kw.toLowerCase(), niche);
        currentIdeas = shuffle(templates).slice(0, count);

        ideasList.innerHTML = '';
        for (var i = 0; i < currentIdeas.length; i++) {
            (function (title, idx) {
                var item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center gap-2';
                var span = document.createElement('span');
                span.textContent = (idx + 1) + '. ' + title;
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-primary flex-shrink-0';
                btn.textContent = 'Copy';
                btn.addEventListener('click', function () { copyText(title, btn); });
                item.appendChild(span);
                item.appendChild(btn);
                ideasList.appendChild(item);
            })(currentIdeas[i], i);
        }
        document.getElementById('ideaCount').textContent = currentIdeas.length + ' ideas generated';
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copyAllBtn').addEventListener('click', function () {
        if (!currentIdeas.length) { return; }
        var text = currentIdeas.map(function (t, i) { return (i + 1) + '. ' + t; }).join('\n');
        copyText(text, document.getElementById('copyAllBtn'));
    });
    document.getElementById('downloadBtn').addEventListener('click', function () {
        if (!currentIdeas.length) { return; }
        var kw = document.getElementById('keywordInput').value.trim() || 'blog';
        var text = 'Blog post ideas for: ' + kw + '\n' + new Date().toDateString() + '\n\n' +
            currentIdeas.map(function (t, i) { return (i + 1) + '. ' + t; }).join('\n');
        var blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'blog-ideas-' + kw.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '.txt';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
    });
})();
</script>
@endsection
