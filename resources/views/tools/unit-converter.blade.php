@extends('layouts.app')

@section('title', 'Unit Converter - Length, Weight, Area Marla Kanal | Azlaan Tools')
@section('meta_description', 'Free online unit converter for Pakistan and worldwide: length, weight, temperature, area including Marla and Kanal, and volume. Instant live conversion, no signup.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Unit Converter</h1>
            <p class="lead text-muted">Convert length, weight, temperature, area (including Pakistani Marla and Kanal) and volume instantly — just type and the result updates live.</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">Category</label>
                        <select class="form-select" id="category">
                            <option value="length" selected>Length</option>
                            <option value="weight">Weight</option>
                            <option value="temperature">Temperature</option>
                            <option value="area">Area (Marla / Kanal included)</option>
                            <option value="volume">Volume</option>
                        </select>
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="fromUnit" class="form-label fw-semibold">From</label>
                            <select class="form-select" id="fromUnit"></select>
                            <input type="number" class="form-control mt-2" id="inputValue" value="1" step="any" placeholder="Enter value">
                        </div>
                        <div class="col-md-2 text-center">
                            <button type="button" class="btn btn-outline-primary w-100" id="swapBtn" title="Swap units">&#8646; Swap</button>
                        </div>
                        <div class="col-md-5">
                            <label for="toUnit" class="form-label fw-semibold">To</label>
                            <select class="form-select" id="toUnit"></select>
                            <input type="text" class="form-control mt-2" id="outputValue" readonly placeholder="Result">
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="resultText">Result will appear here.</div>
                    <p class="small text-muted mt-2 mb-0">Property note: 1 Marla = 272.25 sq ft, 1 Kanal = 20 Marla = 5,445 sq ft.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Select a category: Length, Weight, Temperature, Area or Volume.</li>
                        <li>Choose the unit you have in the <strong>From</strong> list and the unit you want in the <strong>To</strong> list.</li>
                        <li>Type your value — the conversion updates live as you type.</li>
                        <li>Use the <strong>Swap</strong> button to quickly reverse the conversion.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    // Factors are relative to a base unit per category.
    var data = {
        length: { base: 'metre', units: {
            mm: { label: 'Millimetre (mm)', factor: 0.001 },
            cm: { label: 'Centimetre (cm)', factor: 0.01 },
            m: { label: 'Metre (m)', factor: 1 },
            km: { label: 'Kilometre (km)', factor: 1000 },
            inch: { label: 'Inch', factor: 0.0254 },
            foot: { label: 'Foot (ft)', factor: 0.3048 },
            yard: { label: 'Yard', factor: 0.9144 },
            mile: { label: 'Mile', factor: 1609.344 }
        }},
        weight: { base: 'gram', units: {
            g: { label: 'Gram (g)', factor: 1 },
            kg: { label: 'Kilogram (kg)', factor: 1000 },
            ton: { label: 'Ton (metric)', factor: 1000000 },
            pound: { label: 'Pound (lb)', factor: 453.59237 },
            ounce: { label: 'Ounce (oz)', factor: 28.349523125 }
        }},
        temperature: { base: 'celsius', units: {
            c: { label: 'Celsius (°C)', factor: null },
            f: { label: 'Fahrenheit (°F)', factor: null },
            k: { label: 'Kelvin (K)', factor: null }
        }},
        area: { base: 'sq ft', units: {
            sqft: { label: 'Square Feet (sq ft)', factor: 1 },
            sqm: { label: 'Square Metre (sq m)', factor: 10.7639104 },
            marla: { label: 'Marla', factor: 272.25 },
            kanal: { label: 'Kanal (20 Marla)', factor: 5445 }
        }},
        volume: { base: 'litre', units: {
            litre: { label: 'Litre (L)', factor: 1 },
            ml: { label: 'Millilitre (ml)', factor: 0.001 },
            gallon: { label: 'Gallon (US)', factor: 3.785411784 }
        }}
    };

    var categoryEl = document.getElementById('category');
    var fromEl = document.getElementById('fromUnit');
    var toEl = document.getElementById('toUnit');
    var inputEl = document.getElementById('inputValue');
    var outputEl = document.getElementById('outputValue');
    var resultText = document.getElementById('resultText');

    function formatNum(n) {
        if (!isFinite(n)) return '';
        if (n !== 0 && (Math.abs(n) >= 1e12 || Math.abs(n) < 1e-6)) return n.toExponential(6);
        return parseFloat(n.toFixed(8)).toLocaleString('en-US', { maximumFractionDigits: 8 });
    }

    function toCelsius(v, from) {
        if (from === 'c') return v;
        if (from === 'f') return (v - 32) * 5 / 9;
        return v - 273.15; // kelvin
    }
    function fromCelsius(v, to) {
        if (to === 'c') return v;
        if (to === 'f') return v * 9 / 5 + 32;
        return v + 273.15; // kelvin
    }

    function convert() {
        var cat = categoryEl.value;
        var from = fromEl.value, to = toEl.value;
        var val = parseFloat(inputEl.value);
        if (isNaN(val) || !from || !to) {
            outputEl.value = '';
            resultText.textContent = 'Please enter a value to convert.';
            return;
        }
        var result;
        if (cat === 'temperature') {
            result = fromCelsius(toCelsius(val, from), to);
        } else {
            var units = data[cat].units;
            result = val * units[from].factor / units[to].factor;
        }
        outputEl.value = formatNum(result);
        resultText.textContent = formatNum(val) + ' ' + data[cat].units[from].label + ' = ' + formatNum(result) + ' ' + data[cat].units[to].label;
    }

    function fillUnits() {
        var units = data[categoryEl.value].units;
        var keys = Object.keys(units);
        fromEl.innerHTML = ''; toEl.innerHTML = '';
        keys.forEach(function (key) {
            var o1 = document.createElement('option'); o1.value = key; o1.textContent = units[key].label; fromEl.appendChild(o1);
            var o2 = document.createElement('option'); o2.value = key; o2.textContent = units[key].label; toEl.appendChild(o2);
        });
        fromEl.value = keys[0];
        toEl.value = keys.length > 2 ? keys[2] : keys[1] || keys[0];
        convert();
    }

    categoryEl.addEventListener('change', fillUnits);
    fromEl.addEventListener('change', convert);
    toEl.addEventListener('change', convert);
    inputEl.addEventListener('input', convert);
    document.getElementById('swapBtn').addEventListener('click', function () {
        var tmp = fromEl.value; fromEl.value = toEl.value; toEl.value = tmp; convert();
    });

    fillUnits();
})();
</script>
@endsection
