@extends('layouts.app')

@section('title', 'IP Subnet Calculator — Azlaan Tools')
@section('meta_description', 'Free IPv4 subnet calculator. Enter an IP address and CIDR prefix to get network address, broadcast, netmask, wildcard mask, usable hosts range, IP class and binary representation.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-2">IP Subnet Calculator</h1>
            <p class="lead text-muted">Enter an IPv4 address and CIDR prefix — get the full network, broadcast, host range and masks instantly, with binary too.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-7">
                            <label for="ipInput" class="form-label fw-semibold">IPv4 Address</label>
                            <input type="text" class="form-control form-control-lg" id="ipInput" placeholder="e.g. 192.168.1.10" value="192.168.1.10">
                        </div>
                        <div class="col-5">
                            <label for="prefixSelect" class="form-label fw-semibold">CIDR Prefix / Netmask</label>
                            <select class="form-select form-select-lg" id="prefixSelect"></select>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="subnetNote">Enter a valid IPv4 address (four parts, each part 0–255). The result updates live when you change the prefix.</p>

                    <div class="result-box mt-3">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><th style="width:45%">Network Address</th><td id="resNetwork">—</td></tr>
                                    <tr><th>Broadcast Address</th><td id="resBroadcast">—</td></tr>
                                    <tr><th>Netmask</th><td id="resNetmask">—</td></tr>
                                    <tr><th>Wildcard Mask</th><td id="resWildcard">—</td></tr>
                                    <tr><th>First Usable Host</th><td id="resFirst">—</td></tr>
                                    <tr><th>Last Usable Host</th><td id="resLast">—</td></tr>
                                    <tr><th>Total Hosts</th><td id="resTotal">—</td></tr>
                                    <tr><th>Usable Hosts</th><td id="resUsable">—</td></tr>
                                    <tr><th>IP Class</th><td id="resClass">—</td></tr>
                                    <tr><th>Type</th><td id="resType">—</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted mt-2 mb-0" id="edgeNote"></p>
                    </div>

                    <h2 class="h6 fw-semibold mt-4">Binary representation</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <tbody>
                                <tr><th style="width:25%">IP Address</th><td class="font-monospace small" id="binIp">—</td></tr>
                                <tr><th>Netmask</th><td class="font-monospace small" id="binMask">—</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How it works</h2>
                    <p>The CIDR prefix tells how many of the first bits of the IP are the network part; the rest are for hosts. AND-ing the netmask with the IP gives the network address, and adding the wildcard (the mask's inverse) gives the broadcast. Normally usable hosts = total hosts &minus; 2 (network and broadcast are reserved).</p>
                    <p class="mb-1"><strong>Example 1:</strong> 192.168.1.10/24 — network <strong>192.168.1.0</strong>, broadcast <strong>192.168.1.255</strong>, usable hosts 192.168.1.1 – 192.168.1.254, <strong>254</strong> usable in total.</p>
                    <p class="mb-0"><strong>Example 2:</strong> a /30 subnet has 4 addresses in total but only <strong>2 usable</strong> — that is why /30 is commonly used for router-to-router links. /31 and /32 are special cases: this calculator shows usable hosts as 0 for them (the network/broadcast concept does not apply there / a /32 is a single host route).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var prefixSelect = document.getElementById('prefixSelect');
    var maskNames = [];
    function maskFromPrefix(p) {
        if (p === 0) return 0;
        return (0xFFFFFFFF << (32 - p)) >>> 0;
    }
    function toDotted(n) {
        return ((n >>> 24) & 255) + '.' + ((n >>> 16) & 255) + '.' + ((n >>> 8) & 255) + '.' + (n & 255);
    }
    function toBinary(n) {
        var parts = [];
        for (var i = 3; i >= 0; i--) {
            var octet = (n >>> (i * 8)) & 255;
            var bin = octet.toString(2);
            while (bin.length < 8) bin = '0' + bin;
            parts.push(bin);
        }
        return parts.join('.');
    }
    function fmt(n) {
        return Number(n).toLocaleString('en-PK');
    }
    var optHtml = '';
    for (var p = 0; p <= 32; p++) {
        var m = toDotted(maskFromPrefix(p));
        maskNames[p] = m;
        optHtml += '<option value="' + p + '"' + (p === 24 ? ' selected' : '') + '>/' + p + ' — ' + m + '</option>';
    }
    prefixSelect.innerHTML = optHtml;

    function parseIp(str) {
        if (!str) return null;
        var parts = str.trim().split('.');
        if (parts.length !== 4) return null;
        var octets = [];
        for (var i = 0; i < 4; i++) {
            if (!/^\d{1,3}$/.test(parts[i])) return null;
            var v = parseInt(parts[i], 10);
            if (v < 0 || v > 255) return null;
            octets.push(v);
        }
        return octets;
    }
    function ipClass(first) {
        if (first < 128) return 'A';
        if (first < 192) return 'B';
        if (first < 224) return 'C';
        if (first < 240) return 'D (multicast)';
        return 'E (experimental)';
    }
    function ipType(octets, ipNum) {
        var a = octets[0], b = octets[1];
        if (a === 10) return 'Private (RFC 1918)';
        if (a === 172 && b >= 16 && b <= 31) return 'Private (RFC 1918)';
        if (a === 192 && b === 168) return 'Private (RFC 1918)';
        if (a === 127) return 'Loopback';
        if (a === 169 && b === 254) return 'Link-local';
        return 'Public';
    }
    function setAll(val) {
        var ids = ['resNetwork', 'resBroadcast', 'resNetmask', 'resWildcard', 'resFirst', 'resLast', 'resTotal', 'resUsable', 'resClass', 'resType', 'binIp', 'binMask'];
        for (var i = 0; i < ids.length; i++) document.getElementById(ids[i]).textContent = val;
    }
    function calc() {
        var note = document.getElementById('subnetNote');
        var edge = document.getElementById('edgeNote');
        var octets = parseIp(document.getElementById('ipInput').value);
        var prefix = parseInt(prefixSelect.value, 10);
        if (!octets) {
            setAll('—');
            edge.textContent = '';
            note.textContent = 'Wrong IP format — a correct example: 192.168.1.10 (four parts, each part 0–255).';
            return;
        }
        note.textContent = 'Enter a valid IPv4 address (four parts, each part 0–255). The result updates live when you change the prefix.';
        var ipNum = (((octets[0] << 24) | (octets[1] << 16) | (octets[2] << 8) | octets[3]) >>> 0);
        var mask = maskFromPrefix(prefix);
        var wildcard = ((~mask) >>> 0);
        var network = ((ipNum & mask) >>> 0);
        var broadcast = ((network | wildcard) >>> 0);
        var total = Math.pow(2, 32 - prefix);
        var usable, first, last;
        if (prefix <= 30) {
            usable = total - 2;
            first = toDotted((network + 1) >>> 0);
            last = toDotted((broadcast - 1) >>> 0);
            edge.textContent = '';
        } else if (prefix === 31) {
            usable = 0;
            first = '—'; last = '—';
            edge.textContent = 'Note: in /31 the network/broadcast are not reserved (point-to-point links, RFC 3021), but in the normal count usable hosts are counted as 0 — so 0 is shown here.';
        } else {
            usable = 0;
            first = toDotted(network); last = toDotted(network);
            edge.textContent = 'Note: /32 is a single host route — the whole subnet is just this one IP, so usable hosts is shown as 0.';
        }
        document.getElementById('resNetwork').textContent = toDotted(network) + ' /' + prefix;
        document.getElementById('resBroadcast').textContent = toDotted(broadcast);
        document.getElementById('resNetmask').textContent = toDotted(mask);
        document.getElementById('resWildcard').textContent = toDotted(wildcard);
        document.getElementById('resFirst').textContent = first;
        document.getElementById('resLast').textContent = last;
        document.getElementById('resTotal').textContent = fmt(total);
        document.getElementById('resUsable').textContent = fmt(usable);
        document.getElementById('resClass').textContent = 'Class ' + ipClass(octets[0]);
        document.getElementById('resType').textContent = ipType(octets, ipNum);
        document.getElementById('binIp').textContent = toBinary(ipNum);
        document.getElementById('binMask').textContent = toBinary(mask);
    }
    document.getElementById('ipInput').addEventListener('input', calc);
    prefixSelect.addEventListener('change', calc);
    calc();
})();
</script>
@endsection
