@extends('layouts.app')

@section('title', 'What Is My IP Address? - Check IP, Location & ISP | Azlaan Tools')
@section('meta_description', 'See your public IP address instantly, plus your country, city, ISP and browser / device information. Free, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">What Is My IP Address?</h1>
            <p class="lead text-muted">Your public IP address and connection details, shown instantly — plus information about your browser and device.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="text-muted">Your Public IP Address</div>
                    <div class="display-5 fw-bold my-2 text-break" id="ipValue">Loading...</div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="copyIpBtn">Copy IP</button>
                    <span class="text-success small d-none" id="copyMsg">Copied!</span>
                    <button type="button" class="btn btn-sm btn-primary ms-2" id="refreshBtn">Refresh</button>
                    <div class="alert alert-warning mt-3 mb-0 d-none text-start" id="errorBox"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Connection Details</h2>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr><th style="width: 40%">IP Address</th><td id="detailIp">-</td></tr>
                                <tr><th>Country</th><td id="detailCountry">-</td></tr>
                                <tr><th>City / Region</th><td id="detailCity">-</td></tr>
                                <tr><th>ISP / Organisation</th><td id="detailIsp">-</td></tr>
                                <tr><th>Timezone</th><td id="detailTimezone">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Browser &amp; Device Info</h2>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr><th style="width: 40%">User Agent</th><td class="text-break" id="infoUa">-</td></tr>
                                <tr><th>Screen Size</th><td id="infoScreen">-</td></tr>
                                <tr><th>Window Size</th><td id="infoWindow">-</td></tr>
                                <tr><th>Language</th><td id="infoLang">-</td></tr>
                                <tr><th>Platform</th><td id="infoPlatform">-</td></tr>
                                <tr><th>Connection Type</th><td id="infoConnection">-</td></tr>
                                <tr><th>Online Status</th><td id="infoOnline">-</td></tr>
                                <tr><th>Cookies Enabled</th><td id="infoCookies">-</td></tr>
                                <tr><th>Device Memory / Cores</th><td id="infoHardware">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Just open this page — your public IP address is detected automatically.</li>
                        <li>Check the Connection Details table for your country, city and ISP (when available).</li>
                        <li>Scroll down for browser and device information detected from your browser.</li>
                        <li>Click <strong>Copy IP</strong> to copy your address, or <strong>Refresh</strong> to check again (useful after connecting a VPN).</li>
                    </ol>
                    <p class="small text-muted mb-0">Privacy note: This check runs in your browser. We do not store your IP address on this page.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var ipValue = document.getElementById('ipValue');
    var errorBox = document.getElementById('errorBox');

    function setText(id, val) {
        document.getElementById(id).textContent = (val === undefined || val === null || val === '') ? '-' : val;
    }

    function fillBrowserInfo() {
        setText('infoUa', navigator.userAgent);
        setText('infoScreen', window.screen.width + ' x ' + window.screen.height);
        setText('infoWindow', window.innerWidth + ' x ' + window.innerHeight);
        setText('infoLang', navigator.language + (navigator.languages ? ' (' + navigator.languages.join(', ') + ')' : ''));
        setText('infoPlatform', (navigator.userAgentData && navigator.userAgentData.platform) || navigator.platform || '-');
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        setText('infoConnection', conn ? ((conn.effectiveType || '') + (conn.type ? ' / ' + conn.type : '') + (conn.downlink ? ' — approx ' + conn.downlink + ' Mbps' : '')) : 'Not available in this browser');
        setText('infoOnline', navigator.onLine ? 'Online' : 'Offline');
        setText('infoCookies', navigator.cookieEnabled ? 'Yes' : 'No');
        var hw = [];
        if (navigator.deviceMemory) hw.push(navigator.deviceMemory + ' GB RAM (approx)');
        if (navigator.hardwareConcurrency) hw.push(navigator.hardwareConcurrency + ' CPU cores');
        setText('infoHardware', hw.length ? hw.join(' — ') : 'Not available');
    }

    async function loadIp() {
        ipValue.textContent = 'Loading...';
        errorBox.classList.add('d-none');
        // Primary: ipify for the IP, then ipapi for extra details.
        try {
            var res = await fetch('https://api.ipify.org?format=json');
            if (!res.ok) throw new Error('ipify failed');
            var data = await res.json();
            if (!data.ip) throw new Error('no ip');
            ipValue.textContent = data.ip;
            setText('detailIp', data.ip);
            loadDetails();
            return;
        } catch (e) {
            // Fall through to fallback below
        }
        try {
            var res2 = await fetch('https://ipapi.co/json/');
            if (!res2.ok) throw new Error('ipapi failed');
            var d2 = await res2.json();
            if (d2.error) throw new Error('ipapi error');
            applyDetails(d2);
            if (d2.ip) ipValue.textContent = d2.ip;
            return;
        } catch (e2) {
            ipValue.textContent = 'Could not load';
            errorBox.textContent = 'We could not fetch your IP address. The lookup service may be blocked by your network, VPN or an ad-blocker. Your browser and device info below is still available.';
            errorBox.classList.remove('d-none');
        }
    }

    async function loadDetails() {
        try {
            var res = await fetch('https://ipapi.co/json/');
            if (!res.ok) return;
            var d = await res.json();
            if (!d.error) applyDetails(d);
        } catch (e) {
            // Extra details are optional — ignore failures silently
        }
    }

    function applyDetails(d) {
        if (d.ip) setText('detailIp', d.ip);
        setText('detailCountry', d.country_name ? d.country_name + (d.country_code ? ' (' + d.country_code + ')' : '') : '-');
        var cityParts = [];
        if (d.city) cityParts.push(d.city);
        if (d.region) cityParts.push(d.region);
        setText('detailCity', cityParts.length ? cityParts.join(', ') : '-');
        setText('detailIsp', d.org || d.asn || '-');
        setText('detailTimezone', d.timezone || '-');
    }

    document.getElementById('refreshBtn').addEventListener('click', function () { loadIp(); fillBrowserInfo(); });
    document.getElementById('copyIpBtn').addEventListener('click', async function () {
        var ip = ipValue.textContent.trim();
        if (!ip || ip === 'Loading...' || ip === 'Could not load') return;
        try { await navigator.clipboard.writeText(ip); }
        catch (e) { /* clipboard unavailable */ }
        var msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(function () { msg.classList.add('d-none'); }, 2000);
    });
    window.addEventListener('online', fillBrowserInfo);
    window.addEventListener('offline', fillBrowserInfo);
    window.addEventListener('resize', fillBrowserInfo);

    fillBrowserInfo();
    loadIp();
})();
</script>
@endsection
