@extends('layouts.app')

@section('title', 'PTCL Bill Check Online - Duplicate Bill Download | Azlaan Tools')
@section('meta_description', 'Check your PTCL landline and broadband bill online. Enter your phone number with area code or Account ID and open your duplicate bill on the official PTCL portal. Free, no signup.')

@section('content')
<div class="tool-wrap">
    <h1 class="mb-3">PTCL Bill Check Online</h1>
    <p class="lead text-muted">View, download or print your PTCL telephone and internet duplicate bill - no need to wait for the paper copy or visit a PTCL office.</p>

    <div class="alert alert-info" role="alert">
        <strong>Your privacy matters:</strong> Azlaan Tools does not store your bill and does not fetch your bill data. The number you enter is only copied to your clipboard, and you are redirected to the official PTCL website to view your bill.
    </div>

    <div id="copyToast" class="alert alert-success d-none position-fixed bottom-0 start-50 translate-middle-x shadow" style="z-index:1080;" role="status">Number copied - paste it on the official PTCL site</div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">PTCL Duplicate Bill &amp; Bill Inquiry</h2>
            <p>PTCL serves landline, broadband (DSL / FlashFiber), CharJi and EVO customers across Pakistan. The official PTCL portal lets you inquire your current bill and download a duplicate bill using either:</p>
            <ul>
                <li><strong>Phone number with area code</strong> - for example 042 for Lahore, 051 for Islamabad / Rawalpindi, 021 for Karachi, followed by your landline number, or</li>
                <li><strong>Account ID</strong> - printed on every PTCL bill, usually in the top section of the bill.</li>
            </ul>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="ptclPhone">Phone Number (with area code)</label>
                    <input type="text" inputmode="tel" maxlength="17" class="form-control ptcl-input mb-2" id="ptclPhone" placeholder="e.g. 04235781234" autocomplete="off">
                    <div class="form-text">Enter digits only, starting with your city area code.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="ptclAccount">Account ID (optional alternative)</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ptcl-input mb-2" id="ptclAccount" placeholder="Printed on your PTCL bill" autocomplete="off">
                    <div class="form-text">If you use the Account ID, you can leave the phone field empty.</div>
                </div>
            </div>
            <div class="form-text mb-2 msg d-none" id="ptclMsg"></div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="ptclCheckBtn" data-url="https://dbill.ptcl.net.pk/">Check Bill on PTCL</button>
                <a class="btn btn-outline-primary" href="https://www.ptcl.com.pk/" target="_blank" rel="noopener">Open ptcl.com.pk</a>
            </div>
            <p class="small text-muted mt-2 mb-0">The Check Bill button copies your phone number (or Account ID, if that is what you entered) and opens PTCL's official duplicate bill portal in a new tab.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2>How to use</h2>
            <ol>
                <li>Take any old PTCL bill and find your phone number and Account ID - both are printed on it.</li>
                <li>Enter your phone number <strong>with area code</strong> in the first box above (or enter only your Account ID in the second box).</li>
                <li>Click <strong>Check Bill on PTCL</strong> - your number is copied automatically and the official PTCL duplicate bill portal opens in a new tab.</li>
                <li>Paste the number on the PTCL site (press and hold, or Ctrl+V), select your service type if asked (Landline / Broadband or CharJi / EVO), and submit.</li>
                <li>Your bill appears with amount and due date - download or print it as a duplicate bill, then pay via your bank app, JazzCash, Easypaisa or at a bank counter.</li>
            </ol>
            <p class="small text-muted mb-0">Tip: On ptcl.com.pk you can also use My PTCL / Bill Payment for the same inquiry, and the PTCL Touch app shows bills for linked connections.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Frequently Asked Questions</h2>
            <h3 class="h6 mt-3">Where do I find my PTCL Account ID?</h3>
            <p>It is printed on every PTCL bill, usually near the top. Keep one old bill or a photo of it - the Account ID stays the same every month.</p>
            <h3 class="h6">I have no old bill. What can I do?</h3>
            <p>Call the PTCL helpline 1218 from your registered number and ask for your Account ID after verification. You can also send your complete PTCL phone number (with area code) by SMS to 1217 to receive your current bill amount and due date.</p>
            <h3 class="h6">The portal says "No Record Found" - why?</h3>
            <p>Usually the phone format or Account ID is wrong. Try the other option: if the phone number did not work, use the Account ID, and make sure there are no spaces or dashes in the number.</p>
            <h3 class="h6">Is the duplicate PTCL bill valid for payment?</h3>
            <p>Yes. The duplicate bill from the official PTCL portal carries the same details as the original and is accepted at banks and payment channels.</p>
            <h3 class="h6">Is this free?</h3>
            <p class="mb-0">Yes. Bill inquiry and duplicate bill download on the official PTCL portal are free and need no login for the public bill inquiry.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var toast = document.getElementById('copyToast');
    var msg = document.getElementById('ptclMsg');
    var phoneInput = document.getElementById('ptclPhone');
    var accountInput = document.getElementById('ptclAccount');
    var btn = document.getElementById('ptclCheckBtn');
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

    btn.addEventListener('click', function () {
        var phone = (phoneInput.value || '').replace(/[\s-]+/g, '');
        var account = (accountInput.value || '').replace(/\s+/g, '');
        var value = phone || account;
        msg.classList.remove('d-none', 'text-danger', 'text-success');
        if (!value || !/^\d{7,15}$/.test(value)) {
            msg.textContent = 'Please enter your PTCL phone number with area code, or your Account ID, digits only - exactly as printed on your bill.';
            msg.classList.add('text-danger');
            (phone ? phoneInput : accountInput).focus();
            return;
        }
        var url = btn.getAttribute('data-url');
        copyText(value, function () {
            msg.textContent = 'Number copied - paste it on the official PTCL site that just opened.';
            msg.classList.add('text-success');
            showToast();
            window.open(url, '_blank', 'noopener');
        });
    });

    [phoneInput, accountInput].forEach(function (input) {
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') btn.click(); });
    });
})();
</script>
@endsection
