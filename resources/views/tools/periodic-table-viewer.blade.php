@extends('layouts.app')
@section('title', 'Periodic Table Viewer - Interactive Elements Chart | Azlaan Tools')
@section('meta_description', 'Explore the interactive periodic table with all 118 elements — atomic number, symbol, mass and category. Free chemistry study tool for matric and FSC students.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <h1 class="mb-3">Periodic Table Viewer</h1>
            <p class="lead text-muted">Interactive periodic table — all 118 elements. Click any element to see its atomic number, mass and category. Useful for chemistry (matric / FSC) students.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="searchInput" class="form-label fw-semibold">Search element</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="e.g. Oxygen, Fe, 26...">
                        </div>
                        <div class="col-md-6">
                            <label for="catSel" class="form-label fw-semibold">Filter by category</label>
                            <select class="form-select" id="catSel">
                                <option value="">All categories</option>
                            </select>
                        </div>
                    </div>

                    <div id="detailCard" class="alert alert-light border mb-3 d-none">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div id="detailTile" class="rounded d-flex flex-column align-items-center justify-content-center text-dark fw-bold" style="width:84px;height:84px;"></div>
                            <div>
                                <h2 class="h5 mb-1" id="detailName"></h2>
                                <p class="mb-0 small" id="detailInfo"></p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div id="ptable" style="display:grid;grid-template-columns:repeat(18,minmax(44px,1fr));gap:4px;min-width:860px;"></div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap gap-2" id="legend"></div>
                    <div class="alert alert-danger mt-3 d-none" id="errorBox" role="alert"></div>
                </div>
            </div>

            <h2>How to use</h2>
            <ol>
                <li>Click any element tile — details appear above.</li>
                <li>Type a name, symbol or atomic number in the search box to find an element.</li>
                <li>Use the category filter to see only one type of element (for example metals or halogens).</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';
    // [number, symbol, name, atomic mass, category]
    var E = [
        [1,"H","Hydrogen",1.008,"nonmetal"],[2,"He","Helium",4.003,"noble"],
        [3,"Li","Lithium",6.94,"alkali"],[4,"Be","Beryllium",9.012,"alkaline"],
        [5,"B","Boron",10.81,"metalloid"],[6,"C","Carbon",12.011,"nonmetal"],
        [7,"N","Nitrogen",14.007,"nonmetal"],[8,"O","Oxygen",15.999,"nonmetal"],
        [9,"F","Fluorine",18.998,"halogen"],[10,"Ne","Neon",20.18,"noble"],
        [11,"Na","Sodium",22.99,"alkali"],[12,"Mg","Magnesium",24.305,"alkaline"],
        [13,"Al","Aluminium",26.982,"metal"],[14,"Si","Silicon",28.085,"metalloid"],
        [15,"P","Phosphorus",30.974,"nonmetal"],[16,"S","Sulfur",32.06,"nonmetal"],
        [17,"Cl","Chlorine",35.45,"halogen"],[18,"Ar","Argon",39.948,"noble"],
        [19,"K","Potassium",39.098,"alkali"],[20,"Ca","Calcium",40.078,"alkaline"],
        [21,"Sc","Scandium",44.956,"transition"],[22,"Ti","Titanium",47.867,"transition"],
        [23,"V","Vanadium",50.942,"transition"],[24,"Cr","Chromium",51.996,"transition"],
        [25,"Mn","Manganese",54.938,"transition"],[26,"Fe","Iron",55.845,"transition"],
        [27,"Co","Cobalt",58.933,"transition"],[28,"Ni","Nickel",58.693,"transition"],
        [29,"Cu","Copper",63.546,"transition"],[30,"Zn","Zinc",65.38,"transition"],
        [31,"Ga","Gallium",69.723,"metal"],[32,"Ge","Germanium",72.63,"metalloid"],
        [33,"As","Arsenic",74.922,"metalloid"],[34,"Se","Selenium",78.971,"nonmetal"],
        [35,"Br","Bromine",79.904,"halogen"],[36,"Kr","Krypton",83.798,"noble"],
        [37,"Rb","Rubidium",85.468,"alkali"],[38,"Sr","Strontium",87.62,"alkaline"],
        [39,"Y","Yttrium",88.906,"transition"],[40,"Zr","Zirconium",91.224,"transition"],
        [41,"Nb","Niobium",92.906,"transition"],[42,"Mo","Molybdenum",95.95,"transition"],
        [43,"Tc","Technetium",98,"transition"],[44,"Ru","Ruthenium",101.07,"transition"],
        [45,"Rh","Rhodium",102.91,"transition"],[46,"Pd","Palladium",106.42,"transition"],
        [47,"Ag","Silver",107.87,"transition"],[48,"Cd","Cadmium",112.41,"transition"],
        [49,"In","Indium",114.82,"metal"],[50,"Sn","Tin",118.71,"metal"],
        [51,"Sb","Antimony",121.76,"metalloid"],[52,"Te","Tellurium",127.6,"metalloid"],
        [53,"I","Iodine",126.9,"halogen"],[54,"Xe","Xenon",131.29,"noble"],
        [55,"Cs","Caesium",132.91,"alkali"],[56,"Ba","Barium",137.33,"alkaline"],
        [57,"La","Lanthanum",138.91,"lanthanide"],[58,"Ce","Cerium",140.12,"lanthanide"],
        [59,"Pr","Praseodymium",140.91,"lanthanide"],[60,"Nd","Neodymium",144.24,"lanthanide"],
        [61,"Pm","Promethium",145,"lanthanide"],[62,"Sm","Samarium",150.36,"lanthanide"],
        [63,"Eu","Europium",151.96,"lanthanide"],[64,"Gd","Gadolinium",157.25,"lanthanide"],
        [65,"Tb","Terbium",158.93,"lanthanide"],[66,"Dy","Dysprosium",162.5,"lanthanide"],
        [67,"Ho","Holmium",164.93,"lanthanide"],[68,"Er","Erbium",167.26,"lanthanide"],
        [69,"Tm","Thulium",168.93,"lanthanide"],[70,"Yb","Ytterbium",173.05,"lanthanide"],
        [71,"Lu","Lutetium",174.97,"lanthanide"],
        [72,"Hf","Hafnium",178.49,"transition"],[73,"Ta","Tantalum",180.95,"transition"],
        [74,"W","Tungsten",183.84,"transition"],[75,"Re","Rhenium",186.21,"transition"],
        [76,"Os","Osmium",190.23,"transition"],[77,"Ir","Iridium",192.22,"transition"],
        [78,"Pt","Platinum",195.08,"transition"],[79,"Au","Gold",196.97,"transition"],
        [80,"Hg","Mercury",200.59,"transition"],
        [81,"Tl","Thallium",204.38,"metal"],[82,"Pb","Lead",207.2,"metal"],
        [83,"Bi","Bismuth",208.98,"metal"],[84,"Po","Polonium",209,"metal"],
        [85,"At","Astatine",210,"metalloid"],[86,"Rn","Radon",222,"noble"],
        [87,"Fr","Francium",223,"alkali"],[88,"Ra","Radium",226,"alkaline"],
        [89,"Ac","Actinium",227,"actinide"],[90,"Th","Thorium",232.04,"actinide"],
        [91,"Pa","Protactinium",231.04,"actinide"],[92,"U","Uranium",238.03,"actinide"],
        [93,"Np","Neptunium",237,"actinide"],[94,"Pu","Plutonium",244,"actinide"],
        [95,"Am","Americium",243,"actinide"],[96,"Cm","Curium",247,"actinide"],
        [97,"Bk","Berkelium",247,"actinide"],[98,"Cf","Californium",251,"actinide"],
        [99,"Es","Einsteinium",252,"actinide"],[100,"Fm","Fermium",257,"actinide"],
        [101,"Md","Mendelevium",258,"actinide"],[102,"No","Nobelium",259,"actinide"],
        [103,"Lr","Lawrencium",266,"actinide"],
        [104,"Rf","Rutherfordium",267,"transition"],[105,"Db","Dubnium",268,"transition"],
        [106,"Sg","Seaborgium",269,"transition"],[107,"Bh","Bohrium",270,"transition"],
        [108,"Hs","Hassium",277,"transition"],[109,"Mt","Meitnerium",278,"unknown"],
        [110,"Ds","Darmstadtium",281,"unknown"],[111,"Rg","Roentgenium",282,"unknown"],
        [112,"Cn","Copernicium",285,"transition"],[113,"Nh","Nihonium",286,"unknown"],
        [114,"Fl","Flerovium",289,"unknown"],[115,"Mc","Moscovium",290,"unknown"],
        [116,"Lv","Livermorium",293,"unknown"],[117,"Ts","Tennessine",294,"unknown"],
        [118,"Og","Oganesson",294,"unknown"]
    ];

    var catColors = {
        alkali: '#fee2e2', alkaline: '#ffedd5', transition: '#fef3c7',
        metal: '#e0e7ff', metalloid: '#ccfbf1', nonmetal: '#dcfce7',
        halogen: '#fce7f3', noble: '#ede9fe', lanthanide: '#fae8ff',
        actinide: '#ffe4e6', unknown: '#e5e7eb'
    };
    var catNames = {
        alkali: 'Alkali Metal', alkaline: 'Alkaline Earth Metal', transition: 'Transition Metal',
        metal: 'Post-transition Metal', metalloid: 'Metalloid', nonmetal: 'Nonmetal',
        halogen: 'Halogen', noble: 'Noble Gas', lanthanide: 'Lanthanide',
        actinide: 'Actinide', unknown: 'Unknown / Synthetic'
    };

    var ptable = document.getElementById('ptable');
    var searchInput = document.getElementById('searchInput');
    var catSel = document.getElementById('catSel');
    var legend = document.getElementById('legend');
    var detailCard = document.getElementById('detailCard');
    var detailTile = document.getElementById('detailTile');
    var detailName = document.getElementById('detailName');
    var detailInfo = document.getElementById('detailInfo');
    var errorBox = document.getElementById('errorBox');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    function hideError() {
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }

    function pos(n) {
        if (n >= 57 && n <= 71) return { r: 9, c: n - 57 + 4 };
        if (n >= 89 && n <= 103) return { r: 10, c: n - 89 + 4 };
        if (n === 1) return { r: 1, c: 1 };
        if (n === 2) return { r: 1, c: 18 };
        if (n >= 3 && n <= 4) return { r: 2, c: n - 2 };
        if (n >= 5 && n <= 10) return { r: 2, c: n + 8 };
        if (n >= 11 && n <= 12) return { r: 3, c: n - 10 };
        if (n >= 13 && n <= 18) return { r: 3, c: n };
        if (n >= 19 && n <= 36) return { r: 4, c: n - 18 };
        if (n >= 37 && n <= 54) return { r: 5, c: n - 36 };
        if (n === 55 || n === 56) return { r: 6, c: n - 54 };
        if (n >= 72 && n <= 86) return { r: 6, c: n - 68 };
        if (n === 87 || n === 88) return { r: 7, c: n - 86 };
        if (n >= 104 && n <= 118) return { r: 7, c: n - 100 };
        return { r: 1, c: 1 };
    }

    // f-block placeholders at (6,3) and (7,3)
    function placeholder(r, label) {
        var d = document.createElement('div');
        d.style.cssText = 'grid-row:' + r + ';grid-column:3;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;border-radius:6px;min-height:56px;';
        d.textContent = label;
        return d;
    }
    ptable.appendChild(placeholder(6, '57–71'));
    ptable.appendChild(placeholder(7, '89–103'));

    var tiles = [];
    E.forEach(function (e) {
        var p = pos(e[0]);
        var d = document.createElement('button');
        d.type = 'button';
        d.className = 'border-0 rounded p-1 text-dark';
        d.style.cssText = 'grid-row:' + p.r + ';grid-column:' + p.c + ';background:' + catColors[e[4]] + ';min-height:56px;cursor:pointer;';
        d.setAttribute('data-num', e[0]);
        d.setAttribute('data-cat', e[4]);
        d.innerHTML = '<div style="font-size:10px;line-height:1;">' + e[0] + '</div>' +
            '<div style="font-size:15px;font-weight:700;line-height:1.2;">' + e[1] + '</div>' +
            '<div style="font-size:9px;line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + e[2] + '</div>';
        d.addEventListener('click', function () { showDetail(e, p); });
        ptable.appendChild(d);
        tiles.push(d);
    });

    function showDetail(e, p) {
        var period = e[4] === 'lanthanide' ? 6 : (e[4] === 'actinide' ? 7 : p.r);
        detailTile.style.background = catColors[e[4]];
        detailTile.innerHTML = '<div style="font-size:11px;">' + e[0] + '</div><div style="font-size:22px;">' + e[1] + '</div>';
        detailName.textContent = e[2] + ' (' + e[1] + ')';
        detailInfo.textContent = 'Atomic number: ' + e[0] + ' • Atomic mass: ' + e[3] +
            ' • Category: ' + catNames[e[4]] + ' • Period: ' + period + ' • Group: ' + p.c;
        detailCard.classList.remove('d-none');
    }

    // category filter options
    Object.keys(catNames).forEach(function (k) {
        var o = document.createElement('option');
        o.value = k;
        o.textContent = catNames[k];
        catSel.appendChild(o);
    });

    // legend
    Object.keys(catNames).forEach(function (k) {
        var s = document.createElement('span');
        s.className = 'badge border text-dark fw-normal';
        s.style.background = catColors[k];
        s.textContent = catNames[k];
        s.style.cursor = 'pointer';
        s.addEventListener('click', function () {
            catSel.value = k;
            applyFilter();
        });
        legend.appendChild(s);
    });

    function applyFilter() {
        hideError();
        var q = searchInput.value.trim().toLowerCase();
        var cat = catSel.value;
        var visible = 0;
        for (var i = 0; i < tiles.length; i++) {
            var t = tiles[i];
            var e = E[i];
            var matchQ = !q ||
                e[2].toLowerCase().indexOf(q) !== -1 ||
                e[1].toLowerCase() === q ||
                String(e[0]) === q;
            var matchC = !cat || t.getAttribute('data-cat') === cat;
            var show = matchQ && matchC;
            t.style.display = show ? '' : 'none';
            if (show) visible++;
        }
        if (visible === 0) showError('No element found. Try a different search.');
    }

    searchInput.addEventListener('input', applyFilter);
    catSel.addEventListener('change', applyFilter);
})();
</script>
@endsection
