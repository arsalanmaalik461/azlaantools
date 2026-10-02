@extends('layouts.app')

@section('title', 'Multiple Business Profiles - Azlaan Tools')
@section('meta_description', 'Keep the accounts of more than one shop in separate profiles — each profile has its own data.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Multiple Business Profiles</h1>
            <p class="lead text-muted">Have more than one shop? Create a <strong>separate profile</strong> for each shop — each profile's records and notes stay fully separate.</p>

            <div class="alert alert-info" role="alert">
                <strong>How it works:</strong> each profile has its own <strong>key prefix</strong> (for example: <code>shop1</code>, <code>shop2</code>). The account tools of Azlaan Tools keep their data separate with this prefix — for example <code>azlaan7_shop1_ledger</code> vs <code>azlaan7_shop2_ledger</code>. As soon as you switch profile, each tool shows that profile's data.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Active profile</h5>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <label for="pfActive" class="form-label fw-semibold">Which shop's account do you want to open?</label>
                            <select class="form-select" id="pfActive"></select>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-secondary" id="pfRename">Rename</button>
                                <button type="button" class="btn btn-outline-danger" id="pfDelete">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Create a new profile</h5>
                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label for="pfName" class="form-label fw-semibold">Profile name</label>
                            <input type="text" class="form-control" id="pfName" placeholder="Example: City Center Shop">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="pfPrefix" class="form-label fw-semibold">Key prefix (auto)</label>
                            <input type="text" class="form-control" id="pfPrefix" placeholder="Will be auto-created">
                            <div class="form-text">Only a–z, 0–9 and underscore. If left empty, it will be auto-created from the name.</div>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="pfCreate">Create Profile</button>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">All profiles</h5>
            <div id="pfList"></div>
            <p class="text-muted small" id="pfEmpty">No profiles yet — create your first profile above.</p>

            <h2>How to use</h2>
            <ol>
                <li>Use <strong>Create Profile</strong> to make a separate profile for each shop / business.</li>
                <li>Select the <strong>active profile</strong> — the account tools will show that profile's data.</li>
                <li>You can also write <strong>notes</strong> with each profile (for example: shop address, manager name).</li>
                <li>Use <strong>Rename</strong> to change the name, and <strong>Delete</strong> to remove the profile and its notes.</li>
            </ol>
            <p class="text-muted small">Note: data is saved only in your browser, it is never uploaded.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var KEY = 'azlaan7_profiles';
    var pfActive = document.getElementById('pfActive');
    var pfRename = document.getElementById('pfRename');
    var pfDelete = document.getElementById('pfDelete');
    var pfName = document.getElementById('pfName');
    var pfPrefix = document.getElementById('pfPrefix');
    var pfCreate = document.getElementById('pfCreate');
    var pfList = document.getElementById('pfList');
    var pfEmpty = document.getElementById('pfEmpty');
    var errorBox = document.getElementById('errorBox');

    var data = { profiles: [], active: null };

    try {
        var raw = localStorage.getItem(KEY);
        if (raw) {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.profiles) data = parsed;
        }
    } catch (e) { data = { profiles: [], active: null }; }

    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) { /* ignore */ }
    }
    function uid() {
        return 'b' + Date.now().toString(36) + Math.floor(Math.random() * 1000000).toString(36);
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
    function autoPrefix(name) {
        var p = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
        if (!p) p = 'profile';
        return p.slice(0, 24);
    }
    function prefixTaken(p, exceptId) {
        for (var i = 0; i < data.profiles.length; i++) {
            if (data.profiles[i].prefix === p && data.profiles[i].id !== exceptId) return true;
        }
        return false;
    }
    function getActive() {
        for (var i = 0; i < data.profiles.length; i++) {
            if (data.profiles[i].id === data.active) return data.profiles[i];
        }
        return null;
    }

    function render() {
        hideError();
        pfActive.innerHTML = '';
        if (!data.profiles.length) {
            var opt0 = document.createElement('option');
            opt0.textContent = 'No profiles';
            pfActive.appendChild(opt0);
            pfActive.disabled = true;
        } else {
            pfActive.disabled = false;
            data.profiles.forEach(function (pr) {
                var opt = document.createElement('option');
                opt.value = pr.id;
                opt.textContent = pr.name + ' (' + pr.prefix + ')';
                if (pr.id === data.active) opt.selected = true;
                pfActive.appendChild(opt);
            });
        }

        pfList.innerHTML = '';
        pfEmpty.style.display = data.profiles.length ? 'none' : '';
        data.profiles.forEach(function (pr) {
            var card = document.createElement('div');
            card.className = 'card shadow-sm mb-3' + (pr.id === data.active ? ' border-primary' : '');
            var body = document.createElement('div');
            body.className = 'card-body';

            var head = document.createElement('div');
            head.className = 'd-flex justify-content-between align-items-center mb-2 flex-wrap gap-2';
            var title = document.createElement('div');
            title.innerHTML = '<strong>' + esc(pr.name) + '</strong> ' +
                '<code class="small">' + esc(pr.prefix) + '</code> ' +
                (pr.id === data.active ? '<span class="badge bg-primary">Active</span>' : '');
            var sw = document.createElement('button');
            sw.type = 'button';
            sw.className = 'btn btn-sm btn-outline-primary';
            sw.textContent = 'Make active';
            sw.disabled = pr.id === data.active;
            sw.addEventListener('click', function () {
                data.active = pr.id;
                save(); render();
            });
            head.appendChild(title);
            head.appendChild(sw);

            var lab = document.createElement('label');
            lab.className = 'form-label fw-semibold small';
            lab.textContent = 'Notes (example: address, manager, timing)';
            lab.setAttribute('for', 'notes_' + pr.id);
            var ta = document.createElement('textarea');
            ta.className = 'form-control form-control-sm';
            ta.rows = 2;
            ta.id = 'notes_' + pr.id;
            ta.placeholder = 'Write a note about this profile...';
            ta.value = pr.notes || '';
            ta.addEventListener('change', function () {
                pr.notes = ta.value.trim();
                save();
            });

            var hint = document.createElement('div');
            hint.className = 'form-text mt-2';
            hint.innerHTML = 'Data namespace: <code>azlaan7_' + esc(pr.prefix) + '_*</code> — account tools keep their data separate with this prefix.';

            body.appendChild(head);
            body.appendChild(lab);
            body.appendChild(ta);
            body.appendChild(hint);
            card.appendChild(body);
            pfList.appendChild(card);
        });
    }

    pfCreate.addEventListener('click', function () {
        hideError();
        var name = pfName.value.trim();
        if (!name) { showError('Enter the profile name.'); return; }
        var prefix = pfPrefix.value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '');
        if (!prefix) prefix = autoPrefix(name);
        if (!/^[a-z]/.test(prefix)) prefix = 'p_' + prefix;
        var base = prefix, n = 2;
        while (prefixTaken(prefix, null)) {
            prefix = base + '_' + n;
            n++;
        }
        var pr = { id: uid(), name: name, prefix: prefix, notes: '' };
        data.profiles.push(pr);
        if (!data.active) data.active = pr.id;
        save();
        pfName.value = '';
        pfPrefix.value = '';
        render();
    });

    pfActive.addEventListener('change', function () {
        hideError();
        data.active = pfActive.value || null;
        save();
        render();
    });

    pfRename.addEventListener('click', function () {
        hideError();
        var pr = getActive();
        if (!pr) { showError('Create a profile first.'); return; }
        var nv = prompt('New name:', pr.name);
        if (nv === null) return;
        nv = nv.trim();
        if (!nv) { showError('Name cannot be empty.'); return; }
        pr.name = nv;
        save(); render();
    });

    pfDelete.addEventListener('click', function () {
        hideError();
        var pr = getActive();
        if (!pr) { showError('Create a profile first.'); return; }
        if (!confirm('"' + pr.name + '" — delete this profile? (Only the profile and its notes will be deleted.)')) return;
        data.profiles = data.profiles.filter(function (x) { return x.id !== pr.id; });
        if (data.active === pr.id) data.active = data.profiles.length ? data.profiles[0].id : null;
        save(); render();
    });

    render();
})();
</script>
@endsection
