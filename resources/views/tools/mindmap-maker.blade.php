@extends('layouts.app')

@section('title', 'Mind Map Maker - Azlaan Tools')
@section('meta_description', 'Create visual mind maps for study and brainstorming online for free.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h1 class="mb-3">Mind Map Maker</h1>
            <p class="lead text-muted">Make a mind map of any topic — an easy way to remember ideas and brainstorm. Click a node to add branches.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-5">
                            <label for="rootInput" class="form-label fw-semibold">Central topic</label>
                            <input type="text" class="form-control" id="rootInput" placeholder="e.g. Solar System">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="childInput" class="form-label fw-semibold">New branch <span class="text-muted small" id="selLabel">(no node selected)</span></label>
                            <input type="text" class="form-control" id="childInput" placeholder="Branch name">
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-primary flex-fill" id="goBtn">Start / Add</button>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="renameBtn">Rename Selected</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="delBtn">Delete Selected</button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="svgBtn">SVG Download</button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="pngBtn">PNG Download</button>
                        <button type="button" class="btn btn-sm btn-outline-info" id="sampleBtn">Sample Map</button>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="clearBtn">Clear</button>
                    </div>
                    <div class="alert alert-danger mt-1 d-none" id="errorBox" role="alert"></div>
                    <div class="border rounded bg-light overflow-auto" style="max-height: 520px;">
                        <svg id="mapSvg" width="900" height="400" style="display:block; min-width:900px;"></svg>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Tip: Click a node to select it, then add a branch.</p>

                    <div id="results" class="d-none mt-3">
                        <div class="alert alert-info py-2 mb-0 small" id="statLine"></div>
                    </div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Type your central topic and press <strong>Start</strong>.</li>
                <li>Click a node (it turns blue), then type the branch name and press <strong>Add</strong>.</li>
                <li>When your map is ready, download it as <strong>SVG</strong> or <strong>PNG</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    var rootInput = document.getElementById('rootInput');
    var childInput = document.getElementById('childInput');
    var goBtn = document.getElementById('goBtn');
    var renameBtn = document.getElementById('renameBtn');
    var delBtn = document.getElementById('delBtn');
    var svgBtn = document.getElementById('svgBtn');
    var pngBtn = document.getElementById('pngBtn');
    var sampleBtn = document.getElementById('sampleBtn');
    var clearBtn = document.getElementById('clearBtn');
    var errorBox = document.getElementById('errorBox');
    var results = document.getElementById('results');
    var statLine = document.getElementById('statLine');
    var selLabel = document.getElementById('selLabel');
    var svg = document.getElementById('mapSvg');

    var NS = 'http://www.w3.org/2000/svg';
    var COLORS = ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#d63384', '#20c997', '#dc3545', '#0dcaf0'];
    var XSTEP = 230, YSTEP = 64, PADX = 40, PADY = 40;

    var root = null, selected = null, uid = 0;

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
    function mkNode(label) {
        return { id: ++uid, label: label, children: [], parent: null, x: 0, y: 0, h: 1, color: null };
    }
    function count(n) {
        var c = 1;
        n.children.forEach(function (k) { c += count(k); });
        return c;
    }
    function layout(n, depth) {
        n.x = PADX + depth * XSTEP;
        if (!n.children.length) { n.h = 1; return 1; }
        var total = 0, i;
        for (i = 0; i < n.children.length; i++) total += layout(n.children[i], depth + 1);
        n.h = total;
        return total;
    }
    function place(n, topY) {
        if (!n.children.length) { n.y = topY; return topY + 1; }
        var y = topY, i;
        for (i = 0; i < n.children.length; i++) y = place(n.children[i], y);
        n.y = topY + (y - topY - 1) / 2;
        return y;
    }
    function el(tag, attrs) {
        var e = document.createElementNS(NS, tag);
        for (var k in attrs) e.setAttribute(k, attrs[k]);
        return e;
    }
    function textW(s) { return Math.max(70, s.length * 7.5 + 28); }

    function render() {
        while (svg.firstChild) svg.removeChild(svg.firstChild);
        if (!root) {
            var t = el('text', { x: 450, y: 200, 'text-anchor': 'middle', fill: '#adb5bd', 'font-size': 16 });
            t.textContent = 'Type a topic and press Start — your mind map will appear here';
            svg.appendChild(t);
            svg.setAttribute('width', 900);
            svg.setAttribute('height', 400);
            return;
        }
        layout(root, 0);
        place(root, 0);
        var maxX = 0, maxY = 0;
        (function bounds(n) {
            maxX = Math.max(maxX, n.x + textW(n.label));
            maxY = Math.max(maxY, (n.y + 1) * YSTEP);
            n.children.forEach(bounds);
        })(root);
        var W = Math.max(900, maxX + PADX), H = Math.max(400, maxY + PADY);
        svg.setAttribute('width', W);
        svg.setAttribute('height', H);
        svg.style.minWidth = W + 'px';

        function draw(n, depth) {
            var cx = n.x + textW(n.label) / 2;
            var cy = PADY + n.y * YSTEP + 18;
            n._cx = cx; n._cy = cy;
            n.children.forEach(function (k) {
                draw(k, depth + 1);
                var kx = k.x, ky = PADY + k.y * YSTEP + 18;
                var path = el('path', {
                    d: 'M ' + cx + ' ' + cy + ' C ' + (cx + 60) + ' ' + cy + ', ' + (kx - 60) + ' ' + ky + ', ' + kx + ' ' + ky,
                    fill: 'none', stroke: k.color || '#6c757d', 'stroke-width': 2.5
                });
                svg.appendChild(path);
            });
            var w = textW(n.label);
            var g = el('g', { style: 'cursor:pointer' });
            var isSel = selected === n;
            var rect = el('rect', {
                x: n.x, y: PADY + n.y * YSTEP, width: w, height: 36, rx: 18,
                fill: depth === 0 ? '#212529' : (n.color || '#6c757d'),
                stroke: isSel ? '#ffc107' : 'none', 'stroke-width': isSel ? 4 : 0
            });
            var tx = el('text', {
                x: n.x + w / 2, y: PADY + n.y * YSTEP + 23, 'text-anchor': 'middle',
                fill: '#ffffff', 'font-size': 13, 'font-family': 'sans-serif'
            });
            tx.textContent = n.label;
            g.appendChild(rect);
            g.appendChild(tx);
            g.addEventListener('click', function () {
                selected = n;
                selLabel.textContent = '(selected: ' + n.label + ')';
                hideError();
                render();
            });
            svg.appendChild(g);
        }
        draw(root, 0);
        statLine.textContent = 'Total ' + count(root) + ' nodes. New branches will be added to the selected node.';
        results.classList.remove('d-none');
    }

    function ensureRoot() {
        if (root) return true;
        var t = rootInput.value.trim();
        if (!t) { showError('Please enter a central topic first.'); return false; }
        root = mkNode(t);
        root.color = '#212529';
        selected = root;
        selLabel.textContent = '(selected: ' + t + ')';
        rootInput.value = '';
        return true;
    }

    goBtn.addEventListener('click', function () {
        hideError();
        if (!root) {
            if (ensureRoot()) render();
            return;
        }
        var c = childInput.value.trim();
        if (!c) { showError('Please enter a branch name.'); return; }
        if (c.length > 40) { showError('Please keep the branch name under 40 characters.'); return; }
        var target = selected || root;
        var node = mkNode(c);
        node.parent = target;
        if (target === root || !target.parent) {
            node.color = COLORS[target.children.length % COLORS.length];
        } else {
            node.color = target.color;
        }
        target.children.push(node);
        selected = node;
        selLabel.textContent = '(selected: ' + c + ')';
        childInput.value = '';
        childInput.focus();
        render();
    });

    renameBtn.addEventListener('click', function () {
        hideError();
        if (!selected) { showError('Please select a node first.'); return; }
        var v = prompt('Enter a new name:', selected.label);
        if (v && v.trim()) {
            selected.label = v.trim().slice(0, 40);
            selLabel.textContent = '(selected: ' + selected.label + ')';
            render();
        }
    });

    delBtn.addEventListener('click', function () {
        hideError();
        if (!selected) { showError('Please select a node first.'); return; }
        if (selected === root) {
            if (confirm('Delete the whole map? Are you sure?')) { root = null; selected = null; selLabel.textContent = '(no node selected)'; render(); }
            return;
        }
        var p = selected.parent;
        p.children = p.children.filter(function (k) { return k !== selected; });
        selected = p;
        selLabel.textContent = '(selected: ' + p.label + ')';
        render();
    });

    clearBtn.addEventListener('click', function () {
        if (root && confirm('Clear the whole map?')) {
            root = null; selected = null;
            selLabel.textContent = '(no node selected)';
            results.classList.add('d-none');
            render();
        }
    });

    sampleBtn.addEventListener('click', function () {
        hideError();
        root = mkNode('Healthy Life');
        selected = root;
        var branches = [
            ['Food', ['Vegetables', 'Fruit', '8 glasses of water']],
            ['Exercise', ['Walk 30 min', '8 hours of sleep']],
            ['Mind', ['Book', 'Prayer / Meditation']]
        ];
        branches.forEach(function (b, bi) {
            var bn = mkNode(b[0]);
            bn.parent = root;
            bn.color = COLORS[bi % COLORS.length];
            root.children.push(bn);
            b[1].forEach(function (leaf) {
                var ln = mkNode(leaf);
                ln.parent = bn;
                ln.color = bn.color;
                bn.children.push(ln);
            });
        });
        selLabel.textContent = '(selected: Healthy Life)';
        render();
    });

    function download(url, name) {
        var a = document.createElement('a');
        a.href = url;
        a.download = name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    svgBtn.addEventListener('click', function () {
        hideError();
        if (!root) { showError('Please make a mind map first.'); return; }
        var clone = svg.cloneNode(true);
        clone.setAttribute('xmlns', NS);
        clone.style.minWidth = '';
        var blob = new Blob(['<?xml version="1.0" encoding="UTF-8"?>\n' + new XMLSerializer().serializeToString(clone)], { type: 'image/svg+xml' });
        download(URL.createObjectURL(blob), 'mindmap.svg');
    });

    pngBtn.addEventListener('click', function () {
        hideError();
        if (!root) { showError('Please make a mind map first.'); return; }
        var clone = svg.cloneNode(true);
        clone.setAttribute('xmlns', NS);
        clone.style.minWidth = '';
        var str = new XMLSerializer().serializeToString(clone);
        var img = new Image();
        img.onload = function () {
            var c = document.createElement('canvas');
            c.width = svg.getAttribute('width');
            c.height = svg.getAttribute('height');
            var ctx = c.getContext('2d');
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, c.width, c.height);
            ctx.drawImage(img, 0, 0);
            download(c.toDataURL('image/png'), 'mindmap.png');
        };
        img.onerror = function () { showError('There was a problem making the PNG. Try SVG instead.'); };
        img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(str);
    });

    render();
})();
</script>
@endsection
