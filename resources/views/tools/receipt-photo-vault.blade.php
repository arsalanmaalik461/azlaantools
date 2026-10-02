@extends('layouts.app')

@section('title', 'Bill & Receipt Photo Vault - Azlaan Tools')
@section('meta_description', 'Attach a bill or receipt photo with every transaction. Free receipt photo vault — photos stay safe in your browser.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Bill & Receipt Photo Vault</h1>
            <p class="lead text-muted">Attach a bill or receipt photo with every transaction — proof along with your records. Data is saved only in your browser, never uploaded.</p>

            <div class="alert alert-info small">
                <strong>Privacy note:</strong> Photos are saved in your browser's <strong>IndexedDB</strong> (localStorage is too small for photos, so only details are saved there).
                Clearing browser data will delete these photos too — keep a separate backup of important bills.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Add a new receipt</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="rvDate" class="form-label fw-semibold">Date</label>
                            <input type="date" class="form-control" id="rvDate">
                        </div>
                        <div class="col-md-5">
                            <label for="rvParty" class="form-label fw-semibold">Party / shop name</label>
                            <input type="text" class="form-control" id="rvParty" placeholder="e.g. Ahmed Store">
                        </div>
                        <div class="col-md-4">
                            <label for="rvAmount" class="form-label fw-semibold">Amount (Rs)</label>
                            <input type="number" class="form-control" id="rvAmount" placeholder="0" min="0.01" step="0.01">
                        </div>
                        <div class="col-md-8">
                            <label for="rvNote" class="form-label fw-semibold">Note (optional)</label>
                            <input type="text" class="form-control" id="rvNote" placeholder="e.g. bought goods, electricity bill">
                        </div>
                        <div class="col-md-4">
                            <label for="rvPhoto" class="form-label fw-semibold">Bill photo</label>
                            <input type="file" class="form-control" id="rvPhoto" accept="image/*">
                        </div>
                    </div>
                    <div class="mt-2" id="photoPreviewWrap" style="display:none">
                        <img id="photoPreview" alt="Bill photo preview" class="img-thumbnail" style="max-height:160px">
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-3" id="addBtn">Save Receipt</button>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                    <div class="alert alert-success mt-3 d-none" id="okBox" role="status"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Vault (<span id="rvCount">0</span>)</h5>
                        <input type="text" class="form-control form-control-sm" id="rvSearch" placeholder="Search party / note..." style="max-width:240px">
                    </div>
                    <div class="row g-3" id="gallery"></div>
                    <p class="small text-muted mb-0 mt-2" id="galleryEmpty">No receipts yet. Add your first receipt above.</p>
                </div>
            </div>

            <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalTitle">Receipt</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="viewModalImg" alt="Receipt photo" class="img-fluid rounded">
                            <p class="mt-2 mb-0" id="viewModalMeta"></p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Enter the date, party name and amount, and select a <strong>photo</strong> of the bill.</li>
                <li>Press <strong>Save Receipt</strong> — the photo will be saved in your browser.</li>
                <li>In the gallery below, use <strong>View</strong> to see the photo larger, and <strong>Delete</strong> to remove it.</li>
            </ol>
            <p class="text-muted small">Note: photos stay safe only in this browser/device, they are never uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var META_KEY = 'azlaan7_receipt_vault';
    var DB_NAME = 'azlaan7_vault_db';
    var STORE = 'receipts';

    var rvDate = document.getElementById('rvDate');
    var rvParty = document.getElementById('rvParty');
    var rvAmount = document.getElementById('rvAmount');
    var rvNote = document.getElementById('rvNote');
    var rvPhoto = document.getElementById('rvPhoto');
    var photoPreview = document.getElementById('photoPreview');
    var photoPreviewWrap = document.getElementById('photoPreviewWrap');
    var addBtn = document.getElementById('addBtn');
    var errorBox = document.getElementById('errorBox');
    var okBox = document.getElementById('okBox');
    var gallery = document.getElementById('gallery');
    var galleryEmpty = document.getElementById('galleryEmpty');
    var rvCount = document.getElementById('rvCount');
    var rvSearch = document.getElementById('rvSearch');
    var viewModalTitle = document.getElementById('viewModalTitle');
    var viewModalImg = document.getElementById('viewModalImg');
    var viewModalMeta = document.getElementById('viewModalMeta');

    var stagedDataUrl = null;
    var db = null;

    function today() {
        var d = new Date();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + day;
    }
    rvDate.value = today();

    function fmt(n) {
        return 'Rs ' + Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function showOk(msg) {
        okBox.textContent = msg;
        okBox.classList.remove('d-none');
        setTimeout(function () { okBox.classList.add('d-none'); }, 2500);
    }
    function uid() {
        return 'rv' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
    }

    function openDb() {
        return new Promise(function (resolve, reject) {
            if (!window.indexedDB) { reject(new Error('IndexedDB is not supported.')); return; }
            var req = indexedDB.open(DB_NAME, 1);
            req.onupgradeneeded = function () {
                var d = req.result;
                if (!d.objectStoreNames.contains(STORE)) d.createObjectStore(STORE, { keyPath: 'id' });
            };
            req.onsuccess = function () { db = req.result; resolve(db); };
            req.onerror = function () { reject(req.error || new Error('Could not open the database.')); };
        });
    }
    function idbPut(rec) {
        return new Promise(function (resolve, reject) {
            var tx = db.transaction(STORE, 'readwrite');
            tx.objectStore(STORE).put(rec);
            tx.oncomplete = resolve;
            tx.onerror = function () { reject(tx.error); };
        });
    }
    function idbGet(id) {
        return new Promise(function (resolve, reject) {
            var tx = db.transaction(STORE, 'readonly');
            var req = tx.objectStore(STORE).get(id);
            req.onsuccess = function () { resolve(req.result); };
            req.onerror = function () { reject(req.error); };
        });
    }
    function idbDelete(id) {
        return new Promise(function (resolve, reject) {
            var tx = db.transaction(STORE, 'readwrite');
            tx.objectStore(STORE).delete(id);
            tx.oncomplete = resolve;
            tx.onerror = function () { reject(tx.error); };
        });
    }

    function loadMeta() {
        try {
            var raw = localStorage.getItem(META_KEY);
            if (raw) {
                var p = JSON.parse(raw);
                if (Array.isArray(p)) return p;
            }
        } catch (e) {}
        return [];
    }
    function saveMeta(m) {
        try { localStorage.setItem(META_KEY, JSON.stringify(m)); }
        catch (e) { showError('Could not save metadata — browser storage is blocked.'); }
    }

    function resizeImage(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () {
                var img = new Image();
                img.onload = function () {
                    var max = 1600;
                    var w = img.width, h = img.height;
                    if (w > max || h > max) {
                        var ratio = Math.min(max / w, max / h);
                        w = Math.round(w * ratio);
                        h = Math.round(h * ratio);
                    }
                    var canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    resolve(canvas.toDataURL('image/jpeg', 0.82));
                };
                img.onerror = function () { reject(new Error('Could not read the photo.')); };
                img.src = reader.result;
            };
            reader.onerror = function () { reject(new Error('Could not read the file.')); };
            reader.readAsDataURL(file);
        });
    }

    rvPhoto.addEventListener('change', function () {
        hideError();
        stagedDataUrl = null;
        photoPreviewWrap.style.display = 'none';
        var f = rvPhoto.files && rvPhoto.files[0];
        if (!f) return;
        if (!f.type || f.type.indexOf('image/') !== 0) {
            showError('Please select an image file only.');
            rvPhoto.value = '';
            return;
        }
        addBtn.disabled = true;
        addBtn.textContent = 'Preparing photo...';
        resizeImage(f).then(function (url) {
            stagedDataUrl = url;
            photoPreview.src = url;
            photoPreviewWrap.style.display = '';
            addBtn.disabled = false;
            addBtn.textContent = 'Save Receipt';
        }, function (err) {
            addBtn.disabled = false;
            addBtn.textContent = 'Save Receipt';
            showError(err.message);
        });
    });

    addBtn.addEventListener('click', function () {
        hideError();
        if (!rvDate.value) { showError('Please select a date.'); return; }
        var party = rvParty.value.trim();
        if (!party) { showError('Please enter the party / shop name.'); return; }
        var amt = parseFloat(rvAmount.value);
        if (isNaN(amt) || amt <= 0) { showError('Please enter a correct amount (more than 0).'); return; }
        if (!stagedDataUrl) { showError('Please select a bill photo.'); return; }
        var entry = {
            id: uid(),
            date: rvDate.value,
            party: party,
            amount: Math.round(amt * 100) / 100,
            note: rvNote.value.trim(),
            created: new Date().toLocaleString('en-PK')
        };
        idbPut({ id: entry.id, photo: stagedDataUrl }).then(function () {
            var meta = loadMeta();
            meta.unshift(entry);
            if (meta.length > 300) {
                var removed = meta.slice(300);
                meta.length = 300;
                removed.forEach(function (r) { idbDelete(r.id); });
            }
            saveMeta(meta);
            rvParty.value = '';
            rvAmount.value = '';
            rvNote.value = '';
            rvPhoto.value = '';
            stagedDataUrl = null;
            photoPreviewWrap.style.display = 'none';
            showOk('Receipt saved!');
            renderGallery();
        }, function () {
            showError('Could not save the photo — browser storage is blocked or space is low.');
        });
    });

    function matches(r, q) {
        if (!q) return true;
        q = q.toLowerCase();
        return (r.party || '').toLowerCase().indexOf(q) >= 0 ||
               (r.note || '').toLowerCase().indexOf(q) >= 0 ||
               (r.date || '').indexOf(q) >= 0;
    }

    function thumbFor(id, imgEl) {
        idbGet(id).then(function (rec) {
            if (rec && rec.photo) imgEl.src = rec.photo;
        }, function () {});
    }

    function renderGallery() {
        var meta = loadMeta();
        var q = rvSearch.value.trim();
        var list = meta.filter(function (r) { return matches(r, q); });
        rvCount.textContent = meta.length;
        gallery.innerHTML = '';
        galleryEmpty.style.display = list.length ? 'none' : '';
        list.forEach(function (r) {
            var col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            var card = document.createElement('div');
            card.className = 'card h-100 shadow-sm';
            var img = document.createElement('img');
            img.className = 'card-img-top';
            img.alt = 'Receipt photo';
            img.style.height = '140px';
            img.style.objectFit = 'cover';
            img.src = '';
            thumbFor(r.id, img);
            var body = document.createElement('div');
            body.className = 'card-body p-2';
            body.innerHTML = '<div class="fw-semibold small">' + esc(r.party) + '</div>' +
                '<div class="small text-primary fw-bold">' + fmt(r.amount) + '</div>' +
                '<div class="small text-muted">' + esc(r.date) + (r.note ? ' · ' + esc(r.note) : '') + '</div>';
            var foot = document.createElement('div');
            foot.className = 'card-footer p-2 d-flex gap-2';
            var viewB = document.createElement('button');
            viewB.type = 'button';
            viewB.className = 'btn btn-sm btn-outline-primary flex-fill';
            viewB.textContent = 'View';
            viewB.addEventListener('click', function () {
                idbGet(r.id).then(function (rec) {
                    viewModalTitle.textContent = r.party + ' — ' + fmt(r.amount);
                    viewModalImg.src = rec && rec.photo ? rec.photo : '';
                    viewModalMeta.textContent = r.date + (r.note ? ' · ' + r.note : '');
                    var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                    modal.show();
                }, function () { showError('Could not load the photo.'); });
            });
            var delB = document.createElement('button');
            delB.type = 'button';
            delB.className = 'btn btn-sm btn-outline-danger';
            delB.textContent = 'Delete';
            delB.addEventListener('click', function () {
                if (!confirm('Delete this receipt?')) return;
                idbDelete(r.id).then(function () {
                    saveMeta(loadMeta().filter(function (x) { return x.id !== r.id; }));
                    renderGallery();
                }, function () { showError('Could not delete.'); });
            });
            foot.appendChild(viewB);
            foot.appendChild(delB);
            card.appendChild(img);
            card.appendChild(body);
            card.appendChild(foot);
            col.appendChild(card);
            gallery.appendChild(col);
        });
    }

    rvSearch.addEventListener('input', renderGallery);

    openDb().then(function () {
        renderGallery();
    }, function (err) {
        showError(err.message);
    });
})();
</script>
@endsection
