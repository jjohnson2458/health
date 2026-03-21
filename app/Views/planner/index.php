<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="mb-4"><i class="bi bi-calendar-check"></i> <?= e(__('planner.title')) ?></h4>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="/planner">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_weight" class="form-label"><?= e(__('planner.current_weight')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="current_weight" name="current_weight"
                                   value="<?= e($currentWeight ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="goal_weight" class="form-label"><?= e(__('planner.goal_weight')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="goal_weight" name="goal_weight"
                                   required placeholder="e.g. 165">
                        </div>
                        <div class="col-md-4">
                            <label for="weekly_goal" class="form-label"><?= e(__('planner.weekly_goal')) ?></label>
                            <select class="form-select" name="weekly_goal" id="weekly_goal" required>
                                <option value="0.5">0.5 lbs/week</option>
                                <option value="1" selected>1.0 lbs/week</option>
                                <option value="1.5">1.5 lbs/week</option>
                                <option value="2">2.0 lbs/week</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-calendar-plus"></i> <?= e(__('planner.create_plan')) ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
