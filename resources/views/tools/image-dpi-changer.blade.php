@extends('layouts.app')
@section('title', 'Image DPI Changer — Free Online Tool')
@section('meta_description', 'Change the DPI of an image to 300 DPI for printing needs')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3">Image DPI Changer</h1>
            <p class="lead small text-muted">Set the DPI value written into your PNG or JPG file, for example 300 DPI for print, without changing pixel size.</p>

                    <label class="form-label fw-semibold" for="dpFile">Choose a PNG or JPG image</label>
                    <input type="file" id="dpFile" class="form-control" accept="image/png,image/jpeg">
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label" for="dpVal">Target DPI</label><input type="number" id="dpVal" class="form-control" value="300" min="1" max="2400"></div>
                        <div class="col-md-6 d-flex align-items-end"><button type="button" id="dpGo" class="btn btn-primary w-100" disabled>Set DPI and Download</button></div>
                    </div>
                    <p class="small text-muted mt-2 mb-0" id="dpInfo">No image loaded yet.</p>

            <div id="dpMsg" class="alert alert-info d-none mt-3" role="alert"></div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">How to use</h2>
            <ol>
                    <li>Upload a PNG or JPG image.</li>
                    <li>Enter the target DPI, usually 300 for printing.</li>
                    <li>Download the same image with the new DPI written into its file metadata.</li>
            </ol>
            <p class="small text-muted mb-0">DPI here is file metadata that tells printers how large to print the pixels. It does not add detail or change the pixel dimensions.</p>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
(function () {
    'use strict';

    var msgBox = document.getElementById("dpMsg");
    function showMsg(t, ok) { msgBox.textContent = t; msgBox.className = "alert mt-3 " + (ok === false ? "alert-danger" : "alert-info"); msgBox.classList.remove("d-none"); }

    function dl(blob, name) {
        var a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 3000);
    }
    function fmtBytes(b) {
        if (!b && b !== 0) return "-";
        if (b < 1024) return b + " B";
        if (b < 1048576) return (b / 1024).toFixed(1) + " KB";
        return (b / 1048576).toFixed(2) + " MB";
    }

    var imgFile = null;
    var crcTable = null;
    function crc32(bytes, start, len) {
        if (!crcTable) { crcTable = []; for (var n = 0; n < 256; n++) { var c = n; for (var k = 0; k < 8; k++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1); crcTable[n] = c >>> 0; } }
        var crc = 0xFFFFFFFF;
        for (var i = start; i < start + len; i++) crc = crcTable[(crc ^ bytes[i]) & 0xFF] ^ (crc >>> 8);
        return (crc ^ 0xFFFFFFFF) >>> 0;
    }
    function setPngDpi(u8, dpi) {
        var ppm = Math.round(dpi / 0.0254);
        var chunk = new Uint8Array(21);
        var dv = new DataView(chunk.buffer);
        dv.setUint32(0, 9); chunk[4] = 0x70; chunk[5] = 0x48; chunk[6] = 0x59; chunk[7] = 0x73;
        dv.setUint32(8, ppm); dv.setUint32(12, ppm); chunk[16] = 1;
        dv.setUint32(17, crc32(chunk, 4, 13));
        var pos = 8;
        while (pos + 8 <= u8.length) {
            var len = new DataView(u8.buffer, u8.byteOffset + pos, 4).getUint32(0);
            var type = String.fromCharCode(u8[pos + 4], u8[pos + 5], u8[pos + 6], u8[pos + 7]);
            if (type === "IDAT") break;
            pos += 12 + len;
        }
        var out = new Uint8Array(u8.length + 21);
        out.set(u8.subarray(0, pos), 0); out.set(chunk, pos); out.set(u8.subarray(pos), pos + 21);
        return out;
    }
    function setJpegDpi(u8, dpi) {
        if (u8.length > 20 && u8[0] === 0xFF && u8[1] === 0xD8 && u8[2] === 0xFF && u8[3] === 0xE0) {
            var out = new Uint8Array(u8); out[13] = 1; var dv = new DataView(out.buffer, out.byteOffset); dv.setUint16(14, dpi); dv.setUint16(16, dpi); return out;
        }
        var seg = new Uint8Array(20);
        seg[0] = 0xFF; seg[1] = 0xE0; var sd = new DataView(seg.buffer); sd.setUint16(2, 16);
        var id = "JFIF"; for (var i = 0; i < 4; i++) seg[4 + i] = id.charCodeAt(i);
        seg[8] = 0; seg[9] = 1; seg[10] = 2; seg[11] = 1; sd.setUint16(12, dpi); sd.setUint16(14, dpi); seg[16] = 0; seg[17] = 0; seg[18] = 0; seg[19] = 0;
        var res = new Uint8Array(u8.length + 18);
        res.set(u8.subarray(0, 2), 0); res.set(seg.subarray(0, 18), 2); res.set(u8.subarray(2), 20);
        return res;
    }
    document.getElementById("dpFile").addEventListener("change", function () {
        imgFile = this.files && this.files[0];
        if (!imgFile) return;
        document.getElementById("dpGo").disabled = false;
        document.getElementById("dpInfo").textContent = "Loaded: " + imgFile.name + " (" + fmtBytes(imgFile.size) + ", " + imgFile.type + ").";
    });
    document.getElementById("dpGo").addEventListener("click", function () {
        if (!imgFile) { showMsg("Choose an image first.", false); return; }
        var dpi = parseInt(document.getElementById("dpVal").value, 10);
        if (!dpi || dpi < 1) { showMsg("Enter a valid DPI value.", false); return; }
        imgFile.arrayBuffer().then(function (ab) {
            var u8 = new Uint8Array(ab), out, isPng = imgFile.type === "image/png";
            try { out = isPng ? setPngDpi(u8, dpi) : setJpegDpi(u8, dpi); } catch (e) { showMsg("Could not rewrite this file. Try a standard PNG or JPG.", false); return; }
            dl(new Blob([out], { type: imgFile.type }), imgFile.name.replace(/\.[^.]+$/, "") + "-" + dpi + "dpi." + (isPng ? "png" : "jpg"));
            showMsg("Done. DPI metadata set to " + dpi + " in the downloaded file.");
        });
    });

})();
</script>
@endsection
