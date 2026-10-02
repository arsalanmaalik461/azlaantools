@extends('layouts.app')

@section('title', 'E-Challan Check Online Pakistan - Punjab, Karachi, Islamabad | Azlaan Tools')
@section('meta_description', 'Check your traffic e-challan on the official portals: PSCA Punjab, Sindh Police Karachi and Islamabad Police. Step-by-step guide and how to pay via PSID, JazzCash, Easypaisa and bank apps. Free guide, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">E-Challan Check Online — Pakistan</h1>
            <p class="lead text-muted">Got a traffic camera challan? Select your city — we will take you straight to the <strong>official government portal</strong>, where you can check and pay your challan.</p>

            <div class="alert alert-info" role="alert">
                <strong>Privacy &amp; safety:</strong> Azlaan Tools does not fetch any challan data, and your vehicle number / CNIC is not saved here. Always check your challan only on the official portal. <strong>Warning:</strong> never pay by clicking a link in an SMS or WhatsApp message — fake e-challan SMS is a common scam. Type the portal address yourself and open it.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="city" class="form-label fw-semibold">Select Your Province / City</label>
                    <select class="form-select form-select-lg" id="city">
                        <option value="punjab" selected>Punjab (Lahore, Faisalabad, Rawalpindi, Multan, Gujranwala…)</option>
                        <option value="sindh">Sindh — Karachi</option>
                        <option value="islamabad">Islamabad</option>
                        <option value="kpk">Khyber Pakhtunkhwa (KPK)</option>
                        <option value="other">Balochistan / Gilgit-Baltistan / AJK</option>
                    </select>
                </div>
            </div>

            <div class="city-panel" id="panel-punjab">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Punjab — PSCA E-Challan Portal</h2>
                        <p>Camera challans in Punjab are issued by the <strong>Punjab Safe Cities Authority (PSCA)</strong>. Challans from Lahore, Faisalabad, Rawalpindi, Multan, Gujranwala, Sialkot and other cities are all found on this portal.</p>
                        <a class="btn btn-primary btn-lg" href="https://echallan.psca.gop.pk" target="_blank" rel="noopener">Open Official Portal — echallan.psca.gop.pk</a>
                        <h3 class="h6 mt-4">How to check</h3>
                        <ol>
                            <li>Open the official portal with the button above.</li>
                            <li>Enter your <strong>Plate Number</strong> (e.g. LEB-1234) — exactly as it appears on your number plate.</li>
                            <li>Enter your <strong>13-digit CNIC</strong> (without dashes) or the vehicle's <strong>Chassis Number</strong>.</li>
                            <li>Press Search — all pending and paid challans will appear, with the violation type, date, place, fine and camera photo.</li>
                            <li>Every challan comes with a <strong>PSID</strong> — use that same PSID for payment.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="city-panel d-none" id="panel-sindh">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Sindh — Karachi Traffic Police / Sindh Police</h2>
                        <p>Karachi's e-challans are issued through the <strong>Sindh Police</strong> and Karachi Traffic Police system. To check your challan, open the Sindh Police official website and go to the E-Challan / Traffic section.</p>
                        <a class="btn btn-primary btn-lg" href="https://www.sindhpolice.gov.pk" target="_blank" rel="noopener">Open Official Portal — sindhpolice.gov.pk</a>
                        <h3 class="h6 mt-4">How to check</h3>
                        <ol>
                            <li>Open the Sindh Police official website with the button above and look for the E-Challan / Karachi Traffic Police section.</li>
                            <li>Enter your <strong>vehicle registration number</strong> or <strong>CNIC</strong>.</li>
                            <li>Press Search — your pending challan, violation, date and fine will appear.</li>
                            <li>You can also get challan notifications and details from the Sindh Police <strong>TRACS Citizen App</strong>.</li>
                            <li>Pay using the PSID / challan number given on the challan (see the payment steps below).</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="city-panel d-none" id="panel-islamabad">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Islamabad — Islamabad Police E-Challan Verification</h2>
                        <p>Challans in Islamabad are issued by the <strong>Islamabad Traffic Police (ITP)</strong> through Safe City cameras. The Services section of the official Islamabad Police website has "E-Challan Verification", which takes you to the traffic portal.</p>
                        <a class="btn btn-primary btn-lg" href="https://traffic.islamabadpolice.gov.pk/Echallan" target="_blank" rel="noopener">Open Official Portal — traffic.islamabadpolice.gov.pk</a>
                        <p class="small text-muted mt-2">If the portal does not open, open the Islamabad Police main website <strong>islamabadpolice.gov.pk</strong> and go to Services → E-Challan Verification, or call the ITP Helpline <strong>1915</strong>.</p>
                        <h3 class="h6 mt-4">How to check</h3>
                        <ol>
                            <li>Keep the e-ticket number from your SMS safe — you will need it at every step.</li>
                            <li>Open the official traffic portal with the button above.</li>
                            <li>Enter the <strong>vehicle number / ticket number</strong> asked in the form and search.</li>
                            <li>Your pending challan details and the payment method will appear on the portal itself — follow only that.</li>
                            <li>If the portal is down or looks empty, show the SMS at your zone's ITP challan branch to confirm the challan.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="city-panel d-none" id="panel-kpk">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Khyber Pakhtunkhwa (KPK)</h2>
                        <div class="alert alert-warning mb-0">
                            <strong>Official online portal not available</strong> — as far as we have verified, there is no verified official online e-challan portal for KPK. To check your challan, visit your nearest <strong>traffic police office or Police Facilitation / Khidmat Markaz</strong>, and you can pay the challan with the PSID you get there via JazzCash / Easypaisa / bank app.
                        </div>
                    </div>
                </div>
            </div>

            <div class="city-panel d-none" id="panel-other">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Balochistan / Gilgit-Baltistan / AJK</h2>
                        <div class="alert alert-warning mb-0">
                            <strong>Official online portal not available</strong> — we could not find a verified official online e-challan portal for these areas. For challan details and payment, contact your nearest <strong>traffic police office</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2>How to Pay the Challan (with PSID)</h2>
                    <ol>
                        <li>Open your challan on the official portal and note its <strong>PSID (Payment Slip ID)</strong>.</li>
                        <li><strong>JazzCash:</strong> open the app → Payments / Government Payments → Traffic Challan → enter the PSID → confirm the amount and pay.</li>
                        <li><strong>Easypaisa:</strong> open the app → Government / Traffic Challan section → enter the PSID → pay.</li>
                        <li><strong>Bank apps / ATM (1LINK):</strong> Bill Payments → Government Payments → Traffic Challan → enter the PSID to pay. In Punjab you can also use the <strong>ePay Punjab</strong> app.</li>
                        <li>Keep the receipt / SMS after payment. The status usually turns "Paid" on the portal right away, otherwise within 24–48 hours. If the portal has no record of an SMS challan, that SMS may be fake — never pay it.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2>Approximate Fines for Common Violations</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Violation</th><th>Approximate Fine (Rs)</th></tr></thead>
                            <tbody>
                                <tr><td>No Helmet (Motorcycle)</td><td>~2,000</td></tr>
                                <tr><td>Signal Violation (Red Light)</td><td>~500 – 5,000</td></tr>
                                <tr><td>Over Speeding</td><td>~500 – 2,000</td></tr>
                                <tr><td>One-Way Violation</td><td>~1,000 – 2,000</td></tr>
                                <tr><td>No Seat Belt</td><td>~500 – 2,000</td></tr>
                                <tr><td>Mobile Phone Use (While Driving)</td><td>~500 – 5,000</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-danger fw-semibold mb-0">These amounts are only approximate — official amounts on the portal may differ. Always pay the actual amount written on your challan.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select your province / city.</li>
                        <li>Press the official portal button — a new tab will open.</li>
                        <li>Enter your plate number and CNIC there to check your challan, and pay with the PSID.</li>
                    </ol>
                    <p class="small text-muted mb-0">For car wiring, camera systems or electrical work: Azlaan Electric AC Solar Center, Faisalabad — 0300-8987448.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var sel = document.getElementById('city');
    sel.addEventListener('change', function () {
        document.querySelectorAll('.city-panel').forEach(function (p) { p.classList.add('d-none'); });
        document.getElementById('panel-' + sel.value).classList.remove('d-none');
    });
})();
</script>
@endsection
