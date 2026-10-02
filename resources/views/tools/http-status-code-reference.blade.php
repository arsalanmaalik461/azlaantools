@extends('layouts.app')

@section('title', 'HTTP Status Code Reference - Azlaan Tools')
@section('meta_description', 'Searchable reference of every HTTP status code with plain meanings and fix tips. Free online for developers.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">HTTP Status Code Reference</h1>
            <p class="lead text-muted">Meaning and fix tip for every HTTP status code — from 200 to 511. Search or browse by category.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="searchInput" class="form-label fw-semibold">Search (code or name, e.g. "404" or "timeout")</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search status codes...">
                    </div>
                    <div class="mb-3" id="catBtns" role="group" aria-label="Category filter">
                        <button type="button" class="btn btn-sm btn-primary me-1 mb-1 cat-btn" data-cat="all">All</button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 cat-btn" data-cat="1xx">1xx Info</button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 cat-btn" data-cat="2xx">2xx Success</button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 cat-btn" data-cat="3xx">3xx Redirect</button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 cat-btn" data-cat="4xx">4xx Client Error</button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1 cat-btn" data-cat="5xx">5xx Server Error</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div id="results" class="mt-2">
                        <div id="codeList" class="list-group"></div>
                        <p class="text-muted small mt-3 mb-0 d-none" id="noResult">No status code found. Try a different search.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type a code (e.g. 500) or a word (e.g. "forbidden") in the search box above.</li>
                <li>Or filter by category button: 2xx, 4xx, etc.</li>
                <li>Click any code — the meaning and fix tip will open.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var searchInput = document.getElementById('searchInput');
    var codeList = document.getElementById('codeList');
    var noResult = document.getElementById('noResult');
    var errorBox = document.getElementById('errorBox');
    var catBtns = document.querySelectorAll('.cat-btn');

    var codes = [
        { c: 100, n: 'Continue', cat: '1xx', m: 'The server accepted the first part of the request; keep sending the rest.', t: 'Usually handled automatically; nothing to do.' },
        { c: 101, n: 'Switching Protocols', cat: '1xx', m: 'The server is switching protocols (e.g. HTTP to WebSocket).', t: 'Normal during a WebSocket upgrade.' },
        { c: 103, n: 'Early Hints', cat: '1xx', m: 'The server is hinting which files (CSS/JS) to load early.', t: 'A performance feature, not an error.' },
        { c: 200, n: 'OK', cat: '2xx', m: 'All good — the request succeeded and got a response.', t: 'No fix needed. The most common success code.' },
        { c: 201, n: 'Created', cat: '2xx', m: 'Something new was created (e.g. a new user or order).', t: 'Check the API response for the new resource URL.' },
        { c: 204, n: 'No Content', cat: '2xx', m: 'Success, but no data to send back.', t: 'Normal after a DELETE request.' },
        { c: 206, n: 'Partial Content', cat: '2xx', m: 'Only the requested part of the file was sent.', t: 'Normal in video streaming and download resume.' },
        { c: 301, n: 'Moved Permanently', cat: '3xx', m: 'The page moved permanently to a new address.', t: 'Update old links; use a 301 redirect for SEO.' },
        { c: 302, n: 'Found', cat: '3xx', m: 'Being sent temporarily to another address.', t: 'Common after login. Use 301 for a permanent move.' },
        { c: 303, n: 'See Other', cat: '3xx', m: 'See the answer at another URL (after form submit).', t: 'Used to stop a form from being submitted twice.' },
        { c: 304, n: 'Not Modified', cat: '3xx', m: 'The file did not change — use the old copy in the browser.', t: 'Caching is working; not an error.' },
        { c: 307, n: 'Temporary Redirect', cat: '3xx', m: 'Temporary redirect; the request method (POST/GET) stays the same.', t: 'Like 302, but the method does not change.' },
        { c: 308, n: 'Permanent Redirect', cat: '3xx', m: 'Permanent redirect; the request method stays the same.', t: 'Like 301, but the method does not change.' },
        { c: 400, n: 'Bad Request', cat: '4xx', m: 'The request is badly formed — the server could not understand it.', t: 'Check the URL, form fields and JSON format.' },
        { c: 401, n: 'Unauthorized', cat: '4xx', m: 'Login is required, or the login session expired.', t: 'Log in again; check the API key or token.' },
        { c: 402, n: 'Payment Required', cat: '4xx', m: 'Payment is required (some APIs use this when the quota ends).', t: 'Check billing or plan upgrade.' },
        { c: 403, n: 'Forbidden', cat: '4xx', m: 'You are logged in but do not have access to this page.', t: 'Check permissions; see file permissions (644/755 on Linux).' },
        { c: 404, n: 'Not Found', cat: '4xx', m: 'The page or file does not exist.', t: 'Check the URL for typos; if you own the site, check the file path.' },
        { c: 405, n: 'Method Not Allowed', cat: '4xx', m: 'This method (GET/POST) is not allowed on this URL.', t: 'See the correct method in the API docs.' },
        { c: 406, n: 'Not Acceptable', cat: '4xx', m: 'The server cannot give the format that was asked for.', t: 'Check the Accept header or format parameter.' },
        { c: 408, n: 'Request Timeout', cat: '4xx', m: 'Sending the request took too long.', t: 'Check your internet and try again.' },
        { c: 409, n: 'Conflict', cat: '4xx', m: 'The data conflicts (e.g. a duplicate email).', t: 'Check the existing record first, then update.' },
        { c: 410, n: 'Gone', cat: '4xx', m: 'The page was removed permanently.', t: 'Stronger than 404 — search engines remove it from the index.' },
        { c: 413, n: 'Content Too Large', cat: '4xx', m: 'The sent file or data is too large.', t: 'Make the file smaller or raise the server upload limit.' },
        { c: 415, n: 'Unsupported Media Type', cat: '4xx', m: 'The file format is not accepted.', t: 'Use the correct Content-Type header or a supported format.' },
        { c: 422, n: 'Unprocessable Content', cat: '4xx', m: 'The format is fine but the data has an error (e.g. a wrong email).', t: 'Read the validation errors and fix the fields.' },
        { c: 425, n: 'Too Early', cat: '4xx', m: 'The request was sent too early (protection against replay attacks).', t: 'Usually an automatic retry fixes it.' },
        { c: 426, n: 'Upgrade Required', cat: '4xx', m: 'The server wants you to upgrade the protocol.', t: 'Switch to HTTPS or a newer protocol.' },
        { c: 429, n: 'Too Many Requests', cat: '4xx', m: 'Too many requests — rate limit hit.', t: 'Wait a little and try again; slow down your requests.' },
        { c: 431, n: 'Request Header Fields Too Large', cat: '4xx', m: 'Cookies or headers got too large.', t: 'Clear your cookies.' },
        { c: 451, n: 'Unavailable For Legal Reasons', cat: '4xx', m: 'The content is blocked for legal reasons.', t: 'May be a VPN or region issue; check local laws.' },
        { c: 500, n: 'Internal Server Error', cat: '5xx', m: 'An internal problem on the server.', t: 'If you own the site, see the error logs; if you are a visitor, try again later.' },
        { c: 501, n: 'Not Implemented', cat: '5xx', m: 'The server does not support this feature.', t: 'Check the API version or endpoint.' },
        { c: 502, n: 'Bad Gateway', cat: '5xx', m: 'The server behind the server is not answering.', t: 'Try again later; often a temporary hosting issue.' },
        { c: 503, n: 'Service Unavailable', cat: '5xx', m: 'The server is down or very busy (maintenance/overload).', t: 'Wait a while; if it happens often, think about a hosting upgrade.' },
        { c: 504, n: 'Gateway Timeout', cat: '5xx', m: 'The server behind could not answer in time.', t: 'The server is slow or the database is stuck — check the logs.' },
        { c: 505, n: 'HTTP Version Not Supported', cat: '5xx', m: 'The server does not support this HTTP version.', t: 'Very old client or proxy — update it.' },
        { c: 507, n: 'Insufficient Storage', cat: '5xx', m: 'The server ran out of space.', t: 'Free disk space or upgrade the plan.' },
        { c: 508, n: 'Loop Detected', cat: '5xx', m: 'The request looped back to the same place (redirect loop).', t: 'Check redirect rules — not A to B and B to A.' },
        { c: 511, n: 'Network Authentication Required', cat: '5xx', m: 'You must log in on the network (e.g. hotel WiFi).', t: 'Log in on the captive portal.' }
    ];

    var activeCat = 'all';

    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function catLabel(cat) {
        return { '1xx': 'Informational', '2xx': 'Success', '3xx': 'Redirection', '4xx': 'Client Error', '5xx': 'Server Error' }[cat] || cat;
    }

    function catBadge(cat) {
        var cls = { '1xx': 'bg-info', '2xx': 'bg-success', '3xx': 'bg-primary', '4xx': 'bg-warning text-dark', '5xx': 'bg-danger' }[cat] || 'bg-secondary';
        return '<span class="badge ' + cls + ' me-2">' + cat + '</span>';
    }

    function render() {
        hideError();
        var q = searchInput.value.trim().toLowerCase();
        codeList.innerHTML = '';
        var shown = 0;
        codes.forEach(function (item) {
            if (activeCat !== 'all' && item.cat !== activeCat) return;
            var hay = (item.c + ' ' + item.n + ' ' + item.m + ' ' + item.t).toLowerCase();
            if (q && hay.indexOf(q) === -1) return;
            shown++;

            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            a.innerHTML = '<div class="d-flex align-items-center">' + catBadge(item.cat) +
                '<strong class="me-2">' + item.c + '</strong><span>' + item.n + '</span></div>' +
                '<div class="detail d-none mt-2 ps-1"><p class="mb-1 small">' + item.m + '</p>' +
                '<p class="mb-0 small"><strong>Fix tip:</strong> ' + item.t + '</p></div>';
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var d = a.querySelector('.detail');
                d.classList.toggle('d-none');
            });
            codeList.appendChild(a);
        });
        noResult.classList.toggle('d-none', shown > 0);
    }

    searchInput.addEventListener('input', render);

    for (var i = 0; i < catBtns.length; i++) {
        catBtns[i].addEventListener('click', function () {
            activeCat = this.getAttribute('data-cat');
            for (var j = 0; j < catBtns.length; j++) {
                catBtns[j].classList.remove('btn-primary');
                catBtns[j].classList.add('btn-outline-primary');
            }
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            render();
        });
    }

    render();
})();
</script>
@endsection
