@extends('layouts.app')

@section('title', 'Zakat Calculator — Azlaan Tools')
@section('meta_description', 'Free Zakat calculator. Enter cash, gold, silver, business goods and liabilities to check nisab and calculate 2.5% Zakat in PKR.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Zakat Calculator</h1>
            <p class="text-muted mb-4">Enter your zakatable assets — the calculator checks the nisab and tells you if Zakat is due, and if so, how much (2.5%).</p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title mb-3">Assets</h2>
                    <div class="mb-3">
                        <label for="cash" class="form-label">Cash in hand / Bank (PKR)</label>
                        <input type="number" class="form-control zakat-input" id="cash" placeholder="0" step="any">
                    </div>

                    <div class="row g-2 mb-1">
                        <div class="col-md-4">
                            <label for="goldQty" class="form-label">Gold quantity</label>
                            <input type="number" class="form-control zakat-input" id="goldQty" placeholder="0" step="any">
                        </div>
                        <div class="col-md-4">
                            <label for="goldUnit" class="form-label">Gold unit</label>
                            <select class="form-select zakat-input" id="goldUnit">
                                <option value="tola" selected>Tola</option>
                                <option value="gram">Grams</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="goldRate" class="form-label">Gold price per tola (PKR)</label>
                            <input type="number" class="form-control zakat-input" id="goldRate" value="0" step="any">
                        </div>
                    </div>
                    <p class="small text-muted">Enter the rate for today yourself — this field is left empty so no wrong or old rate is used.</p>

                    <div class="row g-2 mb-1">
                        <div class="col-md-4">
                            <label for="silverQty" class="form-label">Silver quantity</label>
                            <input type="number" class="form-control zakat-input" id="silverQty" placeholder="0" step="any">
                        </div>
                        <div class="col-md-4">
                            <label for="silverUnit" class="form-label">Silver unit</label>
                            <select class="form-select zakat-input" id="silverUnit">
                                <option value="tola" selected>Tola</option>
                                <option value="gram">Grams</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="silverRate" class="form-label">Silver price per tola (PKR)</label>
                            <input type="number" class="form-control zakat-input" id="silverRate" value="0" step="any">
                        </div>
                    </div>
                    <p class="small text-muted">Also enter the silver rate for today yourself.</p>

                    <div class="mb-3">
                        <label for="goods" class="form-label">Business Goods / Stock Value (PKR)</label>
                        <input type="number" class="form-control zakat-input" id="goods" placeholder="0" step="any">
                        <div class="form-text">Total value of the trade goods kept for sale.</div>
                    </div>
                    <div class="mb-3">
                        <label for="liabilities" class="form-label">Liabilities / Debts Deductible (PKR)</label>
                        <input type="number" class="form-control zakat-input" id="liabilities" placeholder="0" step="any">
                        <div class="form-text">Debt that must be paid right away will be subtracted from the total.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Calculate nisab based on?</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check zakat-input" name="nisabType" id="nisabSilver" checked>
                            <label class="btn btn-outline-primary" for="nisabSilver">Silver Nisab (52.5 tola silver)</label>
                            <input type="radio" class="btn-check zakat-input" name="nisabType" id="nisabGold">
                            <label class="btn btn-outline-primary" for="nisabGold">Gold Nisab (7.5 tola gold)</label>
                        </div>
                        <div class="form-text">Most scholars advise using the silver nisab for cash, because it is better for the poor.</div>
                    </div>

                    <button type="button" class="btn btn-success w-100" id="calcZakatBtn">Calculate Zakat</button>

                    <div class="mt-4 d-none" id="zakatResult">
                        <div class="row g-3 text-center">
                            <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="text-muted small">Total Zakatable Assets</div><div class="fs-5 fw-bold" id="totalAssetsOut">—</div></div></div>
                            <div class="col-md-4"><div class="border rounded p-3 bg-light"><div class="text-muted small">Nisab Threshold</div><div class="fs-5 fw-bold" id="nisabOut">—</div></div></div>
                            <div class="col-md-4"><div class="border rounded p-3 bg-success text-white"><div class="small">Zakat Payable (2.5%)</div><div class="fs-5 fw-bold" id="zakatOut">—</div></div></div>
                        </div>
                        <div class="alert mt-3 mb-0" id="nisabMsg"></div>
                        <ul class="small text-muted mt-3 mb-0" id="breakdown"></ul>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2>How to use</h2>
                    <ol>
                        <li>Enter the values of cash, gold, silver and business goods. For gold and silver, enter the rate for today yourself.</li>
                        <li>Enter the debt you must pay in "Liabilities" — it will be subtracted from the total.</li>
                        <li>Choose the nisab type: silver nisab (52.5 tola silver) or gold nisab (7.5 tola gold).</li>
                        <li>Click "Calculate Zakat". If your zakatable assets are above the nisab, Zakat = 2.5% of the total.</li>
                        <li>Note: Zakat is only due on wealth held for one lunar year. If you have doubts, consult your mufti or scholar.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function pkr(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }
function num(id) { return parseFloat(document.getElementById(id).value) || 0; }
var GRAMS_PER_TOLA = 11.664;

function calcZakat() {
    var cash = num('cash');
    var goldQty = num('goldQty'), goldRate = num('goldRate');
    var goldTola = document.getElementById('goldUnit').value === 'gram' ? goldQty / GRAMS_PER_TOLA : goldQty;
    var goldValue = goldTola * goldRate;
    var silverQty = num('silverQty'), silverRate = num('silverRate');
    var silverTola = document.getElementById('silverUnit').value === 'gram' ? silverQty / GRAMS_PER_TOLA : silverQty;
    var silverValue = silverTola * silverRate;
    var goods = num('goods');
    var liabilities = num('liabilities');
    var total = cash + goldValue + silverValue + goods - liabilities;
    if (total < 0) total = 0;

    var useSilver = document.getElementById('nisabSilver').checked;
    var nisab = useSilver ? 52.5 * silverRate : 7.5 * goldRate;
    var above = nisab > 0 && total >= nisab;
    var zakat = above ? total * 0.025 : 0;

    document.getElementById('totalAssetsOut').textContent = pkr(total);
    document.getElementById('nisabOut').textContent = nisab > 0 ? pkr(nisab) : 'Enter the rate';
    document.getElementById('zakatOut').textContent = pkr(zakat);
    var msg = document.getElementById('nisabMsg');
    if (nisab <= 0) {
        msg.className = 'alert mt-3 mb-0 alert-warning';
        msg.textContent = 'To check the nisab, you must enter the rate for today for gold or silver.';
    } else if (above) {
        msg.className = 'alert mt-3 mb-0 alert-success';
        msg.textContent = 'Your zakatable assets are above the nisab — Zakat is due (2.5%).';
    } else {
        msg.className = 'alert mt-3 mb-0 alert-secondary';
        msg.textContent = 'Your zakatable assets are below the nisab — Zakat is not due by this calculation.';
    }
    document.getElementById('breakdown').innerHTML =
        '<li>Cash: ' + pkr(cash) + '</li>' +
        '<li>Gold value: ' + pkr(goldValue) + '</li>' +
        '<li>Silver value: ' + pkr(silverValue) + '</li>' +
        '<li>Business goods: ' + pkr(goods) + '</li>' +
        '<li>Minus liabilities: ' + pkr(liabilities) + '</li>';
    document.getElementById('zakatResult').classList.remove('d-none');
}
document.getElementById('calcZakatBtn').addEventListener('click', calcZakat);
</script>
@endsection
