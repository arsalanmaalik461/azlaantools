@extends('layouts.app')

@section('title', 'Image Metadata Viewer - Azlaan Tools')
@section('meta_description', 'Read any photo EXIF data right in your browser: camera model, date taken, GPS location and settings. Free online tool.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Image Metadata Viewer</h1>
            <p class="lead text-muted">See the hidden data of your photo: camera model, date taken, location (GPS) and camera settings. The file is read in your browser only and is never uploaded anywhere.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imgFile" class="form-label fw-semibold">Select a photo (JPG / PNG)</label>
                        <input type="file" class="form-control" id="imgFile" accept="image/jpeg,image/png">
                        <div class="form-text">It is processed only on your device and never goes to a server.</div>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="goBtn">Read Metadata</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>

                    <div id="results" class="d-none mt-4">
                        <div class="row">
                            <div class="col-md-5 text-center mb-3">
                                <img id="previewImg" class="img-fluid rounded border" alt="Selected photo preview">
                            </div>
                            <div class="col-md-7">
                                <h5>Basic Info</h5>
                                <table class="table table-sm table-striped">
                                    <tbody id="basicInfo"></tbody>
                                </table>
                            </div>
                        </div>
                        <h5 class="mt-3">EXIF / Detail Data</h5>
                        <div id="exifWrap">
                            <table class="table table-sm table-striped table-bordered">
                                <tbody id="exifRows"></tbody>
                            </table>
                        </div>
                        <div class="alert alert-info d-none" id="noExif">No EXIF data was found in this photo. Screenshots, forwarded photos and edited photos often lose their EXIF data.</div>
                        <p class="text-muted small">Note: Think before sharing photos with GPS location online — the location data goes along with the photo.</p>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Select your JPG or PNG photo.</li>
                <li>Press the <strong>Read Metadata</strong> button.</li>
                <li>See the camera, date, location and settings in the table below.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var fileInput = document.getElementById('imgFile');
    var goBtn = document.getElementById('goBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var previewImg = document.getElementById('previewImg');
    var basicInfo = document.getElementById('basicInfo');
    var exifRows = document.getElementById('exifRows');
    var noExif = document.getElementById('noExif');
    var exifWrap = document.getElementById('exifWrap');

    var TAG_NAMES = {
        0x010F: 'Camera Make', 0x0110: 'Camera Model', 0x0112: 'Orientation',
        0x0131: 'Software', 0x0132: 'Date Modified', 0x8298: 'Copyright',
        0x0100: 'Image Width', 0x0101: 'Image Height',
        0x8769: 'EXIF Sub-IFD', 0x8825: 'GPS Sub-IFD'
    };
    var EXIF_TAG_NAMES = {
        0x829A: 'Exposure Time', 0x829D: 'F-Number', 0x8827: 'ISO Speed',
        0x9003: 'Date Taken', 0x9004: 'Date Digitized', 0x920A: 'Focal Length',
        0xA001: 'Color Space', 0xA002: 'Pixel X Dimension', 0xA003: 'Pixel Y Dimension',
        0xA434: 'Lens Model', 0x010F: 'Lens Make'
    };
    var ORIENT = { 1: 'Normal', 2: 'Mirror horizontal', 3: 'Rotate 180', 4: 'Mirror vertical', 5: 'Mirror horizontal, rotate 270', 6: 'Rotate 90 CW', 7: 'Mirror horizontal, rotate 90', 8: 'Rotate 270 CW' };
    var TYPE_SIZE = { 1: 1, 2: 1, 3: 2, 4: 4, 5: 8, 7: 1, 9: 4, 10: 8, 16: 8 };

    function u16(dv, off, le) { return dv.getUint16(off, le); }
    function u32(dv, off, le) { return dv.getUint32(off, le); }

    function rationalStr(dv, off, le) {
        var n = u32(dv, off, le), d = u32(dv, off + 4, le);
        if (d === 0) return String(n);
        if (n === 1) return '1/' + d;
        return (n / d).toFixed(2);
    }

    function readValue(dv, tiffStart, le, type, count, dataOff) {
        var i, out = [];
        if (type === 2) {
            var s = '';
            for (i = 0; i < count; i++) {
                var c = dv.getUint8(dataOff + i);
                if (c === 0) break;
                s += String.fromCharCode(c);
            }
            return s;
        }
        if (type === 5 || type === 10) {
            for (i = 0; i < count; i++) out.push(rationalStr(dv, dataOff + i * 8, le));
            return count === 1 ? out[0] : out.join(', ');
        }
        for (i = 0; i < count; i++) {
            if (type === 3) out.push(u16(dv, dataOff + i * 2, le));
            else if (type === 4) out.push(u32(dv, dataOff + i * 4, le));
            else if (type === 1 || type === 7) out.push(dv.getUint8(dataOff + i));
            else out.push('?');
        }
        return count === 1 ? String(out[0]) : out.join(', ');
    }

    function readIFD(dv, tiffStart, ifdOff, le, names) {
        var rows = [], subs = {};
        var count = u16(dv, tiffStart + ifdOff, le);
        var i;
        for (i = 0; i < count; i++) {
            var e = tiffStart + ifdOff + 2 + i * 12;
            var tag = u16(dv, e, le), type = u16(dv, e + 2, le), cnt = u32(dv, e + 4, le);
            var size = (TYPE_SIZE[type] || 0) * cnt;
            var valOff = size <= 4 ? e + 8 : tiffStart + u32(dv, e + 8, le);
            if (tag === 0x8769 || tag === 0x8825) {
                subs[tag] = tiffStart + u32(dv, e + 8, le);
                continue;
            }
            var label = names[tag] || ('Tag 0x' + tag.toString(16).toUpperCase());
            var val = readValue(dv, tiffStart, le, type, cnt, valOff);
            if (tag === 0x0112 && ORIENT[val]) val = ORIENT[val];
            if (tag === 0x829A && val !== '') val = val + ' sec';
            if (tag === 0x829D && val !== '') val = 'f/' + val;
            if (tag === 0x920A && val !== '') val = val + ' mm';
            rows.push({ label: label, value: String(val) });
        }
        return { rows: rows, subs: subs };
    }

    function gpsDecimal(ref, dms) {
        var parts = String(dms).split(',').map(function (p) {
            p = p.trim();
            if (p.indexOf('/') > -1) { var q = p.split('/'); return parseFloat(q[0]) / parseFloat(q[1]); }
            return parseFloat(p);
        });
        var dec = parts[0] + parts[1] / 60 + parts[2] / 3600;
        if (ref === 'S' || ref === 'W') dec = -dec;
        return dec.toFixed(6);
    }

    function readGPS(dv, tiffStart, ifdOff, le) {
        var rows = [];
        var count = u16(dv, tiffStart + ifdOff, le);
        var tags = {}, i;
        for (i = 0; i < count; i++) {
            var e = tiffStart + ifdOff + 2 + i * 12;
            var tag = u16(dv, e, le), type = u16(dv, e + 2, le), cnt = u32(dv, e + 4, le);
            var size = (TYPE_SIZE[type] || 0) * cnt;
            var valOff = size <= 4 ? e + 8 : tiffStart + u32(dv, e + 8, le);
            tags[tag] = readValue(dv, tiffStart, le, type, cnt, valOff);
        }
        if (tags[2] && tags[4]) {
            var lat = gpsDecimal(tags[1] || 'N', tags[2]);
            var lon = gpsDecimal(tags[3] || 'E', tags[4]);
            rows.push({ label: 'GPS Latitude', value: lat + ' deg' });
            rows.push({ label: 'GPS Longitude', value: lon + ' deg' });
            rows.push({ label: 'GPS Map Link', value: 'https://www.google.com/maps?q=' + lat + ',' + lon });
        }
        if (tags[6]) rows.push({ label: 'GPS Altitude', value: String(tags[6]) + ' m' });
        return rows;
    }

    function parseTiff(dv, tiffStart) {
        var order = dv.getUint16(tiffStart);
        var le = order === 0x4949;
        if (!le && order !== 0x4D4D) return [];
        if (u16(dv, tiffStart + 2, le) !== 42) return [];
        var ifd0 = u32(dv, tiffStart + 4, le);
        var main = readIFD(dv, tiffStart, ifd0, le, TAG_NAMES);
        var rows = main.rows;
        if (main.subs[0x8769]) {
            var ex = readIFD(dv, tiffStart, main.subs[0x8769] - tiffStart, le, EXIF_TAG_NAMES);
            rows = rows.concat(ex.rows);
        }
        if (main.subs[0x8825]) {
            rows = rows.concat(readGPS(dv, tiffStart, main.subs[0x8825] - tiffStart, le));
        }
        return rows;
    }

    function exifFromJpeg(dv) {
        if (dv.getUint16(0) !== 0xFFD8) return null;
        var off = 2;
        while (off + 4 < dv.byteLength) {
            if (dv.getUint8(off) !== 0xFF) break;
            var marker = dv.getUint8(off + 1);
            off += 2;
            if (marker === 0xD8 || marker === 0xD9) continue;
            if (marker === 0x01 || (marker >= 0xD0 && marker <= 0xD7)) continue;
            var len = dv.getUint16(off);
            if (len < 2) break;
            if (marker === 0xE1 && dv.getUint32(off + 2) === 0x45786966 && dv.getUint16(off + 6) === 0) {
                return parseTiff(dv, off + 8);
            }
            off += len;
        }
        return null;
    }

    function exifFromPng(dv) {
        var sig = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A];
        var i;
        for (i = 0; i < 8; i++) if (dv.getUint8(i) !== sig[i]) return null;
        var rows = [], off = 8;
        while (off + 8 < dv.byteLength) {
            var len = dv.getUint32(off);
            var type = String.fromCharCode(dv.getUint8(off + 4), dv.getUint8(off + 5), dv.getUint8(off + 6), dv.getUint8(off + 7));
            var dataOff = off + 8;
            if (type === 'IHDR') {
                rows.push({ label: 'PNG Width', value: dv.getUint32(dataOff) + ' px' });
                rows.push({ label: 'PNG Height', value: dv.getUint32(dataOff + 4) + ' px' });
                rows.push({ label: 'Bit Depth', value: String(dv.getUint8(dataOff + 8)) });
                rows.push({ label: 'Color Type', value: String(dv.getUint8(dataOff + 9)) });
            } else if (type === 'eXIf') {
                var buf = dv.buffer.slice(dataOff, dataOff + len);
                var sub = parseTiff(new DataView(buf), 0);
                rows = rows.concat(sub);
            } else if (type === 'tEXt') {
                var kw = '', tx = '', j = dataOff;
                while (j < dataOff + len && dv.getUint8(j) !== 0) { kw += String.fromCharCode(dv.getUint8(j)); j++; }
                j++;
                while (j < dataOff + len) { tx += String.fromCharCode(dv.getUint8(j)); j++; }
                if (kw) rows.push({ label: 'PNG Text: ' + kw, value: tx.substring(0, 200) });
            } else if (type === 'IEND') {
                break;
            }
            off += 12 + len;
        }
        return rows;
    }

    function fmtSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    goBtn.addEventListener('click', function () {
        hideError();
        var f = fileInput.files[0];
        if (!f) { showError('Please select a photo first.'); return; }
        if (f.type !== 'image/jpeg' && f.type !== 'image/png') { showError('Please select only a JPG or PNG photo.'); return; }
        var url = URL.createObjectURL(f);
        previewImg.src = url;
        basicInfo.innerHTML =
            '<tr><th>File Name</th><td>' + esc(f.name) + '</td></tr>' +
            '<tr><th>File Size</th><td>' + fmtSize(f.size) + '</td></tr>' +
            '<tr><th>File Type</th><td>' + esc(f.type) + '</td></tr>' +
            '<tr><th>Dimensions</th><td id="dimCell">Reading...</td></tr>';
        previewImg.onload = function () {
            var d = document.getElementById('dimCell');
            if (d) d.textContent = previewImg.naturalWidth + ' x ' + previewImg.naturalHeight + ' px';
        };
        var reader = new FileReader();
        reader.onload = function (e) {
            try {
                var dv = new DataView(e.target.result);
                var rows = f.type === 'image/jpeg' ? exifFromJpeg(dv) : exifFromPng(dv);
                exifRows.innerHTML = '';
                if (rows && rows.length) {
                    noExif.classList.add('d-none');
                    exifWrap.classList.remove('d-none');
                    rows.forEach(function (r) {
                        var tr = document.createElement('tr');
                        var th = document.createElement('th');
                        th.style.width = '40%';
                        th.textContent = r.label;
                        var td = document.createElement('td');
                        if (String(r.value).indexOf('https://www.google.com/maps') === 0) {
                            var a = document.createElement('a');
                            a.href = r.value;
                            a.target = '_blank';
                            a.rel = 'noopener';
                            a.textContent = 'View on Google Maps';
                            td.appendChild(a);
                        } else {
                            td.textContent = r.value;
                        }
                        tr.appendChild(th);
                        tr.appendChild(td);
                        exifRows.appendChild(tr);
                    });
                } else {
                    exifWrap.classList.add('d-none');
                    noExif.classList.remove('d-none');
                }
                results.classList.remove('d-none');
            } catch (err) {
                showError('There was a problem reading the file. Please try another photo.');
            }
        };
        reader.onerror = function () { showError('The file could not be read.'); };
        reader.readAsArrayBuffer(f);
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
        results.classList.add('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
})();
</script>
@endsection
