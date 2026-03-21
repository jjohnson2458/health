<div class="row justify-content-center">
    <div class="col-md-10">
        <h4 class="mb-4"><i class="bi bi-basket"></i> <?= e(__('nav.food_tracker')) ?></h4>

        <!-- Food Selector -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col">
                        <select class="form-select" id="foodSelect">
                            <option value="">-- Select a food --</option>
                            <optgroup label="Green — Low Carb / High Protein">
                                <?php foreach ($foods as $i => $f): ?>
                                    <?php if ($f['category'] === 'Green'): ?>
                                        <option value="<?= $i ?>"><?= e($f['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Yellow — Moderate Carb / Balanced">
                                <?php foreach ($foods as $i => $f): ?>
                                    <?php if ($f['category'] === 'Yellow'): ?>
                                        <option value="<?= $i ?>"><?= e($f['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Red — High Carb / Refined">
                                <?php foreach ($foods as $i => $f): ?>
                                    <?php if ($f['category'] === 'Red'): ?>
                                        <option value="<?= $i ?>"><?= e($f['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-auto" style="width:80px;">
                        <input type="number" class="form-control text-center" id="foodQty" value="1" min="1" max="20">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-dark" id="addFoodBtn">+ Add</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="text-muted small">Total Calories</div>
                        <div class="fs-3 fw-bold" id="totalCalories">0</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="text-muted small">Total Carbs (g)</div>
                        <div class="fs-3 fw-bold" id="totalCarbs">0</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="text-muted small">Items Logged</div>
                        <div class="fs-3 fw-bold" id="totalItems">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Food Log Table -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="foodLogTable">
                    <thead>
                        <tr>
                            <th>Food Item</th>
                            <th class="text-center" style="width:60px;">Qty</th>
                            <th class="text-center" style="width:90px;">Category</th>
                            <th class="text-center" style="width:90px;">Calories</th>
                            <th class="text-center" style="width:90px;">Carbs (g)</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="foodLogBody">
                        <!-- Dynamic rows -->
                    </tbody>
                </table>
                <div class="text-center py-4 text-muted" id="emptyMsg">
                    Select a food item, enter a quantity, and hit <strong>+ Add</strong> to log it.
                    Each entry automatically shows the color category (Green/Yellow/Red based on carb/nutrition profile)
                    along with calories and carbs. Totals update at the top as you add items.
                </div>
            </div>
        </div>

        <!-- Category Legend -->
        <div class="card mt-3">
            <div class="card-body py-3">
                <div class="row text-center small">
                    <div class="col-4">
                        <span class="badge bg-success">Green</span>
                        <div class="text-muted mt-1">Low carb / high protein<br>Vegetables, lean meats, eggs</div>
                    </div>
                    <div class="col-4">
                        <span class="badge bg-warning text-dark">Yellow</span>
                        <div class="text-muted mt-1">Moderate carb / balanced<br>Whole grains, fruits, legumes</div>
                    </div>
                    <div class="col-4">
                        <span class="badge bg-danger">Red</span>
                        <div class="text-muted mt-1">High carb / refined<br>White bread, pasta, sweets</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $scripts = '<script>
const foods = ' . json_encode($foods) . ';
let loggedItems = [];

function updateTotals() {
    let totalCal = 0, totalCarbs = 0;
    loggedItems.forEach(item => {
        totalCal += item.calories * item.qty;
        totalCarbs += item.carbs * item.qty;
    });
    document.getElementById("totalCalories").textContent = totalCal;
    document.getElementById("totalCarbs").textContent = Math.round(totalCarbs * 10) / 10;
    document.getElementById("totalItems").textContent = loggedItems.length;
}

function getCategoryBadge(cat) {
    if (cat === "Green") return "<span class=\"badge bg-success\">Green</span>";
    if (cat === "Yellow") return "<span class=\"badge bg-warning text-dark\">Yellow</span>";
    return "<span class=\"badge bg-danger\">Red</span>";
}

function renderTable() {
    const tbody = document.getElementById("foodLogBody");
    const emptyMsg = document.getElementById("emptyMsg");

    if (loggedItems.length === 0) {
        tbody.innerHTML = "";
        emptyMsg.style.display = "";
        return;
    }

    emptyMsg.style.display = "none";
    tbody.innerHTML = loggedItems.map((item, i) =>
        "<tr>" +
        "<td>" + item.name + "</td>" +
        "<td class=\"text-center\">" + item.qty + "</td>" +
        "<td class=\"text-center\">" + getCategoryBadge(item.category) + "</td>" +
        "<td class=\"text-center\">" + (item.calories * item.qty) + "</td>" +
        "<td class=\"text-center\">" + Math.round(item.carbs * item.qty * 10) / 10 + "</td>" +
        "<td class=\"text-center\"><button class=\"btn btn-sm btn-outline-secondary border-0\" onclick=\"removeItem(" + i + ")\">×</button></td>" +
        "</tr>"
    ).join("");
}

function removeItem(index) {
    loggedItems.splice(index, 1);
    renderTable();
    updateTotals();
}

document.getElementById("addFoodBtn").addEventListener("click", function() {
    const select = document.getElementById("foodSelect");
    const qtyInput = document.getElementById("foodQty");
    const idx = select.value;
    const qty = parseInt(qtyInput.value) || 1;

    if (idx === "") return;

    const food = foods[idx];
    loggedItems.push({
        name: food.name,
        calories: food.calories,
        carbs: food.carbs,
        category: food.category,
        qty: qty
    });

    renderTable();
    updateTotals();
    select.value = "";
    qtyInput.value = 1;
});
</script>'; ?>
