@extends('layouts.app')

@section('title', 'Moving Box Estimator — Free Online Tool')
@section('meta_description', 'Estimate moving boxes and packing material needed by home size and rooms.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3">Moving Box Estimator</h1>
                    <p class="lead small text-muted">Count your bedrooms, living areas, kitchen and bathrooms and get small, medium and large box counts plus tape and packing paper estimates for your move.</p>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="mbBed">Bedrooms</label><input type="number" class="form-control" id="mbBed" value="3" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="mbLiving">Living / dining rooms</label><input type="number" class="form-control" id="mbLiving" value="2" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="mbKitchen">Kitchens</label><input type="number" class="form-control" id="mbKitchen" value="1" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="mbBath">Bathrooms</label><input type="number" class="form-control" id="mbBath" value="2" min="0" step="1"></div>
                        <div class="col-md-3"><label class="form-label" for="mbStuff">Belongings level</label><select class="form-select" id="mbStuff"><option value="0.75">Minimal</option><option value="1" selected>Average</option><option value="1.35">A lot / hoarder level</option></select></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" id="mbOut">Enter your rooms to estimate boxes.</div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">How to use</h2>
                    <ol>
                        <li>Count the rooms in the home you are leaving.</li>
                        <li>Choose how full the home is — minimal, average or a lot of belongings.</li>
                        <li>Buy the box counts and packing supplies shown, plus a few spares.</li>
                    </ol>
                    <p class="small text-muted mb-0">Estimates use common mover guidelines per room (bedroom: 4 small, 6 medium, 3 large; living room: 3, 5, 3; kitchen: 4, 5, 2; bathroom: 2 small, 1 medium), scaled by belongings level and rounded up. Books belong in small boxes so they stay liftable. Wardrobe boxes for hanging clothes are extra.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    "use strict";
    function el(id) { return document.getElementById(id); }
    function iv(id) { var v = parseInt(el(id).value, 10); return isNaN(v) || v < 0 ? 0 : v; }
    function calc() {
        var bed = iv("mbBed"), liv = iv("mbLiving"), kit = iv("mbKitchen"), bath = iv("mbBath");
        var f = parseFloat(el("mbStuff").value);
        var out = el("mbOut");
        var rooms = bed + liv + kit + bath;
        if (rooms <= 0) { out.textContent = "Please enter at least one room."; return; }
        var small = Math.ceil((bed * 4 + liv * 3 + kit * 4 + bath * 2) * f);
        var medium = Math.ceil((bed * 6 + liv * 5 + kit * 5 + bath * 1) * f);
        var large = Math.ceil((bed * 3 + liv * 3 + kit * 2) * f);
        var total = small + medium + large;
        var tape = Math.max(2, Math.ceil(total / 12));
        var paper = Math.max(1, Math.ceil(total / 15));
        out.innerHTML = "<strong>Boxes needed: " + total + " total</strong> — Small: " + small + ", Medium: " + medium + ", Large: " + large + "<br><strong>Tape rolls:</strong> about " + tape + " &nbsp; <strong>Packing paper bundles:</strong> about " + paper + " &nbsp; Add 1 to 2 rolls of bubble wrap for fragile items.";
    }
    ["mbBed", "mbLiving", "mbKitchen", "mbBath", "mbStuff"].forEach(function (id) { el(id).addEventListener("input", calc); el(id).addEventListener("change", calc); });
    calc();
})();
</script>
@endsection
