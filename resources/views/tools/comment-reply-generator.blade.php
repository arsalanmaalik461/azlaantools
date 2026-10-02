@extends('layouts.app')

@section('title', 'Comment Reply Generator - Azlaan Tools')
@section('meta_description', 'Generate polite replies to comments for business and personal use. Free online comment reply generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Comment Reply Generator</h1>
            <p class="lead text-muted">Generate polite and professional replies to comments — for business reviews, social media and personal messages.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="commentInput" class="form-label fw-semibold">Write the comment / review</label>
                        <textarea class="form-control" id="commentInput" rows="3" placeholder="Example: Great service, thank you!"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="commentType" class="form-label fw-semibold">Comment type</label>
                            <select class="form-select" id="commentType">
                                <option value="positive">Positive / Praise</option>
                                <option value="negative">Negative / Complaint</option>
                                <option value="question">Question</option>
                                <option value="suggestion">Suggestion / Advice</option>
                                <option value="neutral">Neutral / General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="toneSel" class="form-label fw-semibold">Tone</label>
                            <select class="form-select" id="toneSel">
                                <option value="friendly">Friendly</option>
                                <option value="formal">Formal / Professional</option>
                                <option value="short">Short</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="nameInput" class="form-label fw-semibold">Your name / brand (optional)</label>
                        <input type="text" class="form-control" id="nameInput" placeholder="Example: Azlaan Solar">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Replies</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold mb-2">3 ready replies — copy the one you like:</p>
                        <div id="replyList"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Write the comment or review you want to reply to.</li>
                <li>Choose the comment type (praise, complaint, question...) and the tone.</li>
                <li>Press "Generate Replies" — you will get 3 different replies, copy and use them.</li>
            </ol>
            <p class="text-muted small">Tip: Always answer complaints calmly and with a solution — never show anger.</p>
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

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function pick(arr, seed) {
        return arr[seed % arr.length];
    }

    function generate(comment, type, tone, brand) {
        var c = comment.toLowerCase();
        var detail = '';
        var kw = ['service', 'quality', 'price', 'delivery', 'product', 'staff', 'work', 'rate', 'time', 'item'];
        for (var i = 0; i < kw.length; i++) {
            if (c.indexOf(kw[i]) !== -1) { detail = kw[i]; break; }
        }
        var b = brand ? ' — ' + brand : '';
        var replies = [];

        if (type === 'positive') {
            replies.push('Thank you so much for your kind words!' + (detail ? ' We are glad you liked our ' + detail + '.' : '') + ' Your trust is our real success.' + b);
            replies.push('Thank you very much! Customers like you push us to keep getting better. Please give us another chance to serve you.' + b);
            replies.push('Your feedback means a lot to us! ' + (detail ? 'We are glad our ' + detail + ' met your expectations. ' : '') + 'Stay connected — even better things are coming.' + b);
        } else if (type === 'negative') {
            replies.push('We are sorry to hear about your complaint. This is not our standard — please send your details in our inbox so we can fix it right away.' + b);
            replies.push('We are truly sorry for the inconvenience. Your experience matters a lot to us — we are looking into this issue and will contact you.' + b);
            replies.push('We are sorry for the trouble you faced. Please share your order or contact number and we will solve this on priority.' + b);
        } else if (type === 'question') {
            replies.push('Good question! ' + (detail ? 'About ' + detail + ', here are the details: ' : '') + 'Please make your question a little clearer or message us in our inbox so we can give a full answer.' + b);
            replies.push('Thanks for asking! We are sending you the full details in your inbox. If you have more questions, please ask.' + b);
            replies.push('Yes, of course — we are happy to answer this question. Message us for details and we will reply right away.' + b);
        } else if (type === 'suggestion') {
            replies.push('Great suggestion — thank you! We will think about it and try to improve. Feedback like this is how we move forward.' + b);
            replies.push('Thanks for the suggestion! Your point is noted — our team will work on it. If you have more ideas, please share them.' + b);
            replies.push('Your suggestion is valuable. We will include it in our next planning. Thank you for your support!' + b);
        } else {
            replies.push('Thanks for your comment! We are happy to hear from you. If you have more questions or suggestions, please tell us.' + b);
            replies.push('Thank you! We love staying in touch with you. If you need any help, we are here.' + b);
            replies.push('Noted — thanks for sharing! Your opinion matters to us.' + b);
        }

        if (tone === 'formal') {
            replies = replies.map(function (r) {
                return r.replace(/Thank you so much/g, 'Thank you very much').replace(/Thanks/g, 'Thank you');
            });
        } else if (tone === 'short') {
            var shorts = {
                positive: ['Thank you very much!' + b, 'Thank you so much!' + b, 'Glad to hear — thank you!' + b],
                negative: ['Sorry — please send the details in our inbox and we will fix it.' + b, 'Sorry for the trouble. We are looking into it right away.' + b, 'We are sorry — please share your contact so we can solve this.' + b],
                question: ['Good question — sending the details in your inbox.' + b, 'Yes, answering this in your inbox.' + b, 'Thanks for asking — details coming in your inbox.' + b],
                suggestion: ['Great suggestion — thank you! Noted.' + b, 'Thanks — we will look into it.' + b, 'Valuable suggestion, thank you!' + b],
                neutral: ['Thank you!' + b, 'Thanks for your comment!' + b, 'Noted — thank you!' + b]
            };
            replies = shorts[type] || shorts.neutral;
        }
        return replies;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var comment = document.getElementById('commentInput').value.trim();
        if (!comment) { showError('Please enter the comment first.'); return; }
        var type = document.getElementById('commentType').value;
        var tone = document.getElementById('toneSel').value;
        var brand = document.getElementById('nameInput').value.trim();

        var replies = generate(comment, type, tone, brand);
        var html = '';
        replies.forEach(function (r, i) {
            html += '<div class="card mb-2"><div class="card-body">';
            html += '<div class="d-flex justify-content-between align-items-start gap-2">';
            html += '<p class="mb-0 flex-grow-1">' + esc(r) + '</p>';
            html += '<button type="button" class="btn btn-sm btn-outline-primary copyOne flex-shrink-0" data-i="' + i + '">Copy</button>';
            html += '</div></div></div>';
        });
        var list = document.getElementById('replyList');
        list.innerHTML = html;
        results.classList.remove('d-none');

        var btns = list.querySelectorAll('.copyOne');
        for (var k = 0; k < btns.length; k++) {
            (function (btn) {
                btn.addEventListener('click', function () {
                    var text = replies[parseInt(btn.getAttribute('data-i'), 10)];
                    var done = function () {
                        btn.textContent = 'Copied!';
                        setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
                    };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(done, function () { showError('Could not copy.'); });
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
            })(btns[k]);
        }
    });
})();
</script>
@endsection
