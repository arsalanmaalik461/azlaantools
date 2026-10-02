@extends('layouts.app')

@section('title', 'Barcode Generator - Azlaan Tools')
@section('meta_description', 'Generate Code128, Code39, EAN-13, UPC-A and EAN-8 barcodes for products and labels, free online.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Barcode Generator</h1>
            <p class="lead text-muted">Create barcodes for products and labels — Code128, Code39, EAN-13, UPC-A, EAN-8. Generate free barcodes and download them as PNG.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="typeSel" class="form-label fw-semibold">Barcode type</label>
                        <select class="form-select" id="typeSel">
                            <option value="code128">Code128 (general / products)</option>
                            <option value="code39">Code39 (inventory / labels)</option>
                            <option value="ean13">EAN-13 (retail products)</option>
                            <option value="upca">UPC-A (US/Canada retail)</option>
                            <option value="ean8">EAN-8 (small products)</option>
                        </select>
                        <div class="form-text" id="typeHint">Any text or number (A-Z, 0-9, spaces and symbols).</div>
                    </div>
                    <div class="mb-3">
                        <label for="dataInput" class="form-label fw-semibold">Barcode data</label>
                        <input type="text" class="form-control" id="dataInput" placeholder="e.g. 890123456789 or AZLAAN-001">
                    </div>
                    <div class="mb-3">
                        <label for="scaleRange" class="form-label fw-semibold">Size: <span id="scaleVal">2</span>x</label>
                        <input type="range" class="form-range" id="scaleRange" min="1" max="5" value="2">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Generate Barcode</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4 text-center">
                        <canvas id="bcCanvas" class="img-fluid border rounded bg-white"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success" id="dlBtn">Download PNG</button>
                        </div>
                        <div class="form-text mt-2" id="bcInfo"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Choose the barcode type (EAN-13 for retail products, Code128 for general use).</li>
                <li>Enter the data — the check digit is calculated automatically for EAN/UPC.</li>
                <li>Press <strong>Generate Barcode</strong> and download the PNG.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var typeSel = document.getElementById('typeSel');
    var dataInput = document.getElementById('dataInput');
    var scaleRange = document.getElementById('scaleRange');
    var scaleVal = document.getElementById('scaleVal');
    var typeHint = document.getElementById('typeHint');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var bcCanvas = document.getElementById('bcCanvas');
    var dlBtn = document.getElementById('dlBtn');
    var bcInfo = document.getElementById('bcInfo');

    var HINTS = {
        code128: 'Any text or number (A-Z, 0-9, spaces and symbols).',
        code39: 'Only A-Z, 0-9 and - . space $ / + % allowed.',
        ean13: 'Enter 12 digits (13th check digit is automatic) or 13 digits to verify.',
        upca: 'Enter 11 digits (12th check digit is automatic) or 12 digits to verify.',
        ean8: 'Enter 7 digits (8th check digit is automatic) or 8 digits to verify.'
    };

    var C128 = ["212222","222122","222221","121223","121322","131222","122213","122312","132212","221213",
    "221312","231212","112232","122132","122231","113222","123122","123221","223211","221132",
    "221231","213212","223112","312131","311222","321122","321221","312212","322112","322211",
    "212123","212321","232121","111323","131123","131321","112313","132113","132311","211313",
    "231113","231311","112133","112331","132131","113123","113321","133121","313121","211331",
    "231131","213113","213311","213131","311123","311321","331121","312113","312311","332111",
    "314111","221411","431111","111224","111422","121124","121421","141122","121221","112214",
    "112412","122114","122411","142112","142211","241211","221114","413111","241112","134111",
    "111242","121142","121241","114212","124112","124211","411212","421112","421211","212141",
    "214121","412121","111143","111341","131141","114113","114311","411113","411311","113141",
    "114131","311141","411131","211412","211214","211232","2331112"];

    var C39 = {'0':'nnnwwnwnn','1':'wnnwnnnnw','2':'nnwwnnnnw','3':'wnwwnnnnn','4':'nnnwwnnnw',
    '5':'wnnwwnnnn','6':'nnwwwnnnn','7':'nnnwnnwnw','8':'wnnwnnwnn','9':'nnwwnnwnn',
    'A':'wnnnnwnnw','B':'nnwnnwnnw','C':'wnwnnwnnn','D':'nnnnwwnnw','E':'wnnnwwnnn',
    'F':'nnwnwwnnn','G':'nnnnnwwnw','H':'wnnnnwwnn','I':'nnwnnwwnn','J':'nnnnwwwnn',
    'K':'wnnnnnnww','L':'nnwnnnnww','M':'wnwnnnnwn','N':'nnnnwnnww','O':'wnnnwnnwn',
    'P':'nnwnwnnwn','Q':'nnnnnnwww','R':'wnnnnnwwn','S':'nnwnnnwwn','T':'nnnnwnwwn',
    'U':'wwnnnnnnw','V':'nwwnnnnnw','W':'wwwnnnnnn','X':'nwnnwnnnw','Y':'wwnnwnnnn',
    'Z':'nwwnwnnnn','-':'nwnnnnwnw','.':'wwnnnnwnn',' ':'nwwnnnwnn','*':'nwnnwnwnn',
    '$':'nwnwnwnnn','/':'nwnwnnnwn','+':'nwnnnwnwn','%':'nnnwnwnwn'};

    var L_PAT = ['0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'];
    var G_PAT = ['0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111'];
    var R_PAT = ['1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100'];
    var EAN13_PAR = ['LLLLLL','LLGLGG','LLGGLG','LLGGGL','LGLLGG','LGGLLG','LGGGLL','LGLGLG','LGLGGL','LLGLLG'];

    typeSel.addEventListener('change', function () {
        typeHint.textContent = HINTS[typeSel.value];
        results.classList.add('d-none');
    });
    scaleRange.addEventListener('input', function () {
        scaleVal.textContent = scaleRange.value;
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    function eanCheck(digits) {
        var sum = 0;
        for (var i = 0; i < digits.length; i++) {
            var d = parseInt(digits.charAt(i), 10);
            sum += (i % 2 === 0) ? d : d * 3;
        }
        return String((10 - (sum % 10)) % 10);
    }

    function buildEanBits(digits) {
        var bits = '101', guards = [], i, d, par;
        for (i = 0; i < 3; i++) { guards.push(bits.length - 3 + i); }
        if (digits.length === 13) {
            par = EAN13_PAR[parseInt(digits.charAt(0), 10)].split('');
            for (i = 1; i <= 6; i++) {
                d = parseInt(digits.charAt(i), 10);
                bits += (par[i - 1] === 'G' ? G_PAT[d] : L_PAT[d]);
            }
        } else {
            for (i = 0; i < digits.length / 2; i++) { bits += L_PAT[parseInt(digits.charAt(i), 10)]; }
        }
        var mid = bits.length;
        bits += '01010';
        for (i = 0; i < 5; i++) { guards.push(mid + i); }
        var rightStart = digits.length === 13 ? 7 : digits.length / 2;
        for (i = rightStart; i < digits.length; i++) { bits += R_PAT[parseInt(digits.charAt(i), 10)]; }
        var end = bits.length;
        bits += '101';
        for (i = 0; i < 3; i++) { guards.push(end + i); }
        return { bits: bits, guards: guards, leftText: digits.length === 13 ? digits.slice(0, 7) : digits.slice(0, digits.length / 2), rightText: digits.length === 13 ? digits.slice(7) : digits.slice(digits.length / 2) };
    }

    function buildModules(type, data) {
        if (type === 'code128') {
            for (var i = 0; i < data.length; i++) {
                var c = data.charCodeAt(i);
                if (c < 32 || c > 126) { return { err: 'Code128: only ASCII 32-126 allowed.' }; }
            }
            var vals = [104], sum = 104;
            for (var k = 0; k < data.length; k++) {
                var v = data.charCodeAt(k) - 32;
                vals.push(v);
                sum += (k + 1) * v;
            }
            vals.push(sum % 103, 106);
            var mods = [], isGuard = [];
            vals.forEach(function (vv) {
                var pat = C128[vv];
                for (var q = 0; q < pat.length; q++) {
                    mods.push(parseInt(pat.charAt(q), 10));
                    isGuard.push(false);
                }
            });
            return { mods: mods, guards: isGuard, text: data };
        }
        if (type === 'code39') {
            var up = data.toUpperCase();
            for (var m = 0; m < up.length; m++) {
                if (!C39[up.charAt(m)]) { return { err: 'Code39: this character is not allowed: ' + up.charAt(m) }; }
            }
            var full = '*' + up + '*';
            var mods2 = [], g2 = [];
            for (var n2 = 0; n2 < full.length; n2++) {
                var p2 = C39[full.charAt(n2)];
                for (var r = 0; r < 9; r++) { mods2.push(p2.charAt(r) === 'w' ? 3 : 1); g2.push(false); }
                if (n2 < full.length - 1) { mods2.push(1); g2.push(false); }
            }
            return { mods: mods2, guards: g2, text: up };
        }
        var dig = data.replace(/\D/g, '');
        var need, full2;
        if (type === 'ean13') {
            if (!/^\d{12,13}$/.test(dig)) { return { err: 'Enter 12 or 13 digits for EAN-13.' }; }
            if (dig.length === 12) { dig += eanCheck(dig); }
            else if (eanCheck(dig.slice(0, 12)) !== dig.charAt(12)) { return { err: 'Wrong check digit. Enter 12 digits and the 13th is added automatically.' }; }
            var e = buildEanBits(dig);
            var mods3 = [], g3 = [];
            for (var s = 0; s < e.bits.length; s++) { mods3.push(1); g3.push(e.guards.indexOf(s) !== -1); }
            return { mods: mods3, guards: g3, text: dig, ean: e, ean13: true };
        }
        if (type === 'upca') {
            if (!/^\d{11,12}$/.test(dig)) { return { err: 'Enter 11 or 12 digits for UPC-A.' }; }
            if (dig.length === 11) { dig += eanCheck(dig); }
            else if (eanCheck(dig.slice(0, 11)) !== dig.charAt(11)) { return { err: 'Wrong check digit. Enter 11 digits.' }; }
            var e13 = buildEanBits('0' + dig);
            var mods4 = [], g4 = [];
            for (var s2 = 0; s2 < e13.bits.length; s2++) { mods4.push(1); g4.push(e13.guards.indexOf(s2) !== -1); }
            return { mods: mods4, guards: g4, text: dig, ean: { bits: e13.bits, guards: e13.guards, leftText: dig.slice(0, 6), rightText: dig.slice(6) }, ean13: true };
        }
        if (type === 'ean8') {
            if (!/^\d{7,8}$/.test(dig)) { return { err: 'Enter 7 or 8 digits for EAN-8.' }; }
            if (dig.length === 7) { dig += eanCheck(dig); }
            else if (eanCheck(dig.slice(0, 7)) !== dig.charAt(7)) { return { err: 'Wrong check digit. Enter 7 digits.' }; }
            var e8 = buildEanBits(dig);
            var mods5 = [], g5 = [];
            for (var s3 = 0; s3 < e8.bits.length; s3++) { mods5.push(1); g5.push(e8.guards.indexOf(s3) !== -1); }
            return { mods: mods5, guards: g5, text: dig, ean: e8, ean13: true };
        }
        return { err: 'Unknown type' };
    }

    function drawBarcode(res, scale) {
        var mods = res.mods, guards = res.guards;
        var total = mods.reduce(function (a, b) { return a + b; }, 0);
        var quiet = 10;
        var barH = 90, guardExtra = 12, textH = 26, pad = 8;
        var W = (total + quiet * 2) * scale;
        var H = barH + guardExtra + textH + pad * 2;
        bcCanvas.width = W;
        bcCanvas.height = H;
        var ctx = bcCanvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);
        ctx.fillStyle = '#000000';
        var x = quiet * scale, isBar = true;
        for (var i = 0; i < mods.length; i++) {
            var w = mods[i] * scale;
            if (isBar) {
                var h = guards[i] ? barH + guardExtra : barH;
                ctx.fillRect(x, pad, w, h);
            }
            x += w;
            isBar = !isBar;
        }
        ctx.fillStyle = '#000000';
        ctx.font = '20px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        if (res.ean13 && res.ean) {
            var y = pad + barH + guardExtra + 2;
            ctx.textAlign = 'left';
            ctx.fillText(res.ean.leftText.split('').join(' '), pad, y);
            ctx.textAlign = 'right';
            ctx.fillText(res.ean.rightText.split('').join(' '), W - pad, y);
        } else {
            ctx.fillText(res.text, W / 2, pad + barH + guardExtra + 2);
        }
        return { w: W, h: H };
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var type = typeSel.value;
        var data = dataInput.value.trim();
        if (!data) { showError('Please enter barcode data.'); return; }
        var res = buildModules(type, data);
        if (res.err) { showError(res.err); return; }
        var scale = parseInt(scaleRange.value, 10);
        drawBarcode(res, scale);
        bcInfo.textContent = 'Type: ' + typeSel.options[typeSel.selectedIndex].text + ' • Data: ' + res.text;
        results.classList.remove('d-none');
        dlBtn.onclick = function () {
            var a = document.createElement('a');
            a.href = bcCanvas.toDataURL('image/png');
            a.download = 'barcode-' + res.text.replace(/[^a-z0-9]+/gi, '-').toLowerCase() + '.png';
            document.body.appendChild(a);
            a.click();
            a.remove();
        };
    });
})();
</script>
@endsection
