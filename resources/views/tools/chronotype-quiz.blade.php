@extends('layouts.app')
@section('title', 'Chronotype Quiz - Free Online | Azlaan Tools')
@section('meta_description', 'Find out if you are a morning person or a night owl - free 2-minute chronotype quiz about your sleep type.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Chronotype Quiz</h1>
            <p class="lead text-muted">Are you a morning person or a night owl? Take this free 2-minute quiz to find your sleep type.</p>

            <div class="alert alert-info small">
                <strong>Disclaimer:</strong> This is an estimate, not a medical diagnosis. If you have sleep problems, talk to a doctor.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="quizBox">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold" id="qCounter"></span>
                            <span class="text-muted small" id="qProgress"></span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" id="qBar" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <h2 class="h5 mb-3" id="qText"></h2>
                        <div id="qOptions" class="d-grid gap-2"></div>
                        <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    </div>

                    <div id="results" class="d-none">
                        <div class="text-center mb-3">
                            <div class="display-4 mb-2" id="resEmoji"></div>
                            <h2 class="h4" id="resTitle"></h2>
                            <p class="text-muted" id="resScore"></p>
                        </div>
                        <p id="resDesc" class="mb-3"></p>
                        <div class="alert alert-light border">
                            <strong>Tips for you:</strong>
                            <ul class="mb-0 mt-2" id="resTips"></ul>
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="retakeBtn">🔄 Retake Quiz</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Answer all 6 questions honestly — choose the option closest to your natural habit.</li>
                <li>Your chronotype (morning lark, intermediate or night owl) appears with personalized tips.</li>
                <li>Retake anytime — no signup, no data leaves your browser.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var questions = [
        {
            text: 'If you were free to choose, what time would be easiest for you to wake up in the morning?',
            options: [
                { t: 'Between 5:00 and 6:30 AM', v: 4 },
                { t: 'Between 6:30 and 7:45 AM', v: 3 },
                { t: 'Between 7:45 and 9:45 AM', v: 2 },
                { t: 'Between 9:45 and 11:00 AM', v: 1 },
                { t: 'After 11:00 AM', v: 0 }
            ]
        },
        {
            text: 'If you were free to choose, what time would you like to go to sleep at night?',
            options: [
                { t: 'Between 8:00 and 9:00 PM', v: 4 },
                { t: 'Between 9:00 and 10:15 PM', v: 3 },
                { t: 'Between 10:15 PM and 12:30 AM', v: 2 },
                { t: 'Between 12:30 and 1:45 AM', v: 1 },
                { t: 'After 1:45 AM', v: 0 }
            ]
        },
        {
            text: 'How do you feel during the first half hour after waking up in the morning?',
            options: [
                { t: 'Completely fresh and ready', v: 4 },
                { t: 'Fairly good', v: 3 },
                { t: 'A little sleepy', v: 2 },
                { t: 'Very sleepy and drowsy', v: 1 },
                { t: 'I do not want to get up at all', v: 0 }
            ]
        },
        {
            text: 'At what time of day do you feel most fresh and productive?',
            options: [
                { t: 'Early morning', v: 4 },
                { t: '10 AM - 12 noon', v: 3 },
                { t: '12 noon - 5 PM', v: 2 },
                { t: '5 PM - 10 PM', v: 1 },
                { t: 'After 10 PM', v: 0 }
            ]
        },
        {
            text: 'How would you feel if you had to work from 11 PM to 6 AM?',
            options: [
                { t: 'Perfectly fine, no problem', v: 0 },
                { t: 'A little hard, but I could manage', v: 1 },
                { t: 'It would be hard', v: 2 },
                { t: 'It would be very hard', v: 3 },
                { t: 'It would feel impossible', v: 4 }
            ]
        },
        {
            text: 'Which category would you put yourself in?',
            options: [
                { t: 'Definitely a morning person', v: 4 },
                { t: 'More of a morning person', v: 3 },
                { t: 'Neither morning nor night - in between', v: 2 },
                { t: 'More of a night owl', v: 1 },
                { t: 'Definitely a night owl', v: 0 }
            ]
        }
    ];

    var quizBox = document.getElementById('quizBox');
    var results = document.getElementById('results');
    var qCounter = document.getElementById('qCounter');
    var qProgress = document.getElementById('qProgress');
    var qBar = document.getElementById('qBar');
    var qText = document.getElementById('qText');
    var qOptions = document.getElementById('qOptions');
    var errorBox = document.getElementById('errorBox');
    var retakeBtn = document.getElementById('retakeBtn');

    var current = 0;
    var score = 0;
    var maxScore = questions.length * 4;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function renderQuestion() {
        hideError();
        var q = questions[current];
        qCounter.textContent = 'Question ' + (current + 1) + ' of ' + questions.length;
        qProgress.textContent = Math.round((current / questions.length) * 100) + '% complete';
        qBar.style.width = ((current / questions.length) * 100) + '%';
        qText.textContent = q.text;
        qOptions.innerHTML = '';
        q.options.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-secondary text-start p-3';
            btn.textContent = opt.t;
            btn.addEventListener('click', function () {
                score += opt.v;
                current++;
                if (current < questions.length) {
                    renderQuestion();
                } else {
                    showResult();
                }
            });
            qOptions.appendChild(btn);
        });
    }

    function chronotypeFor(s) {
        if (s >= 19) {
            return {
                emoji: '🌅', title: 'Definite Morning Lark',
                desc: 'You are a definite morning person - waking up early and working in the morning is your natural habit. Feeling tired early in the evening is normal for you.',
                tips: ['Do your important work between 8 AM and 11 AM.', 'Build a routine of sleeping by 10 PM.', 'Morning sunlight (15-20 minutes) is great for your energy.']
            };
        }
        if (s >= 14) {
            return {
                emoji: '☀️', title: 'Moderate Morning Type',
                desc: 'You lean toward mornings but also have some flexibility. With a steady routine, you keep good energy all day.',
                tips: ['Finish important work before noon.', 'Reduce screen time at night so you sleep on time.', 'Do not change your routine too much on weekends.']
            };
        }
        if (s >= 10) {
            return {
                emoji: '⚖️', title: 'Intermediate Type',
                desc: 'You are neither a strong morning person nor a strong night owl - people in the middle are the most adaptable and can adjust to both routines.',
                tips: ['Notice your peak time and do hard work then.', 'Keeping the same sleep and wake time is most important.', 'A short afternoon nap (20 minutes) can boost productivity.']
            };
        }
        if (s >= 5) {
            return {
                emoji: '🌇', title: 'Moderate Evening Type',
                desc: 'Your mind is most active in the evening and at night. Waking up early is hard for you - this is not laziness, it is your natural rhythm.',
                tips: ['Schedule creative or hard work in the evening.', 'Practice waking up on time before morning meetings.', 'Avoid caffeine after noon.']
            };
        }
        return {
            emoji: '🦉', title: 'Definite Night Owl',
            desc: 'You are a definite night owl - your focus and energy peak at night. A morning schedule is your biggest challenge.',
            tips: ['If possible, choose a late shift or a flexible routine.', 'Bright light and a short walk help you wake up in the morning.', 'Blue light from screens late at night harms sleep - use night mode.']
        };
    }

    function showResult() {
        var c = chronotypeFor(score);
        quizBox.classList.add('d-none');
        results.classList.remove('d-none');
        document.getElementById('resEmoji').textContent = c.emoji;
        document.getElementById('resTitle').textContent = c.title;
        document.getElementById('resScore').textContent = 'Your score: ' + score + ' / ' + maxScore;
        document.getElementById('resDesc').textContent = c.desc;
        var tipsUl = document.getElementById('resTips');
        tipsUl.innerHTML = '';
        c.tips.forEach(function (t) {
            var li = document.createElement('li');
            li.textContent = t;
            tipsUl.appendChild(li);
        });
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    retakeBtn.addEventListener('click', function () {
        current = 0;
        score = 0;
        results.classList.add('d-none');
        quizBox.classList.remove('d-none');
        renderQuestion();
    });

    renderQuestion();
})();
</script>
@endsection
