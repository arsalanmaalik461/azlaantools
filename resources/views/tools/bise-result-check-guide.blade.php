@extends('layouts.app')

@section('title', 'BISE Result Check Guide - Azlaan Tools')
@section('meta_description', 'Step by step guide to check Matric and Inter results on BISE board official websites and by SMS. Free guide.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">BISE Result Check Guide</h1>
            <p class="lead text-muted">How to check your Matric and Inter result on your board's <strong>official website</strong> or by <strong>SMS</strong>. Select your board below — you will get its website link and SMS code.</p>

            <div class="alert alert-warning small" role="alert">
                This is only a <strong>guide</strong> — this tool does not check results or verify any board's live data. Always check your result on your board's official website.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="boardSelect" class="form-label fw-semibold">Select your board</label>
                            <select class="form-select" id="boardSelect"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="boardSearch" class="form-label fw-semibold">Or search for your board</label>
                            <input type="text" class="form-control" id="boardSearch" placeholder="For example: Lahore, Karachi...">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="goBtn">Show Board Details</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4"></div>
                </div>
            </div>

            <h2>How to check your result on the website</h2>
            <ol>
                <li>Open your board's official website (from the link given below).</li>
                <li>Click the <strong>Result</strong> or <strong>Results Online</strong> section.</li>
                <li>Enter your <strong>Roll Number</strong> and select the exam (Matric 9th/10th or Inter 11th/12th).</li>
                <li>Press <strong>Search / View Result</strong> — your result will appear on screen.</li>
                <li>Be sure to take a screenshot or print.</li>
            </ol>

            <h2>How to check your result by SMS</h2>
            <ol>
                <li>Write a new SMS from your mobile and type only your <strong>Roll Number</strong> in it.</li>
                <li>Send it to your board's short code (for example Lahore board: <strong>80029</strong>).</li>
                <li>You will get the result SMS in a few minutes. Normal mobile charges apply to each SMS.</li>
            </ol>

            <h2>Important notes</h2>
            <ul>
                <li>Websites are busy on result day — wait a little and try again.</li>
                <li>A wrong roll number will show "not found" — copy it from your admit card.</li>
                <li>For rechecking / supplementary also use the <strong>Online Services</strong> section of the board website.</li>
            </ul>
            <p class="text-muted small">Board SMS short codes can change over time — confirm on your board's website before sending.</p>
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
    var boardSelect = document.getElementById('boardSelect');
    var boardSearch = document.getElementById('boardSearch');

    // Official BISE board portals (real domains) + known SMS codes.
    var boards = [
        { name: 'BISE Lahore (Punjab)', site: 'https://www.biselahore.com', sms: '80029' },
        { name: 'BISE Gujranwala (Punjab)', site: 'https://www.bisegrw.edu.pk', sms: '800299' },
        { name: 'BISE Multan (Punjab)', site: 'https://web.bisemultan.edu.pk', sms: '800293' },
        { name: 'BISE Faisalabad (Punjab)', site: 'https://www.bisefsd.edu.pk', sms: 'See the board website' },
        { name: 'BISE Rawalpindi (Punjab)', site: 'https://www.biserwp.edu.pk', sms: 'See the board website' },
        { name: 'BISE Sargodha (Punjab)', site: 'https://www.bisesargodha.edu.pk', sms: 'See the board website' },
        { name: 'BISE Dera Ghazi Khan (Punjab)', site: 'https://www.bisedgkhan.edu.pk', sms: 'See the board website' },
        { name: 'BISE Bahawalpur (Punjab)', site: 'https://www.bisebwp.edu.pk', sms: 'See the board website' },
        { name: 'BISE Sahiwal (Punjab)', site: 'https://www.bisesahiwal.edu.pk', sms: 'See the board website' },
        { name: 'BISE Karachi - Inter (Sindh)', site: 'https://www.biek.edu.pk', sms: 'See the board website' },
        { name: 'BSEK Karachi - Matric (Sindh)', site: 'https://www.bsek.edu.pk', sms: 'See the board website' },
        { name: 'BISE Hyderabad (Sindh)', site: 'https://www.biseh.edu.pk', sms: 'See the board website' },
        { name: 'BISE Sukkur (Sindh)', site: 'https://www.bisesuks.edu.pk', sms: 'See the board website' },
        { name: 'BISE Larkana (Sindh)', site: 'https://www.biselrk.edu.pk', sms: 'See the board website' },
        { name: 'BISE Mirpurkhas (Sindh)', site: 'https://www.bisemirpurkhas.edu.pk', sms: 'See the board website' },
        { name: 'BISE Peshawar (KPK)', site: 'https://www.bisep.edu.pk', sms: 'See the board website' },
        { name: 'BISE Mardan (KPK)', site: 'https://www.bisemardan.edu.pk', sms: 'See the board website' },
        { name: 'BISE Swat (KPK)', site: 'https://www.bisess.edu.pk', sms: 'See the board website' },
        { name: 'BISE Abbottabad (KPK)', site: 'https://www.biseatd.edu.pk', sms: 'See the board website' },
        { name: 'BISE Kohat (KPK)', site: 'https://www.bisekohat.edu.pk', sms: 'See the board website' },
        { name: 'BISE Bannu (KPK)', site: 'https://www.biseb.edu.pk', sms: 'See the board website' },
        { name: 'BISE Dera Ismail Khan (KPK)', site: 'https://www.bisedik.edu.pk', sms: 'See the board website' },
        { name: 'BISE Malakand (KPK)', site: 'https://www.bisemalakand.edu.pk', sms: 'See the board website' },
        { name: 'BISE Quetta (Balochistan)', site: 'https://www.bbiseqta.edu.pk', sms: 'See the board website' },
        { name: 'FBISE Islamabad (Federal)', site: 'https://www.fbise.edu.pk', sms: '5050' },
        { name: 'BISE Mirpur AJK', site: 'https://www.ajkbise.net', sms: 'See the board website' },
        { name: 'BISE Gilgit-Baltistan', site: 'https://www.bisegb.edu.pk', sms: 'See the board website' }
    ];

    function fillSelect(filter) {
        boardSelect.innerHTML = '';
        var q = (filter || '').toLowerCase();
        boards.forEach(function (b) {
            if (q && b.name.toLowerCase().indexOf(q) === -1) { return; }
            var opt = document.createElement('option');
            opt.value = b.name;
            opt.textContent = b.name;
            boardSelect.appendChild(opt);
        });
        if (!boardSelect.options.length) {
            var opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'No board found — clear the search and try again';
            boardSelect.appendChild(opt);
        }
    }

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

    fillSelect('');

    boardSearch.addEventListener('input', function () {
        fillSelect(boardSearch.value);
    });

    goBtn.addEventListener('click', function () {
        hideError();
        var name = boardSelect.value;
        if (!name) { showError('Please select your board.'); return; }
        var b = null;
        for (var i = 0; i < boards.length; i++) {
            if (boards[i].name === name) { b = boards[i]; break; }
        }
        if (!b) { showError('Board not found. Please select again.'); return; }

        var html = '<div class="card"><div class="card-body">';
        html += '<h5 class="card-title">' + esc(b.name) + '</h5>';
        html += '<p class="mb-2"><strong>Official website:</strong> <a href="' + esc(b.site) + '" target="_blank" rel="noopener">' + esc(b.site) + '</a></p>';
        html += '<p class="mb-2"><strong>SMS code:</strong> ';
        if (/^[0-9]+$/.test(b.sms)) {
            html += 'Type your <strong>Roll Number</strong> and SMS it to <strong>' + esc(b.sms) + '</strong>';
        } else {
            html += esc(b.sms);
        }
        html += '</p>';
        html += '<p class="mb-0 small text-muted">Tip: on the website, enter your roll number in the Result section and select the class (9th / 10th / 11th / 12th).</p>';
        html += '</div></div>';
        results.innerHTML = html;
        results.classList.remove('d-none');
    });
})();
</script>
@endsection
