@extends('layouts.app')

@section('title', 'JSON-LD Generator - Azlaan Tools')
@section('meta_description', 'Generate JSON-LD structured data for FAQ, Article, Product, LocalBusiness and Event — free online schema generator.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">JSON-LD Generator</h1>
            <p class="lead text-muted">Create schema.org structured data for your page — for Google rich results. Select a type, fill the fields, and copy the code.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="schemaType" class="form-label fw-semibold">Schema type</label>
                        <select class="form-select" id="schemaType">
                            <option value="faq">FAQ Page</option>
                            <option value="article">Article</option>
                            <option value="product">Product</option>
                            <option value="localbusiness">Local Business</option>
                            <option value="event">Event</option>
                        </select>
                    </div>

                    <div id="fieldsArea"></div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Generate JSON-LD</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Generated code</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">Copy</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="dlBtn">Download .json</button>
                            </div>
                        </div>
                        <pre class="border rounded p-3 bg-light" style="max-height:380px;overflow:auto;"><code id="codeOut"></code></pre>
                        <div class="form-text">Paste it inside the <code>&lt;head&gt;</code> of your page, in a <code>&lt;script type="application/ld+json"&gt;</code> tag.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select a schema type (FAQ, Article, Product...).</li>
                <li>Fill the fields — for FAQ, add question/answer rows.</li>
                <li>Click <strong>Generate JSON-LD</strong> and copy the code into your site.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var schemaType = document.getElementById('schemaType');
    var fieldsArea = document.getElementById('fieldsArea');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');

    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('d-none'); results.classList.add('d-none'); }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    var COMMON = [
        { id: 'f_name', label: 'Name / Title', req: true },
        { id: 'f_desc', label: 'Description', req: false, textarea: true }
    ];
    var EXTRA = {
        article: [
            { id: 'f_author', label: 'Author name', req: true },
            { id: 'f_date', label: 'Publish date (YYYY-MM-DD)', req: true },
            { id: 'f_image', label: 'Image URL', req: false },
            { id: 'f_publisher', label: 'Publisher name', req: false }
        ],
        product: [
            { id: 'f_brand', label: 'Brand', req: false },
            { id: 'f_price', label: 'Price', req: true },
            { id: 'f_currency', label: 'Currency (e.g. PKR)', req: true },
            { id: 'f_avail', label: 'Availability', req: false, select: ['InStock', 'OutOfStock', 'PreOrder'] },
            { id: 'f_rating', label: 'Rating (0-5)', req: false },
            { id: 'f_reviews', label: 'Review count', req: false },
            { id: 'f_image', label: 'Image URL', req: false }
        ],
        localbusiness: [
            { id: 'f_phone', label: 'Phone', req: true },
            { id: 'f_address', label: 'Address', req: true },
            { id: 'f_hours', label: 'Opening hours (e.g. Mo-Fr 09:00-18:00)', req: false },
            { id: 'f_pricerange', label: 'Price range (e.g. Rs 500-5000)', req: false }
        ],
        event: [
            { id: 'f_start', label: 'Start date/time (YYYY-MM-DDTHH:MM)', req: true },
            { id: 'f_end', label: 'End date/time', req: false },
            { id: 'f_location', label: 'Location / venue', req: true },
            { id: 'f_image', label: 'Image URL', req: false }
        ]
    };

    function fieldHtml(f) {
        var req = f.req ? ' <span class="text-danger">*</span>' : '';
        var html = '<div class="mb-3"><label for="' + f.id + '" class="form-label fw-semibold">' + f.label + req + '</label>';
        if (f.select) {
            html += '<select class="form-select" id="' + f.id + '">';
            f.select.forEach(function (o) { html += '<option>' + o + '</option>'; });
            html += '</select>';
        } else if (f.textarea) {
            html += '<textarea class="form-control" id="' + f.id + '" rows="2"></textarea>';
        } else {
            var val = f.id === 'f_currency' ? ' value="PKR"' : '';
            html += '<input type="text" class="form-control" id="' + f.id + '"' + val + '>';
        }
        return html + '</div>';
    }

    function val(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }

    function renderFields() {
        hideError();
        var t = schemaType.value;
        var html = '';
        if (t === 'faq') {
            html += '<div id="faqRows"></div>' +
                '<button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="addFaq">+ Add question/answer</button>';
        } else {
            COMMON.forEach(function (f) { html += fieldHtml(f); });
            (EXTRA[t] || []).forEach(function (f) { html += fieldHtml(f); });
        }
        fieldsArea.innerHTML = html;
        if (t === 'faq') {
            document.getElementById('addFaq').addEventListener('click', addFaqRow);
            addFaqRow(); addFaqRow();
        }
    }

    function addFaqRow() {
        var wrap = document.getElementById('faqRows');
        var div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2 faq-row';
        div.innerHTML = '<input type="text" class="form-control mb-2 faq-q" placeholder="Question">' +
            '<textarea class="form-control faq-a" rows="2" placeholder="Answer"></textarea>' +
            '<button type="button" class="btn btn-sm btn-link text-danger faq-del">Remove</button>';
        div.querySelector('.faq-del').addEventListener('click', function () { div.remove(); });
        wrap.appendChild(div);
    }

    function buildSchema() {
        var t = schemaType.value;
        if (t === 'faq') {
            var items = [];
            Array.prototype.forEach.call(document.querySelectorAll('.faq-row'), function (row) {
                var q = row.querySelector('.faq-q').value.trim();
                var a = row.querySelector('.faq-a').value.trim();
                if (q && a) items.push({ '@type': 'Question', name: q, acceptedAnswer: { '@type': 'Answer', text: a } });
            });
            if (!items.length) { showError('Please write at least one question and answer.'); return null; }
            return { '@context': 'https://schema.org', '@type': 'FAQPage', mainEntity: items };
        }
        var name = val('f_name');
        if (!name) { showError('Name / Title is required.'); return null; }
        var desc = val('f_desc');
        var s = { '@context': 'https://schema.org', name: name };
        if (desc) s.description = desc;
        if (t === 'article') {
            s['@type'] = 'Article';
            var author = val('f_author'), date = val('f_date');
            if (!author || !date) { showError('Author and publish date are required.'); return null; }
            s.headline = name; delete s.name;
            s.author = { '@type': 'Person', name: author };
            s.datePublished = date;
            if (val('f_image')) s.image = val('f_image');
            if (val('f_publisher')) s.publisher = { '@type': 'Organization', name: val('f_publisher') };
        } else if (t === 'product') {
            s['@type'] = 'Product';
            var price = val('f_price'), cur = val('f_currency');
            if (!price || !cur) { showError('Price and currency are required.'); return null; }
            s.offers = { '@type': 'Offer', price: price, priceCurrency: cur, availability: 'https://schema.org/' + val('f_avail') };
            if (val('f_brand')) s.brand = { '@type': 'Brand', name: val('f_brand') };
            if (val('f_rating') && val('f_reviews')) s.aggregateRating = { '@type': 'AggregateRating', ratingValue: val('f_rating'), reviewCount: val('f_reviews') };
            if (val('f_image')) s.image = val('f_image');
        } else if (t === 'localbusiness') {
            s['@type'] = 'LocalBusiness';
            var phone = val('f_phone'), addr = val('f_address');
            if (!phone || !addr) { showError('Phone and address are required.'); return null; }
            s.telephone = phone; s.address = addr;
            if (val('f_hours')) s.openingHours = val('f_hours');
            if (val('f_pricerange')) s.priceRange = val('f_pricerange');
        } else if (t === 'event') {
            s['@type'] = 'Event';
            var start = val('f_start'), loc = val('f_location');
            if (!start || !loc) { showError('Start date/time and location are required.'); return null; }
            s.startDate = start;
            if (val('f_end')) s.endDate = val('f_end');
            s.location = { '@type': 'Place', name: loc };
            if (val('f_image')) s.image = val('f_image');
        }
        return s;
    }

    schemaType.addEventListener('change', renderFields);
    renderFields();

    goBtn.addEventListener('click', function () {
        hideError();
        var s = buildSchema();
        if (!s) return;
        var json = JSON.stringify(s, null, 2);
        document.getElementById('codeOut').textContent = json;
        results.classList.remove('d-none');
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        var t = document.getElementById('codeOut').textContent;
        if (!t) { showError('Please generate first.'); return; }
        navigator.clipboard.writeText(t).then(function () {
            document.getElementById('copyBtn').textContent = 'Copied!';
            setTimeout(function () { document.getElementById('copyBtn').textContent = 'Copy'; }, 1500);
        }, function () { showError('Could not copy.'); });
    });

    document.getElementById('dlBtn').addEventListener('click', function () {
        var t = document.getElementById('codeOut').textContent;
        if (!t) { showError('Please generate first.'); return; }
        var blob = new Blob([t], { type: 'application/ld+json;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'schema.jsonld';
        document.body.appendChild(a); a.click(); a.remove();
    });
})();
</script>
@endsection
