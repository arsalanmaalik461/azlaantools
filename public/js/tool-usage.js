/* Azlaan Tools — personal tool usage tracker + "Top 10" renderer.
 * Usage data stays in this browser's localStorage only; nothing is sent anywhere.
 * Vanilla JS, no libraries. Everything wrapped so private-mode failures never break the page. */
(function () {
    'use strict';

    var KEY = 'azlaan_tool_usage_v1';
    var MAX_ENTRIES = 200;

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function readUsage() {
        try {
            var raw = localStorage.getItem(KEY);
            if (!raw) { return {}; }
            var obj = JSON.parse(raw);
            if (!obj || typeof obj !== 'object' || Array.isArray(obj)) { return {}; }
            return obj;
        } catch (e) {
            return {};
        }
    }

    function writeUsage(obj) {
        try {
            localStorage.setItem(KEY, JSON.stringify(obj));
        } catch (e) {
            /* private mode / quota — stay silent, keep the page working */
        }
    }

    /* ============ PART A — track visits on tool pages ============ */
    try {
        var m = /^\/tools\/([a-z0-9-]+)\/?$/.exec(location.pathname);
        if (m) {
            var slug = m[1];
            var usage = readUsage();
            var now = Date.now();
            var entry = usage[slug];
            if (!entry || typeof entry !== 'object' || typeof entry.c !== 'number') {
                entry = { c: 0, t: now };
            }
            entry.c = entry.c + 1;
            entry.t = now;
            usage[slug] = entry;

            var keys = Object.keys(usage);
            if (keys.length > MAX_ENTRIES) {
                keys.sort(function (a, b) {
                    var ta = (usage[a] && usage[a].t) || 0;
                    var tb = (usage[b] && usage[b].t) || 0;
                    return ta - tb;
                });
                for (var i = 0; i < keys.length - MAX_ENTRIES; i++) {
                    delete usage[keys[i]];
                }
            }
            writeUsage(usage);
        }
    } catch (e) {
        /* never break the page for analytics */
    }

    /* ============ PART B — homepage "top 10" ============ */
    var grid = document.getElementById('topToolsGrid');
    if (!grid) { return; }

    var hint = document.getElementById('topToolsHint');
    var clearBtn = document.getElementById('clearTopTools');
    var indexPromise = null;

    function loadIndex() {
        if (!indexPromise) {
            indexPromise = fetch('tools-index.json', { credentials: 'same-origin' })
                .then(function (r) {
                    if (!r.ok) { throw new Error('index http ' + r.status); }
                    return r.json();
                })
                .catch(function () { return []; });
        }
        return indexPromise;
    }

    function topSlugs(usage) {
        var keys = Object.keys(usage).filter(function (s) {
            var e = usage[s];
            return e && typeof e === 'object' && typeof e.c === 'number' && e.c > 0;
        });
        keys.sort(function (a, b) {
            var ea = usage[a], eb = usage[b];
            if (eb.c !== ea.c) { return eb.c - ea.c; }
            return ((eb.t || 0) - (ea.t || 0));
        });
        return keys.slice(0, 10);
    }

    function showEmpty() {
        grid.classList.add('d-none');
        grid.innerHTML = '';
        if (hint) { hint.classList.remove('d-none'); }
        if (clearBtn) { clearBtn.classList.add('d-none'); }
    }

    function showGrid() {
        grid.classList.remove('d-none');
        if (hint) { hint.classList.add('d-none'); }
        if (clearBtn) { clearBtn.classList.remove('d-none'); }
    }

    function cardHtml(tool, count) {
        return '<div class="col-6 col-md-4 col-lg-3">'
            + '<div class="tool-card p-3"><a href="/tools/' + esc(tool.slug) + '">'
            + '<div class="icon">' + tool.icon + '</div>'
            + '<h3 class="h6 fw-bold mt-2">' + esc(tool.name)
            + ' <span class="use-badge">Used ' + esc(String(count)) + ' times</span></h3>'
            + '<p class="small text-muted mb-0">' + esc(tool.desc) + '</p>'
            + '</a></div></div>';
    }

    function render() {
        var usage = readUsage();
        var slugs = topSlugs(usage);
        if (slugs.length === 0) { showEmpty(); return; }
        loadIndex().then(function (index) {
            var map = {};
            (index || []).forEach(function (t) {
                if (t && t.slug) { map[t.slug] = t; }
            });
            var html = '';
            var shown = 0;
            slugs.forEach(function (s) {
                var tool = map[s];
                if (!tool) { return; }
                var c = (usage[s] && usage[s].c) || 0;
                html += cardHtml(tool, c);
                shown++;
            });
            if (shown === 0) { showEmpty(); return; }
            grid.innerHTML = html;
            showGrid();
        });
    }

    render();

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            try { localStorage.removeItem(KEY); } catch (e) { /* ignore */ }
            render();
        });
    }
})();
