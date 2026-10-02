@extends('layouts.app')

@section('title', 'Complex Number Calculator — Free Online Tool')
@section('meta_description', 'Add, subtract, multiply and divide complex numbers, and find the modulus')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3">Complex Number Calculator</h1>
            <p class="lead small text-muted">Add, subtract, multiply and divide complex numbers, and find the modulus</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
<div class="row g-3"><div class="col-md-4"><label for="a" class="form-label">z1 real part a</label><input type="number" class="form-control" id="a" value="3" step="any"></div><div class="col-md-4"><label for="b" class="form-label">z1 imaginary part b</label><input type="number" class="form-control" id="b" value="2" step="any"></div><div class="col-md-4"><label for="c" class="form-label">z2 real part c</label><input type="number" class="form-control" id="c" value="1" step="any"></div><div class="col-md-4"><label for="d" class="form-label">z2 imaginary part d</label><input type="number" class="form-control" id="d" value="-1" step="any"></div></div>
                    <div id="res" class="alert alert-secondary mt-3 mb-0">—</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the parts of z1 = a + bi and z2 = c + di.</li>
                        <li>For a negative imaginary part, enter a minus value.</li>
                        <li>Add, subtract, multiply, divide, modulus and argument will all be shown.</li>
                    </ol>
                    <p class="small text-muted mb-0"><strong>Note:</strong> In multiplication i^2 = -1 applies: (a+bi)(c+di) = (ac - bd) + (ad + bc)i. For division, the denominator is rationalized with the conjugate.</p>
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
    function $(id) { return document.getElementById(id); }
    function v(id) { var n = parseFloat($(id).value); return isFinite(n) ? n : 0; }
    function fmt(n, d) { if (!isFinite(n)) { return "—"; } if (d === undefined) { d = 2; } return n.toLocaleString("en-US", { maximumFractionDigits: d }); }
    function fmtd(n, d) { if (!isFinite(n)) { return "—"; } return n.toLocaleString("en-US", { maximumFractionDigits: d, minimumFractionDigits: d }); }
    function table(rowsArr) { var h = "<table class=\"table table-sm align-middle mb-0\"><tbody>"; rowsArr.forEach(function (r) { h += "<tr><td>" + r[0] + "</td><td class=\"text-end fw-semibold\">" + r[1] + "</td></tr>"; }); return h + "</tbody></table>"; }
    function bad(msg) { $("res").innerHTML = "<span class=\"text-danger fw-semibold\">" + msg + "</span>"; }
    function parseList(id) { return $(id).value.split(/[\s,;]+/).map(function (x) { return parseFloat(x); }).filter(function (x) { return isFinite(x); }); }
    function bind(ids, fn) { ids.forEach(function (id) { var el = $(id); if (el) { el.addEventListener("input", fn); el.addEventListener("change", fn); } }); }

    function fc(re, im) {
        if (Math.abs(im) < 1e-12) { return fmt(re); }
        if (Math.abs(re) < 1e-12) { return fmt(im) + "i"; }
        return fmt(re) + (im < 0 ? " - " : " + ") + fmt(Math.abs(im)) + "i";
    }
    function calc() {
        var a = v("a"), b = v("b"), c = v("c"), d = v("d");
        var den = c * c + d * d;
        var divTxt = den === 0 ? "Undefined — z2 is zero" : fc((a * c + b * d) / den, (b * c - a * d) / den);
        $("res").innerHTML = table([
            ["z1", fc(a, b)], ["z2", fc(c, d)],
            ["z1 + z2", fc(a + c, b + d)],
            ["z1 - z2", fc(a - c, b - d)],
            ["z1 x z2", fc(a * c - b * d, a * d + b * c)],
            ["z1 / z2", divTxt],
            ["Conjugate of z1", fc(a, -b)],
            ["Modulus |z1|", fmt(Math.sqrt(a * a + b * b))],
            ["Modulus |z2|", fmt(Math.sqrt(c * c + d * d))],
            ["Argument of z1 (degrees)", fmt(Math.atan2(b, a) * 180 / Math.PI)],
            ["z1 in polar form", fmt(Math.sqrt(a * a + b * b)) + " (cos " + fmt(Math.atan2(b, a) * 180 / Math.PI) + " deg + i sin " + fmt(Math.atan2(b, a) * 180 / Math.PI) + " deg)"]
        ]);
    }
  
    bind(["a", "b", "c", "d"], calc);
    calc();
})();
</script>
@endsection
