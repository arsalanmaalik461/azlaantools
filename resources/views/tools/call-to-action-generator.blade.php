@extends('layouts.app')

@section('title', 'Call to Action Generator - Azlaan Tools')
@section('meta_description', 'Generate powerful call to action phrases for buttons and banners to increase clicks. Free CTA generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Call to Action Generator</h1>
            <p class="lead text-muted">Make powerful CTA phrases for buttons and banners — get more clicks and sales.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="bizType" class="form-label fw-semibold">Business / page type</label>
                            <select class="form-select" id="bizType">
                                <option value="shop">Online shop / store</option>
                                <option value="service">Service business</option>
                                <option value="course">Course / coaching</option>
                                <option value="app">App / software</option>
                                <option value="blog">Blog / content</option>
                                <option value="event">Event / webinar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="goalSel" class="form-label fw-semibold">Goal (what should the visitor do?)</label>
                            <select class="form-select" id="goalSel">
                                <option value="buy">Buy now</option>
                                <option value="signup">Sign up / register</option>
                                <option value="contact">Contact us</option>
                                <option value="download">Download</option>
                                <option value="learn">Learn more</option>
                                <option value="book">Booking / appointment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="toneSel" class="form-label fw-semibold">Tone</label>
                            <select class="form-select" id="toneSel">
                                <option value="urgent">Urgent (act now)</option>
                                <option value="friendly">Friendly</option>
                                <option value="pro">Professional</option>
                                <option value="fun">Fun / playful</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="langSel" class="form-label fw-semibold">Language</label>
                            <select class="form-select" id="langSel">
                                <option value="en">English</option>
                                <option value="ur">Roman Urdu</option>
                                <option value="mix">Mix (English + Urdu)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="offerIn" class="form-label fw-semibold">Offer / product name (optional)</label>
                        <input type="text" class="form-control" id="offerIn" placeholder="e.g. Solar 5kW System, 20% Off">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate CTAs</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <h5 class="mb-3">Here are <span id="ctaCount"></span> CTA phrases for you:</h5>
                        <div id="ctaList"></div>
                        <button type="button" class="btn btn-outline-primary mt-3" id="copyAllBtn">Copy All</button>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your business type, goal and tone.</li>
                <li>Click "Generate CTAs" — you will get 12 ready-to-use phrases.</li>
                <li>Use the "Copy" button on a phrase you like, then paste it into your button or banner.</li>
            </ol>
            <p class="text-muted small">Tip: a short, clear CTA with one action always works better.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var bizType = document.getElementById('bizType');
    var goalSel = document.getElementById('goalSel');
    var toneSel = document.getElementById('toneSel');
    var langSel = document.getElementById('langSel');
    var offerIn = document.getElementById('offerIn');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var ctaCount = document.getElementById('ctaCount');
    var ctaList = document.getElementById('ctaList');
    var copyAllBtn = document.getElementById('copyAllBtn');

    var EN = {
        buy: { shop: ['Buy Now', 'Add to Cart', 'Grab Yours Today', 'Shop the Sale'], service: ['Book This Service', 'Get a Free Quote', 'Hire Us Today', 'Start Your Project'], course: ['Enroll Now', 'Claim Your Seat', 'Start Learning Today', 'Join the Course'], app: ['Get the App', 'Start Free Trial', 'Download Now', 'Try It Free'], blog: ['Read the Full Guide', 'Get the Free Ebook', 'Subscribe for More', 'Unlock Premium'], event: ['Reserve Your Seat', 'Get Tickets Now', 'Register Free', 'Save My Spot'] },
        signup: { d: ['Sign Up Free', 'Create Your Account', 'Join Now — It\'s Free', 'Get Started'] },
        contact: { d: ['Contact Us', 'Call Now', 'WhatsApp Us', 'Request a Callback'] },
        download: { d: ['Download Free', 'Get the PDF', 'Download the App', 'Get Instant Access'] },
        learn: { d: ['Learn More', 'See How It Works', 'Watch the Demo', 'Explore Features'] },
        book: { d: ['Book Appointment', 'Schedule a Call', 'Pick Your Slot', 'Book Your Visit'] }
    };
    var UR = {
        buy: ['Abhi Khariden', 'Apna Order Book Karen', 'Foran Mangwain', 'Offer Se Faida Uthain'],
        signup: ['Muft Sign Up Karen', 'Account Banain', 'Abhi Join Karen', 'Shuru Karen'],
        contact: ['Rabta Karen', 'Abhi Call Karen', 'WhatsApp Par Message Karen', 'Callback Mangwain'],
        download: ['Muft Download Karen', 'Abhi Hasil Karen', 'PDF Download Karen', 'Foran Access Pain'],
        learn: ['Mazeed Janen', 'Tafseel Dekhen', 'Demo Dekhen', 'Mukammal Guide Parhen'],
        book: ['Appointment Book Karen', 'Apni Slot Chunain', 'Visit Book Karen', 'Call Schedule Karen']
    };
    var TONE_EN = {
        urgent: ['Don\'t Miss Out — Act Now', 'Limited Time: Claim Yours', 'Offer Ends Soon — Grab It', 'Last Chance to Join'],
        friendly: ['Let\'s Get Started!', 'We\'d Love to Help — Say Hi', 'Come Join Us!', 'Give It a Try, You\'ll Love It'],
        pro: ['Request a Consultation', 'Get a Custom Quote', 'Talk to Our Team', 'Schedule a Demo'],
        fun: ['Let\'s Do This!', 'Hop On Board!', 'Your Adventure Starts Here', 'Click It, Love It!']
    };
    var TONE_UR = {
        urgent: ['Jaldi Karen — Offer Khatam Hone Wali Hai', 'Aaj Hi Faida Uthain', 'Waqt Zaya Na Karen', 'Limited Stock — Abhi Order Karen'],
        friendly: ['Aaiye, Baat Karte Hain!', 'Hum Aap Ki Khidmat Ke Liye Hazir Hain', 'Sath Chalen!', 'Zaroor Try Karen'],
        pro: ['Mashwara Hasil Karen', 'Tafseeli Quote Mangwain', 'Hamari Team Se Baat Karen', 'Demo Book Karen'],
        fun: ['Chalen Shuru Karte Hain!', 'Mazay Shuru!', 'Abhi Click Karen!', 'Dekhen Aur Hairan Hon!']
    };

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }
    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

    function pick(arr, n) {
        var c = arr.slice(), out = [];
        while (out.length < n && c.length) { out.push(c.splice(Math.floor(Math.random() * c.length), 1)[0]); }
        return out;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var biz = bizType.value, goal = goalSel.value, tone = toneSel.value, lang = langSel.value;
        var offer = offerIn.value.trim();
        var list = [];

        function enBase() {
            var g = EN[goal];
            if (g.d) return g.d.slice();
            return (g[biz] || g.shop).slice();
        }

        if (lang === 'en' || lang === 'mix') {
            var base = pick(enBase(), 4);
            var tEn = pick(TONE_EN[tone], 4);
            list = list.concat(base, tEn);
            if (offer) {
                list.push('Get ' + offer + ' Now');
                list.push('Claim Your ' + offer);
            }
        }
        if (lang === 'ur' || lang === 'mix') {
            var baseU = pick(UR[goal], 4);
            var tUr = pick(TONE_UR[tone], 4);
            list = list.concat(baseU, tUr);
            if (offer && lang === 'ur') {
                list.push(offer + ' Abhi Hasil Karen');
                list.push(offer + ' Ke Liye Rabta Karen');
            }
        }

        list = list.slice(0, 12);
        if (!list.length) { showError('No phrase could be generated. Please try again.'); return; }

        ctaCount.textContent = list.length;
        var html = '';
        list.forEach(function (cta, i) {
            html += '<div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light">';
            html += '<span class="fw-semibold">' + esc(cta) + '</span>';
            html += '<button type="button" class="btn btn-sm btn-outline-primary copyOne" data-i="' + i + '">Copy</button>';
            html += '</div>';
        });
        ctaList.innerHTML = html;

        var btns = ctaList.querySelectorAll('.copyOne');
        btns.forEach(function (b) {
            b.addEventListener('click', function () {
                var txt = list[parseInt(this.getAttribute('data-i'), 10)];
                var self = this;
                function done() { self.textContent = 'Copied!'; setTimeout(function () { self.textContent = 'Copy'; }, 1200); }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(txt).then(done, function () {});
                }
            });
        });

        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    copyAllBtn.addEventListener('click', function () {
        var items = ctaList.querySelectorAll('.fw-semibold');
        var txt = [];
        items.forEach(function (el) { txt.push(el.textContent); });
        if (!txt.length) return;
        var self = copyAllBtn;
        function done() { self.textContent = 'Copied!'; setTimeout(function () { self.textContent = 'Copy All'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(txt.join('\n')).then(done, function () {});
        }
    });
})();
</script>
@endsection
