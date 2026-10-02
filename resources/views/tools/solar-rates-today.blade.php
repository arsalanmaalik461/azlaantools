@extends('layouts.app')

@section('title', 'Solar Rates Today in Pakistan — Panel, Inverter & Battery Prices | Azlaan Tools')
@section('meta_description', 'Solar rates today in Pakistan — daily solar panel per-watt rates, hybrid inverter prices and lithium / tubular battery prices. Last updated: 2026-10-01. Free, no signup.')

@section('content')
@php
    $ratesPath = resource_path('data/solar-rates.json');
    $rates = file_exists($ratesPath) ? json_decode(file_get_contents($ratesPath), true) : null;
@endphp
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            @if(!$rates)
                <div class="alert alert-warning" role="alert">
                    Rates are being updated — please check back shortly
                </div>
            @else
                <div class="text-center mb-4">
                    <h1 class="mb-3">Solar Rates Today — Pakistan</h1>
                    <p class="lead">Today's solar panel, inverter and battery rates — compiled from Pakistan market's daily listings.</p>
                    <span class="badge bg-success fs-6 px-3 py-2">Last updated: {{ $rates['updated'] }}</span>
                </div>

                <div class="alert alert-info" role="alert">
                    {{ $rates['market_note'] }}
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <label for="rateSearch" class="form-label fw-bold">Search rates</label>
                        <input type="text" class="form-control form-control-lg" id="rateSearch" placeholder="Search brand or model — e.g. Longi, Inverex, Nitrox, Lithium...">
                        <div class="form-text">All three tables filter live as you type.</div>
                    </div>
                </div>

                <h2 class="mt-4 mb-3">Solar Panel Rates Per Watt</h2>
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0 rate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Size</th>
                                        <th>Rate / Watt</th>
                                        <th>Approx. Plate Price</th>
                                        <th>Trend</th>
                                        <th>Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rates['panels'] as $panel)
                                        <tr>
                                            <td>{{ $panel['brand'] }}</td>
                                            <td>{{ $panel['model'] }}</td>
                                            <td>{{ $panel['watts'] }}W</td>
                                            <td>
                                                @if($panel['rate_per_watt_min'] == $panel['rate_per_watt_max'])
                                                    Rs {{ rtrim(rtrim(number_format($panel['rate_per_watt_min'], 2), '0'), '.') }}
                                                @else
                                                    Rs {{ rtrim(rtrim(number_format($panel['rate_per_watt_min'], 2), '0'), '.') }} – {{ rtrim(rtrim(number_format($panel['rate_per_watt_max'], 2), '0'), '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                @php $plateMin = $panel['rate_per_watt_min'] * $panel['watts']; $plateMax = $panel['rate_per_watt_max'] * $panel['watts']; @endphp
                                                @if($plateMin == $plateMax)
                                                    ≈ Rs {{ number_format($plateMin) }}
                                                @else
                                                    ≈ Rs {{ number_format($plateMin) }} – {{ number_format($plateMax) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(($panel['trend'] ?? 'same') === 'up')
                                                    <span class="text-danger">▲</span>
                                                @elseif(($panel['trend'] ?? 'same') === 'down')
                                                    <span class="text-success">▼</span>
                                                @else
                                                    <span class="text-muted">→</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $panel['source'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h2 class="mt-4 mb-3">Solar Inverter Prices</h2>
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0 rate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Trend</th>
                                        <th>Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rates['inverters'] as $inverter)
                                        <tr>
                                            <td>{{ $inverter['brand'] }}</td>
                                            <td>{{ $inverter['model'] }}</td>
                                            <td>{{ $inverter['size'] }}</td>
                                            <td>{{ $inverter['type'] }}</td>
                                            <td>
                                                @if($inverter['price_min'] == $inverter['price_max'])
                                                    Rs {{ number_format($inverter['price_min']) }}
                                                @else
                                                    Rs {{ number_format($inverter['price_min']) }} – {{ number_format($inverter['price_max']) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(($inverter['trend'] ?? 'same') === 'up')
                                                    <span class="text-danger">▲</span>
                                                @elseif(($inverter['trend'] ?? 'same') === 'down')
                                                    <span class="text-success">▼</span>
                                                @else
                                                    <span class="text-muted">→</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $inverter['source'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h2 class="mt-4 mb-3">Solar Battery Prices</h2>
                @foreach(['Lithium', 'Tubular', 'Dry / AGM'] as $group)
                    <h3 class="h5 mt-4 mb-2">{{ $group }}</h3>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0 rate-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Capacity</th>
                                            <th>Price</th>
                                            <th>Trend</th>
                                            <th>Source</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rates['batteries'] as $battery)
                                            @if($battery['group'] === $group)
                                                <tr>
                                                    <td>{{ $battery['brand'] }}</td>
                                                    <td>{{ $battery['model'] }}</td>
                                                    <td>{{ $battery['capacity'] }}</td>
                                                    <td>
                                                        @if($battery['price_min'] == $battery['price_max'])
                                                            Rs {{ number_format($battery['price_min']) }}
                                                        @else
                                                            Rs {{ number_format($battery['price_min']) }} – {{ number_format($battery['price_max']) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(($battery['trend'] ?? 'same') === 'up')
                                                            <span class="text-danger">▲</span>
                                                        @elseif(($battery['trend'] ?? 'same') === 'down')
                                                            <span class="text-success">▼</span>
                                                        @else
                                                            <span class="text-muted">→</span>
                                                        @endif
                                                    </td>
                                                    <td class="small">{{ $battery['source'] }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Sources &amp; Disclaimer</strong>
                    </div>
                    <div class="card-body">
                        <h3 class="h6">Sources</h3>
                        <ul class="small">
                            @foreach($rates['sources'] as $source)
                                <li>{{ $source }}</li>
                            @endforeach
                        </ul>
                        <p class="small mb-0">Rates are compiled from public listings on the sources above (researched 2026-10-01) for guidance only. Actual prices vary by city, dealer, warranty and stock. Azlaan Tools does not sell at these listed rates — contact us for a proper quotation.</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 border-success">
                    <div class="card-body text-center">
                        <h2 class="h4 mb-3">Want a full solar system estimate?</h2>
                        <p class="text-muted">To find the estimated price of a full system including panels, inverter, battery, structure and installation, use the estimator, or get a free quotation on WhatsApp.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="{{ route('tools.solar-price-estimator') }}" class="btn btn-success">Solar Price Estimator</a>
                            <a href="https://wa.me/923008987448" target="_blank" rel="noopener" class="btn btn-outline-success">WhatsApp — Azlaan Electric AC Solar Center, Faisalabad: 0300-8987448</a>
                        </div>
                    </div>
                </div>

                <h2>How to use</h2>
                <ol>
                    <li>Type a brand or model in the search box above to check rates — all three tables filter live.</li>
                    <li>Compare panel rates per watt and check the approx. plate price to plan your panel budget.</li>
                    <li>Compare inverter and battery prices for your system size (Hybrid, On-Grid, Lithium or Tubular).</li>
                    <li>Use the Solar Price Estimator to calculate the full system cost.</li>
                    <li>For an exact quotation and free survey, contact Azlaan Electric AC Solar Center on WhatsApp.</li>
                </ol>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var searchInput = document.getElementById('rateSearch');
    if (!searchInput) { return; }
    searchInput.addEventListener('input', function () {
        var query = searchInput.value.toLowerCase().trim();
        var rows = document.querySelectorAll('.rate-table tbody tr');
        rows.forEach(function (row) {
            var text = row.textContent.toLowerCase();
            if (!query || text.indexOf(query) !== -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
})();
</script>
@endsection
