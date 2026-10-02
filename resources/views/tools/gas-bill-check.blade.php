@extends('layouts.app')

@section('title', 'Gas Bill Check Online - SNGPL & SSGC Duplicate Bill | Azlaan Tools')
@section('meta_description', 'Check your gas bill online for SNGPL and SSGC. Enter your consumer / customer number and open your duplicate bill on the official SNGPL or SSGC portal. Free, no signup.')

@section('content')
<div class="tool-wrap">
    <h1 class="mb-3">Gas Bill Check Online - SNGPL &amp; SSGC</h1>
    <p class="lead text-muted">Check your Sui gas bill in under a minute. Pick your gas company, enter your consumer number, and view or download your duplicate bill on the official portal.</p>

    <div class="alert alert-info" role="alert">
        <strong>Your privacy matters:</strong> Azlaan Tools does not store your bill and does not fetch your bill data. Your consumer number is only copied to your clipboard, and you are redirected to the official company website to view your bill.
    </div>

    <div id="copyToast" class="alert alert-success d-none position-fixed bottom-0 start-50 translate-middle-x shadow" style="z-index:1080;" role="status">Consumer number copied - paste it on the official site</div>

    <div class="row g-3 mb-4">
        {{-- SNGPL --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 gas-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">SNGPL - Sui Northern Gas</h2>
                    <p class="small text-muted">Covers Punjab, Khyber Pakhtunkhwa, Islamabad and AJK - including Lahore, Faisalabad, Multan, Rawalpindi, Gujranwala and Peshawar.</p>
                    <p class="small">Your <strong>Consumer Number</strong> is printed on your gas bill, usually at the top, labelled "Consumer No" - it is commonly 11 digits. Use an old bill if this month's bill has not arrived.</p>
                    <label class="form-label fw-semibold" for="num-sngpl">Consumer Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control consumer-input mb-2" id="num-sngpl" placeholder="Enter consumer number" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://www.sngpl.com.pk/" data-label="Consumer number">Check Bill on SNGPL</button>
                    <p class="small text-muted mt-2 mb-0">Opens sngpl.com.pk - go to Customer Services / "Get Your Gas Bill", paste your number and submit.</p>
                </div>
            </div>
        </div>
        {{-- SSGC --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 gas-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">SSGC - Sui Southern Gas</h2>
                    <p class="small text-muted">Covers Sindh and Balochistan - including Karachi, Hyderabad, Sukkur and Quetta.</p>
                    <p class="small">Your <strong>Customer Number</strong> is printed on your gas bill, usually at the top, labelled "Customer No" - it is commonly 10 digits. Use an old bill if this month's bill has not arrived.</p>
                    <label class="form-label fw-semibold" for="num-ssgc">Customer Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control consumer-input mb-2" id="num-ssgc" placeholder="Enter customer number" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://viewbill.ssgc.com.pk/" data-label="Customer number">Check Bill on SSGC</button>
                    <p class="small text-muted mt-2 mb-0">Opens the official SSGC View / Print Duplicate Bill portal (ssgc.com.pk). Paste your number and submit.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2>How to use</h2>
            <ol>
                <li>Check which company serves your area: SNGPL for Punjab, KP and Islamabad; SSGC for Sindh and Balochistan. The company name is printed at the top of any old gas bill.</li>
                <li>Find your consumer / customer number on that bill (top section, labelled "Consumer No" or "Customer No").</li>
                <li>Type the number in the correct box above, without spaces, and click <strong>Check Bill</strong>.</li>
                <li>Your number is copied automatically and the official portal opens in a new tab - paste the number there (press and hold, or Ctrl+V), complete the captcha if shown, and submit.</li>
                <li>View your bill amount and due date, and download or print the duplicate bill for payment or records.</li>
            </ol>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Frequently Asked Questions</h2>
            <h3 class="h6 mt-3">I do not have any old gas bill. How do I get my consumer number?</h3>
            <p>Check a previous payment receipt or your JazzCash / Easypaisa / bank app payment history - the consumer number is shown there. Otherwise call the gas helpline 1199 or visit your nearest SNGPL / SSGC customer centre with your CNIC.</p>
            <h3 class="h6">Why is no bill showing for this month?</h3>
            <p>Some residential gas consumers are billed every two months, and new bills are uploaded a few days after meter reading. If no bill appears, the billing cycle for your area may not have completed yet - try again in a few days.</p>
            <h3 class="h6">Why is my winter gas bill so high?</h3>
            <p>Geyser and heating use in winter increases consumption a lot, and higher usage moves you into higher tariff slabs, so the bill can rise sharply in cold months.</p>
            <h3 class="h6">Is checking the gas bill online free?</h3>
            <p>Yes, viewing your bill on the official SNGPL and SSGC portals is completely free. You only pay the bill amount itself.</p>
            <h3 class="h6">What if I smell gas or suspect a leak?</h3>
            <p class="mb-0">Do not wait - call the emergency helpline 1199 immediately, avoid flames and electrical switches, and leave the area if the smell is strong.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var toast = document.getElementById('copyToast');
    var toastTimer = null;

    function showToast() {
        toast.classList.remove('d-none');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toast.classList.add('d-none'); }, 4000);
    }

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        ta.remove();
    }

    function copyText(text, done) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
        } else {
            fallbackCopy(text);
            done();
        }
    }

    document.querySelectorAll('.check-bill-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('.gas-card');
            var input = card.querySelector('.consumer-input');
            var msg = card.querySelector('.msg');
            var label = btn.getAttribute('data-label') || 'Consumer number';
            var num = (input.value || '').replace(/\s+/g, '');
            msg.classList.remove('d-none', 'text-danger', 'text-success');
            if (!/^\d{8,14}$/.test(num)) {
                msg.textContent = 'Please enter a valid ' + label.toLowerCase() + ' (digits only, no spaces) exactly as printed on your gas bill.';
                msg.classList.add('text-danger');
                input.focus();
                return;
            }
            var url = btn.getAttribute('data-url');
            copyText(num, function () {
                msg.textContent = label + ' copied - paste it on the official site that just opened.';
                msg.classList.add('text-success');
                showToast();
                window.open(url, '_blank', 'noopener');
            });
        });
    });

    document.querySelectorAll('.consumer-input').forEach(function (input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                input.closest('.gas-card').querySelector('.check-bill-btn').click();
            }
        });
    });
})();
</script>
@endsection
