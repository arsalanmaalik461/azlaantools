@extends('layouts.app')

@section('title', 'Report Card Comment Bank - Azlaan Tools')
@section('meta_description', 'Pick a performance level and subject to get ready-to-use report card comments, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Report Card Comment Bank</h1>
            <p class="lead text-muted">Choose the performance level and subject — get ready-made report card comments, then edit and print them.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="levelSel" class="form-label fw-semibold">Performance level</label>
                            <select class="form-select" id="levelSel">
                                <option value="excellent">Excellent (A / A+)</option>
                                <option value="good">Good (B)</option>
                                <option value="satisfactory">Satisfactory (C)</option>
                                <option value="needs">Needs Improvement (D)</option>
                                <option value="poor">Unsatisfactory (F / Fail)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="subjSel" class="form-label fw-semibold">Subject</label>
                            <select class="form-select" id="subjSel">
                                <option value="general">General</option>
                                <option value="math">Mathematics</option>
                                <option value="english">English</option>
                                <option value="urdu">Urdu</option>
                                <option value="science">Science</option>
                                <option value="behavior">Behavior / Conduct</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Get Comments</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <p class="fw-semibold">Comments — click a comment to add it below:</p>
                        <div class="list-group mb-3" id="commentList"></div>
                        <h5>Selected comments</h5>
                        <textarea class="form-control" id="selectedBox" rows="6" placeholder="Selected comments will appear here — you can edit them too."></textarea>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-outline-primary flex-fill" id="copyBtn">Copy All</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="clearBtn">Clear</button>
                            <button type="button" class="btn btn-success flex-fill" id="printBtn">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the student performance level and subject.</li>
                <li>Press Get Comments — click a comment to add it below.</li>
                <li>Edit if needed, then copy or print.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var levelSel = document.getElementById('levelSel');
    var subjSel = document.getElementById('subjSel');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var commentList = document.getElementById('commentList');
    var selectedBox = document.getElementById('selectedBox');
    var copyBtn = document.getElementById('copyBtn');
    var clearBtn = document.getElementById('clearBtn');
    var printBtn = document.getElementById('printBtn');

    var LEVELS = {
        excellent: [
            'Consistently produces outstanding work and sets a high standard for the class.',
            'Shows excellent understanding of all concepts and applies them confidently.',
            'A self-motivated learner who always goes beyond what is expected.',
            'Demonstrates excellent problem-solving skills and creative thinking.',
            'Always completes assignments on time with remarkable accuracy.',
            'Actively participates in class discussions and helps fellow students.',
            'Shows excellent progress and a genuine love for learning.',
            'A role model for the class in both work and conduct.'
        ],
        good: [
            'Shows a good understanding of the subject and performs consistently well.',
            'Completes work carefully and meets expectations in most areas.',
            'Participates in class and shows steady improvement over the term.',
            'Demonstrates good effort and a positive attitude towards learning.',
            'With continued focus, has the potential to achieve excellent results.',
            'Handles most tasks independently and asks for help when needed.',
            'Shows good organizational skills and submits work on time.',
            'A cooperative student who contributes well to group activities.'
        ],
        satisfactory: [
            'Meets the basic requirements of the subject but needs more consistent effort.',
            'Shows understanding of key concepts but should practice more regularly.',
            'Performance is satisfactory; greater attention to detail would help.',
            'Completes most work but sometimes needs reminders and guidance.',
            'Capable of better results with more focus and regular revision.',
            'Participates occasionally; more active involvement is encouraged.',
            'Shows progress in some areas but needs to strengthen fundamentals.',
            'With regular study habits, performance can improve significantly.'
        ],
        needs: [
            'Needs to put in more effort to meet the expected standard.',
            'Struggles with key concepts; regular practice and revision are essential.',
            'Often submits incomplete work; needs to take studies more seriously.',
            'Distraction in class is affecting performance; focus is required.',
            'Needs extra support at home with daily homework and revision.',
            'Falling behind the class; consistent effort is urgently needed.',
            'Test performance does not reflect true ability; more preparation needed.',
            'Parents are requested to monitor study routine closely.'
        ],
        poor: [
            'Performance is unsatisfactory; immediate and serious effort is required.',
            'Fails to meet minimum requirements in this subject.',
            'Rarely completes assignments; a major change in attitude is needed.',
            'Needs intensive remedial work to catch up with the class.',
            'Parents are requested to meet the teacher to discuss a support plan.',
            'Lack of preparation is clearly reflected in results.',
            'Must repeat and practice basic concepts before moving forward.',
            'Without significant improvement, promotion may be at risk.'
        ]
    };

    var SUBJECTS = {
        general: [
            'Overall performance this term has been {level} and reflects genuine effort.',
            'Homework and classwork are completed with {level} consistency.',
            'Shows {level} time management and organizational skills.',
            'Attendance and punctuality have been {level} throughout the term.',
            'Responds {level} to feedback and works on suggested improvements.'
        ],
        math: [
            'Problem-solving skills are {level}; calculations are usually accurate.',
            'Shows {level} understanding of formulas and their application.',
            'Needs to show complete working steps to secure full marks.',
            'Mental math and tables practice would strengthen performance.',
            'Word problems are handled with {level} confidence.'
        ],
        english: [
            'Reading fluency and comprehension are {level}.',
            'Writing skills show {level} grammar, vocabulary and expression.',
            'Spelling and handwriting need {level} attention.',
            'Participates {level} in speaking and listening activities.',
            'Creative writing shows {level} imagination and structure.'
        ],
        urdu: [
            'Urdu parhne ki rawani {level} hai.',
            'Likhnay mein imla aur khattati par {level} tawajjuh hai.',
            'Urdu grammar (sarf-o-nahw) ki samajh {level} hai.',
            'Nazm-o-nasr ki tashreeh {level} andaz mein ki jati hai.',
            'Urdu bolne aur samajhne ki salahiyat {level} hai.'
        ],
        science: [
            'Shows {level} understanding of scientific concepts and experiments.',
            'Practical work and observations are recorded with {level} care.',
            'Diagrams and labelling skills are {level}.',
            'Asks thoughtful questions showing {level} curiosity.',
            'Applies science concepts to daily life with {level} understanding.'
        ],
        behavior: [
            'Conduct in class has been {level} throughout the term.',
            'Shows {level} respect towards teachers and classmates.',
            'Follows school rules and routines with {level} consistency.',
            'Cooperation in group work is {level}.',
            'Discipline and self-control have been {level} this term.'
        ]
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

    goBtn.addEventListener('click', function () {
        hideError();
        var level = levelSel.value;
        var subj = subjSel.value;
        var levelWord = levelSel.options[levelSel.selectedIndex].text.split(' ')[0].toLowerCase();

        var pool = (LEVELS[level] || []).concat((SUBJECTS[subj] || []).map(function (c) {
            return c.replace(/\{level\}/g, levelWord);
        }));

        if (!pool.length) { showError('No comments found for this selection. Please try again.'); return; }

        commentList.innerHTML = '';
        pool.forEach(function (c) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'list-group-item list-group-item-action';
            b.textContent = c;
            b.addEventListener('click', function () {
                var cur = selectedBox.value.trim();
                selectedBox.value = cur ? cur + '\n' + c : c;
                b.classList.add('active');
                setTimeout(function () { b.classList.remove('active'); }, 300);
            });
            commentList.appendChild(b);
        });
        results.classList.remove('d-none');
    });

    copyBtn.addEventListener('click', function () {
        if (!selectedBox.value.trim()) { showError('Please select a comment first.'); return; }
        hideError();
        selectedBox.select();
        try {
            var ok = document.execCommand('copy');
            if (!ok && navigator.clipboard) { navigator.clipboard.writeText(selectedBox.value); }
            copyBtn.textContent = 'Copied!';
            setTimeout(function () { copyBtn.textContent = 'Copy All'; }, 1500);
        } catch (e) { showError('Copy did not work. Select the text and copy it yourself.'); }
    });

    clearBtn.addEventListener('click', function () { selectedBox.value = ''; });

    printBtn.addEventListener('click', function () {
        var w = window.open('', '_blank');
        var body = selectedBox.value.trim().split('\n').map(function (l) {
            return '<p>' + l.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</p>';
        }).join('');
        w.document.write('<!doctype html><html><head><title>Report Card Comments</title></head><body>' +
            '<h2>Report Card Comments</h2>' + body + '</body></html>');
        w.document.close();
        w.focus();
        w.print();
    });
})();
</script>
@endsection
