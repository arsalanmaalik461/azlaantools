@extends('layouts.app')

@section('title', 'Outreach Email Generator - Azlaan Tools')
@section('meta_description', 'Generate professional backlink and guest post outreach emails. Free outreach email template generator for link building.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Outreach Email Generator</h1>
            <p class="lead text-muted">Make a professional outreach email for backlinks and guest posts — type your details, then copy a ready-to-send email.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="purpose" class="form-label fw-semibold">Outreach Purpose</label>
                        <select class="form-select" id="purpose">
                            <option value="guestpost">Guest Post Pitch</option>
                            <option value="backlink">Backlink Request (Resource Link)</option>
                            <option value="broken">Broken Link Building</option>
                            <option value="review">Product / Tool Review Request</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="yourName" class="form-label fw-semibold">Your Name</label>
                            <input type="text" class="form-control" id="yourName" placeholder="e.g. Ali Khan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="yourSite" class="form-label fw-semibold">Your Website</label>
                            <input type="text" class="form-control" id="yourSite" placeholder="e.g. example.com">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bloggerName" class="form-label fw-semibold">Recipient Name</label>
                            <input type="text" class="form-control" id="bloggerName" placeholder="e.g. Sarah (or Team)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="blogName" class="form-label fw-semibold">Recipient Blog</label>
                            <input type="text" class="form-control" id="blogName" placeholder="e.g. techblog.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="topic" class="form-label fw-semibold">Topic / Article Idea</label>
                        <input type="text" class="form-control" id="topic" placeholder="e.g. 10 free tools for freelancers">
                    </div>
                    <div class="mb-3">
                        <label for="valueProp" class="form-label fw-semibold">Your Value Proposition (optional)</label>
                        <input type="text" class="form-control" id="valueProp" placeholder="e.g. My article already ranks #3 for this keyword">
                        <div class="form-text">The point that helps them — this is what raises your reply rate.</div>
                    </div>
                    <div class="mb-3">
                        <label for="tone" class="form-label fw-semibold">Tone</label>
                        <select class="form-select" id="tone">
                            <option value="friendly">Friendly &amp; casual</option>
                            <option value="professional">Professional</option>
                            <option value="short">Short &amp; direct</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Email</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <label for="subjectOut" class="form-label fw-semibold">Subject</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="subjectOut" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copySubject">Copy</button>
                        </div>
                        <label for="bodyOut" class="form-label fw-semibold">Email Body</label>
                        <textarea class="form-control" id="bodyOut" rows="14" readonly></textarea>
                        <button type="button" class="btn btn-success w-100 mt-3" id="copyBody">Copy Email Body</button>
                        <div class="alert alert-success mt-3 d-none" id="copiedMsg" role="alert">Copied to clipboard.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the outreach purpose and type your details.</li>
                <li>Choose a tone and press Generate.</li>
                <li>Copy the subject and body and paste them into your email — be sure to add a personal touch before sending.</li>
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
    var subjectOut = document.getElementById('subjectOut');
    var bodyOut = document.getElementById('bodyOut');
    var copiedMsg = document.getElementById('copiedMsg');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function copyText(el, msgEl) {
        el.select();
        el.setSelectionRange(0, el.value.length);
        function done() {
            msgEl.classList.remove('d-none');
            setTimeout(function () { msgEl.classList.add('d-none'); }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(el.value).then(done, function () {
                document.execCommand('copy');
                done();
            });
        } else {
            document.execCommand('copy');
            done();
        }
    }

    var GREETINGS = {
        friendly: function (name) { return 'Hi ' + name + ','; },
        professional: function (name) { return 'Dear ' + name + ','; },
        short: function (name) { return 'Hi ' + name + ','; }
    };
    var CLOSINGS = {
        friendly: function (name) { return 'Cheers,\n' + name; },
        professional: function (name) { return 'Best regards,\n' + name; },
        short: function (name) { return 'Thanks,\n' + name; }
    };

    goBtn.addEventListener('click', function () {
        hideError();
        var purpose = document.getElementById('purpose').value;
        var yourName = document.getElementById('yourName').value.trim();
        var yourSite = document.getElementById('yourSite').value.trim().replace(/^https?:\/\//, '');
        var bloggerName = document.getElementById('bloggerName').value.trim();
        var blogName = document.getElementById('blogName').value.trim();
        var topic = document.getElementById('topic').value.trim();
        var valueProp = document.getElementById('valueProp').value.trim();
        var tone = document.getElementById('tone').value;

        if (!yourName) { showError('Please enter your name.'); return; }
        if (!blogName) { showError('Please enter the recipient blog.'); return; }
        if (!topic) { showError('Please enter a topic.'); return; }

        var rec = bloggerName || 'there';
        var greet = GREETINGS[tone](rec);
        var close = CLOSINGS[tone](yourName);
        var valLine = valueProp ? '\nA quick note: ' + valueProp + '.\n' : '';

        var subject = '', body = '';
        if (purpose === 'guestpost') {
            subject = 'Guest post idea for ' + blogName + ': "' + topic + '"';
            body = greet + '\n\nI have been reading ' + blogName + ' for a while now, and I love how you cover practical, reader-first content.\n\nI would love to contribute a guest post titled "' + topic + '". Here is a quick outline of what I would cover:\n- A fresh angle your readers have not seen yet\n- Actionable takeaways with real examples\n- 100% original content written for your audience\n' + valLine + '\nWould you be open to it? If yes, I can have a draft ready within a week.\n\n' + close + (yourSite ? '\n' + yourSite : '');
        } else if (purpose === 'backlink') {
            subject = 'Quick suggestion for your article on ' + blogName;
            body = greet + '\n\nI was reading your article about "' + topic + '" on ' + blogName + ' and noticed it links out to a few great resources.\n\nI recently published an in-depth guide on the same topic (' + (yourSite || 'my site') + ') that I think would be a genuinely useful addition for your readers. It covers everything in one place with updated data.\n' + valLine + '\nWould you consider adding it as a resource? No pressure either way — I just thought it fit well.\n\n' + close + (yourSite ? '\n' + yourSite : '');
        } else if (purpose === 'broken') {
            subject = 'Found a broken link on ' + blogName + ' (quick fix inside)';
            body = greet + '\n\nWhile reading your article on "' + topic + '" I noticed one of the outbound links appears to be broken (returns an error page).\n\nI actually have a detailed, up-to-date resource on the same topic that could replace it: ' + (yourSite || 'my site') + '.\n' + valLine + '\nThought I would flag it — broken links can hurt rankings, and this is an easy fix.\n\n' + close + (yourSite ? '\n' + yourSite : '');
        } else {
            subject = 'Your honest take on "' + topic + '"?';
            body = greet + '\n\nI run ' + (yourSite || 'a website') + ' and we just released "' + topic + '" — I think it is something your audience at ' + blogName + ' would genuinely find useful.\n\nI would love your honest opinion on it. If you like it, a review or mention would mean a lot. Happy to return the favor in any way I can.\n' + valLine + '\nLet me know what you think!\n\n' + close + (yourSite ? '\n' + yourSite : '');
        }

        if (tone === 'short') {
            body = body.replace(/\n{3,}/g, '\n\n');
        }

        subjectOut.value = subject;
        bodyOut.value = body;
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('copySubject').addEventListener('click', function () {
        copyText(subjectOut, copiedMsg);
    });
    document.getElementById('copyBody').addEventListener('click', function () {
        copyText(bodyOut, copiedMsg);
    });
})();
</script>
@endsection
