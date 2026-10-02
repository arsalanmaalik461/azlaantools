@extends('layouts.app')

@section('title', 'Electricity Bill Check Online Pakistan - All DISCOs | Azlaan Tools')
@section('meta_description', 'Check your electricity bill online for LESCO, IESCO, FESCO, MEPCO, GEPCO, HESCO, SEPCO, PESCO, QESCO and TESCO. Enter your 14-digit reference number and open your bill on the official company portal. Free, no signup.')

@section('content')
<div class="tool-wrap">
    <h1 class="mb-3">Electricity Bill Check Online - Pakistan</h1>
    <p class="lead text-muted">Lost your paper bill or it never arrived? Find your electricity company below, enter your 14-digit reference number, and view or download your duplicate bill on the official portal.</p>

    <div class="alert alert-info" role="alert">
        <strong>Your privacy matters:</strong> Azlaan Tools does not store your bill and does not fetch your bill data. Your reference number is only copied to your clipboard, and you are redirected to the official company website to view your bill.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">Where is my reference number?</h2>
            <p class="mb-0">Your <strong>14-digit reference number</strong> is printed on every electricity bill, usually near the top, labelled "Reference No". Enter it <strong>without spaces</strong>. The same number works every month - you can use an old bill to find it. Your company name is also printed at the top of your bill.</p>
        </div>
    </div>

    <div id="copyToast" class="alert alert-success d-none position-fixed bottom-0 start-50 translate-middle-x shadow" style="z-index:1080;" role="status">Reference number copied - paste it on the official site</div>

    <div class="row g-3 mb-4">
        {{-- LESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">LESCO</h2>
                    <p class="small text-muted mb-2">Lahore Electric Supply Company - Lahore, Kasur, Okara, Sheikhupura, Nankana Sahib</p>
                    <label class="form-label fw-semibold" for="ref-lesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-lesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/lescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- IESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">IESCO</h2>
                    <p class="small text-muted mb-2">Islamabad Electric Supply Company - Islamabad, Rawalpindi, Attock, Chakwal, Jhelum</p>
                    <label class="form-label fw-semibold" for="ref-iesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-iesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/iescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- FESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">FESCO</h2>
                    <p class="small text-muted mb-2">Faisalabad Electric Supply Company - Faisalabad, Jhang, Chiniot, Toba Tek Singh, Sargodha and nearby districts</p>
                    <label class="form-label fw-semibold" for="ref-fesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-fesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/fescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- MEPCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">MEPCO</h2>
                    <p class="small text-muted mb-2">Multan Electric Power Company - Multan and South Punjab (Bahawalpur, DG Khan, Sahiwal, Vehari, Khanewal, Rahim Yar Khan)</p>
                    <label class="form-label fw-semibold" for="ref-mepco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-mepco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/mepcobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- GEPCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">GEPCO</h2>
                    <p class="small text-muted mb-2">Gujranwala Electric Power Company - Gujranwala, Sialkot, Gujrat, Narowal, Hafizabad, Mandi Bahauddin</p>
                    <label class="form-label fw-semibold" for="ref-gepco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-gepco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/gepcobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- HESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">HESCO</h2>
                    <p class="small text-muted mb-2">Hyderabad Electric Supply Company - Hyderabad and surrounding districts in Sindh</p>
                    <label class="form-label fw-semibold" for="ref-hesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-hesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/hescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- SEPCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">SEPCO</h2>
                    <p class="small text-muted mb-2">Sukkur Electric Power Company - Sukkur, Larkana, Khairpur, Ghotki, Jacobabad and upper Sindh</p>
                    <label class="form-label fw-semibold" for="ref-sepco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-sepco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/sepcobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- PESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">PESCO</h2>
                    <p class="small text-muted mb-2">Peshawar Electric Supply Company - Peshawar and Khyber Pakhtunkhwa (KP)</p>
                    <label class="form-label fw-semibold" for="ref-pesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-pesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/pescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- QESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">QESCO</h2>
                    <p class="small text-muted mb-2">Quetta Electric Supply Company - Quetta and Balochistan</p>
                    <label class="form-label fw-semibold" for="ref-qesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-qesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/qescobill">Check Bill</button>
                </div>
            </div>
        </div>
        {{-- TESCO --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 disco-card">
                <div class="card-body">
                    <h2 class="h5 card-title mb-1">TESCO</h2>
                    <p class="small text-muted mb-2">Tribal Electric Supply Company - Tribal areas (Khyber, Bajaur, Mohmand, Kurram, Orakzai, North and South Waziristan)</p>
                    <label class="form-label fw-semibold" for="ref-tesco">14-digit Reference Number</label>
                    <input type="text" inputmode="numeric" maxlength="17" class="form-control ref-input mb-2" id="ref-tesco" placeholder="e.g. 12345678901234" autocomplete="off">
                    <div class="form-text mb-2 msg d-none"></div>
                    <button type="button" class="btn btn-primary check-bill-btn" data-url="https://bill.pitc.com.pk/tescobill">Check Bill</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2>How to use</h2>
            <ol>
                <li>Find your electricity company - it is printed at the top of any old bill (for example LESCO for Lahore, MEPCO for Multan).</li>
                <li>Copy the 14-digit reference number from that bill and type it in your company's box above, without spaces.</li>
                <li>Click <strong>Check Bill</strong>. Your reference number is copied automatically and the official company portal opens in a new tab.</li>
                <li>Paste the reference number on the official site (press and hold, or Ctrl+V), complete the captcha if shown, and submit.</li>
                <li>Your current bill appears - you can view the amount and due date, and download or print it as a duplicate bill.</li>
            </ol>
            <p class="small text-muted mb-0">Karachi note: Karachi is served by K-Electric, which is not a DISCO listed here - K-Electric customers should use the KE website or KE Live app.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Frequently Asked Questions</h2>
            <h3 class="h6 mt-3">Is this service free?</h3>
            <p>Yes. Checking your bill on the official portal is completely free and needs no signup or login.</p>
            <h3 class="h6">Can I check my bill without a reference number?</h3>
            <p>The official portals need the reference number (some also accept a Customer ID, printed on the bill next to the reference number). If you have no old bill at all, call the electricity helpline 118 or visit your nearest customer service centre with your CNIC and address.</p>
            <h3 class="h6">Why does the portal say "No Record Found"?</h3>
            <p>Almost always a digit was typed wrong, or the number was entered on the wrong company's portal. Recheck the number against a paper bill and make sure you picked the company for your area.</p>
            <h3 class="h6">Is a duplicate bill printed from the official portal valid?</h3>
            <p>Yes. It is generated from the company's own billing system with the same details as the paper bill, and is accepted at banks and payment counters.</p>
            <h3 class="h6">Does Azlaan Tools see or save my bill?</h3>
            <p class="mb-0">No. Nothing you type here is sent to us or stored. The button only copies the number on your own device and opens the official portal.</p>
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

    function copyText(text, done) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
        } else {
            fallbackCopy(text);
            done();
        }
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

    document.querySelectorAll('.check-bill-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('.disco-card');
            var input = card.querySelector('.ref-input');
            var msg = card.querySelector('.msg');
            var ref = (input.value || '').replace(/\s+/g, '');
            msg.classList.remove('d-none', 'text-danger', 'text-success');
            if (!/^\d{14}$/.test(ref)) {
                msg.textContent = 'Please enter a valid 14-digit reference number (digits only, no spaces). Check it on your bill and try again.';
                msg.classList.add('text-danger');
                input.focus();
                return;
            }
            var url = btn.getAttribute('data-url');
            copyText(ref, function () {
                msg.textContent = 'Reference number copied - paste it on the official site that just opened.';
                msg.classList.add('text-success');
                showToast();
                window.open(url, '_blank', 'noopener');
            });
        });
    });

    document.querySelectorAll('.ref-input').forEach(function (input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                var card = input.closest('.disco-card');
                card.querySelector('.check-bill-btn').click();
            }
        });
    });
})();
</script>
@endsection
