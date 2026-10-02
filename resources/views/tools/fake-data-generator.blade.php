@extends('layouts.app')

@section('title', 'Fake Data Generator - Azlaan Tools')
@section('meta_description', 'Generate dummy names, emails, phones, addresses and test data for developers — free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fake Data Generator</h1>
            <p class="lead text-muted">Generate <strong>dummy data</strong> for testing — names, emails, phones, addresses, companies, UUIDs, passwords and much more. All of it is fake and has no link to any real person.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="dataType" class="form-label fw-semibold">Which type of data do you need?</label>
                            <select class="form-select" id="dataType">
                                <option value="person">Full Person (name, email, phone, address)</option>
                                <option value="name">Name only</option>
                                <option value="email">Email only</option>
                                <option value="phone">Phone (Pakistani format)</option>
                                <option value="address">Address</option>
                                <option value="company">Company</option>
                                <option value="username">Username</option>
                                <option value="password">Strong Password</option>
                                <option value="uuid">UUID</option>
                                <option value="ipv4">IPv4 Address</option>
                                <option value="card">Test Card Number (Luhn valid)</option>
                                <option value="cnic">Test CNIC Format</option>
                                <option value="dob">Date of Birth</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="rowCount" class="form-label fw-semibold">How many rows? (1-100)</label>
                            <input type="number" class="form-control" id="rowCount" value="5" min="1" max="100">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary flex-fill" id="goBtn">Generate</button>
                        <button type="button" class="btn btn-outline-secondary" id="copyBtn">Copy</button>
                        <button type="button" class="btn btn-outline-success" id="csvBtn">CSV</button>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="table-light"><tr id="headRow"></tr></thead>
                                <tbody id="bodyRows"></tbody>
                            </table>
                        </div>
                        <div class="form-text mt-2">Warning: cards and CNICs are only for <strong>format testing</strong> — they are not real numbers, so do not use them as real anywhere.</div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select the type of data (for example Full Person or Email).</li>
                <li>Enter how many rows you need and press <strong>Generate</strong>.</li>
                <li>Use <strong>Copy</strong> to copy the text or <strong>CSV</strong> to download the file.</li>
            </ol>
            <h2>What is it for?</h2>
            <p>When developers test an app or website, they need sample data — for form testing, database seeding, or design mockups. This tool gives ready dummy data instantly.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var dataType = document.getElementById('dataType');
    var rowCount = document.getElementById('rowCount');
    var goBtn = document.getElementById('goBtn');
    var copyBtn = document.getElementById('copyBtn');
    var csvBtn = document.getElementById('csvBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var headRow = document.getElementById('headRow');
    var bodyRows = document.getElementById('bodyRows');

    var male = ['Ahmed', 'Ali', 'Bilal', 'Danish', 'Fahad', 'Hamza', 'Imran', 'Junaid', 'Kamran', 'Noman', 'Rashid', 'Sajid', 'Tariq', 'Usman', 'Waqas', 'Zubair', 'Adnan', 'Farhan', 'Hassan', 'Kashif'];
    var female = ['Ayesha', 'Fatima', 'Hira', 'Iqra', 'Mahnoor', 'Maryam', 'Nadia', 'Rabia', 'Sana', 'Zainab', 'Areeba', 'Bushra', 'Hina', 'Kiran', 'Sadia'];
    var last = ['Khan', 'Malik', 'Ahmed', 'Hussain', 'Raza', 'Sheikh', 'Butt', 'Chaudhry', 'Rana', 'Mahmood', 'Aslam', 'Iqbal', 'Farooq', 'Nadeem', 'Akram', 'Yousuf', 'Shah', 'Qureshi', 'Siddiqui', 'Mirza'];
    var domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'example.pk'];
    var cities = ['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala'];
    var areas = ['Gulberg', 'DHA Phase 5', 'Model Town', 'Satellite Town', 'Cantt', 'Johar Town', 'Bahria Town', 'F-10', 'Clifton', 'North Nazimabad'];
    var companies = ['Tech Solutions', 'Digital Marketing Co', 'General Traders', 'Foods Ltd', 'Textile Mills', 'Builders', 'Logistics', 'Consultants', 'Electronics', 'Pharma Ltd'];
    var jobs = ['Manager', 'Developer', 'Accountant', 'Teacher', 'Engineer', 'Designer', 'Sales Executive', 'Driver', 'Clerk', 'Technician'];

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
    function rint(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }
    function pad(n, len) { var s = String(n); while (s.length < len) { s = '0' + s; } return s; }

    function makeName() {
        var first = Math.random() < 0.5 ? pick(male) : pick(female);
        return { first: first, last: pick(last), full: first + ' ' + pick(last) };
    }
    function makeEmail(n) {
        return (n.first + '.' + n.last + rint(1, 99)).toLowerCase().replace(/ /g, '') + '@' + pick(domains);
    }
    function makePhone() {
        return '03' + pad(rint(0, 49), 2) + '-' + pad(rint(0, 9999999), 7);
    }
    function makeAddress() {
        return 'House ' + rint(1, 500) + ', Street ' + rint(1, 40) + ', ' + pick(areas) + ', ' + pick(cities);
    }
    function makeUuid() {
        var h = '0123456789abcdef';
        var s = '';
        for (var i = 0; i < 36; i++) {
            if (i === 8 || i === 13 || i === 18 || i === 23) { s += '-'; }
            else if (i === 14) { s += '4'; }
            else if (i === 19) { s += h[8 + rint(0, 3)]; }
            else { s += h[rint(0, 15)]; }
        }
        return s;
    }
    function makePassword() {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
        var p = '';
        for (var i = 0; i < 14; i++) { p += chars[rint(0, chars.length - 1)]; }
        return p;
    }
    function makeCard() {
        var d = '4';
        for (var i = 0; i < 14; i++) { d += rint(0, 9); }
        var sum = 0;
        for (var j = 0; j < 15; j++) {
            var dig = parseInt(d.charAt(14 - j), 10);
            if (j % 2 === 0) { dig = dig * 2; if (dig > 9) { dig -= 9; } }
            sum += dig;
        }
        var check = (10 - (sum % 10)) % 10;
        var num = d + check;
        return num.replace(/(.{4})/g, '$1 ').trim() + ' (TEST)';
    }
    function makeCnic() {
        return pad(rint(10000, 45999), 5) + '-' + pad(rint(1000000, 9999999), 7) + '-' + rint(0, 9) + ' (TEST)';
    }
    function makeDob() {
        return pad(rint(1, 28), 2) + '-' + pad(rint(1, 12), 2) + '-' + rint(1970, 2005);
    }
    function makeIpv4() {
        return rint(11, 223) + '.' + rint(0, 255) + '.' + rint(0, 255) + '.' + rint(1, 254);
    }

    function rowFor(type) {
        var n = makeName();
        switch (type) {
            case 'person': return { cols: ['Name', 'Email', 'Phone', 'Address'], vals: [n.full, makeEmail(n), makePhone(), makeAddress()] };
            case 'name': return { cols: ['Name'], vals: [n.full] };
            case 'email': return { cols: ['Email'], vals: [makeEmail(n)] };
            case 'phone': return { cols: ['Phone'], vals: [makePhone()] };
            case 'address': return { cols: ['Address'], vals: [makeAddress()] };
            case 'company': return { cols: ['Company', 'Contact', 'Email'], vals: [pick(companies) + ' (Pvt) Ltd', makePhone(), 'info@' + pick(domains)] };
            case 'username': return { cols: ['Username'], vals: [(n.first + n.last + rint(10, 99)).toLowerCase()] };
            case 'password': return { cols: ['Password'], vals: [makePassword()] };
            case 'uuid': return { cols: ['UUID'], vals: [makeUuid()] };
            case 'ipv4': return { cols: ['IPv4'], vals: [makeIpv4()] };
            case 'card': return { cols: ['Card Number'], vals: [makeCard()] };
            case 'cnic': return { cols: ['CNIC'], vals: [makeCnic()] };
            case 'dob': return { cols: ['Date of Birth'], vals: [makeDob()] };
            default: return { cols: ['Value'], vals: [n.full] };
        }
    }

    var lastData = null;

    goBtn.addEventListener('click', function () {
        hideError();
        var count = parseInt(rowCount.value, 10);
        if (isNaN(count) || count < 1 || count > 100) { showError('Rows must be between 1 and 100.'); return; }
        var type = dataType.value;
        var sample = rowFor(type);
        lastData = { cols: sample.cols, rows: [] };
        for (var i = 0; i < count; i++) { lastData.rows.push(rowFor(type).vals); }

        headRow.innerHTML = '';
        for (var c = 0; c < lastData.cols.length; c++) {
            var th = document.createElement('th');
            th.textContent = lastData.cols[c];
            headRow.appendChild(th);
        }
        bodyRows.innerHTML = '';
        for (var r = 0; r < lastData.rows.length; r++) {
            var tr = document.createElement('tr');
            for (var v = 0; v < lastData.rows[r].length; v++) {
                var td = document.createElement('td');
                td.textContent = lastData.rows[r][v];
                tr.appendChild(td);
            }
            bodyRows.appendChild(tr);
        }
        results.classList.remove('d-none');
        results.scrollIntoView({ behavior: 'smooth' });
    });

    function asText() {
        if (!lastData) { return ''; }
        var lines = [lastData.cols.join(' | ')];
        for (var i = 0; i < lastData.rows.length; i++) { lines.push(lastData.rows[i].join(' | ')); }
        return lines.join('\n');
    }

    copyBtn.addEventListener('click', function () {
        var t = asText();
        if (!t) { showError('Please press Generate first.'); return; }
        hideError();
        function done() { copyBtn.textContent = 'Copied'; setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500); }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(done, function () { showError('Could not copy.'); });
        } else {
            var ta = document.createElement('textarea');
            ta.value = t;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); } catch (e) { showError('Could not copy.'); }
            document.body.removeChild(ta);
        }
    });

    csvBtn.addEventListener('click', function () {
        if (!lastData) { showError('Please press Generate first.'); return; }
        hideError();
        var lines = ['"' + lastData.cols.join('","') + '"'];
        for (var i = 0; i < lastData.rows.length; i++) {
            var cells = [];
            for (var j = 0; j < lastData.rows[i].length; j++) {
                cells.push('"' + String(lastData.rows[i][j]).replace(/"/g, '""') + '"');
            }
            lines.push(cells.join(','));
        }
        var blob = new Blob([lines.join('\n')], { type: 'text/csv' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'fake-data.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>
@endsection
